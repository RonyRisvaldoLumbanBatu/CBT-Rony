//! Endpoint untuk mahasiswa: alur inti mengerjakan ujian (Fase 1, PG).
//!
//! Prinsip kunci:
//! - Timer OTORITATIF server: `deadline_efektif` dihitung & disimpan saat mulai,
//!   tidak pernah percaya waktu browser.
//! - Satu attempt per (ujian, mahasiswa): refresh/resume melanjutkan yang sama.
//! - Kunci jawaban TIDAK PERNAH dikirim ke klien saat ujian berlangsung.
//! - Auto-grade PG saat submit (atau saat akses setelah waktu habis).

use crate::{
    auth::AuthUser,
    error::{AppError, AppResult},
    services::grading::{nilai_pg, Opsi, PgContent},
    state::AppState,
};
use axum::{
    extract::{Path, State},
    Json,
};
use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use std::collections::HashMap;
use uuid::Uuid;

// ---------- Daftar ujian tersedia ----------

#[derive(Debug, Serialize, sqlx::FromRow)]
pub struct AvailableExam {
    pub id: Uuid,
    pub judul: String,
    pub mata_kuliah: String,
    pub durasi_menit: i32,
    pub waktu_mulai: DateTime<Utc>,
    pub waktu_selesai: DateTime<Utc>,
    pub sudah_selesai: bool,
}

pub async fn list_available(
    State(st): State<AppState>,
    auth: AuthUser,
) -> AppResult<Json<Vec<AvailableExam>>> {
    auth.require_role(&["mahasiswa"])?;

    let exams: Vec<AvailableExam> = sqlx::query_as(
        "SELECT DISTINCT e.id, e.judul, mk.nama AS mata_kuliah, e.durasi_menit,
                e.waktu_mulai, e.waktu_selesai,
                EXISTS(SELECT 1 FROM exam_attempts ea
                       WHERE ea.exam_id = e.id AND ea.mahasiswa_id = $1
                         AND ea.status IN ('submitted','dinilai','expired')) AS sudah_selesai
         FROM exams e
         JOIN exam_kelas ek ON ek.exam_id = e.id
         JOIN enrollment en ON en.kelas_id = ek.kelas_id
                            AND en.mahasiswa_id = $1 AND en.status = 'aktif'
         JOIN mata_kuliah mk ON mk.id = e.mata_kuliah_id
         WHERE e.status IN ('dijadwalkan','berlangsung')
           AND now() BETWEEN e.waktu_mulai AND e.waktu_selesai
         ORDER BY e.waktu_selesai",
    )
    .bind(auth.user_id)
    .fetch_all(&st.db)
    .await?;

    Ok(Json(exams))
}

// ---------- State attempt yang dikirim ke klien ----------

#[derive(Debug, Serialize)]
pub struct SoalOut {
    pub nomor: usize,
    pub question_id: Uuid,
    pub points: f64,
    pub question_text: String,
    pub opsi: Vec<Opsi>, // TANPA kunci
}

#[derive(Debug, Serialize)]
pub struct AttemptState {
    pub attempt_id: Uuid,
    pub exam_judul: String,
    pub status: String,
    pub time_left_seconds: i64,
    pub soal: Vec<SoalOut>,
    /// question_id -> pilihan yang tersimpan
    pub answers: HashMap<Uuid, String>,
}

// Baris ringkas attempt + info ujian terkait.
#[derive(sqlx::FromRow)]
struct AttemptRow {
    exam_id: Uuid,
    deadline_efektif: Option<DateTime<Utc>>,
    status: String,
    soal_order: Option<serde_json::Value>,
    total_score: Option<f64>,
    judul: String,
    tampilkan_hasil: bool,
}

async fn load_attempt(
    st: &AppState,
    attempt_id: Uuid,
    mahasiswa_id: Uuid,
) -> AppResult<AttemptRow> {
    sqlx::query_as::<_, AttemptRow>(
        "SELECT ea.exam_id, ea.deadline_efektif, ea.status, ea.soal_order,
                ea.total_score::float8 AS total_score, e.judul, e.tampilkan_hasil
         FROM exam_attempts ea
         JOIN exams e ON e.id = ea.exam_id
         WHERE ea.id = $1 AND ea.mahasiswa_id = $2",
    )
    .bind(attempt_id)
    .bind(mahasiswa_id)
    .fetch_optional(st.db_ref())
    .await?
    .ok_or_else(|| AppError::NotFound("Attempt tidak ditemukan".into()))
}

fn time_left(deadline: Option<DateTime<Utc>>) -> i64 {
    match deadline {
        Some(d) => (d - Utc::now()).num_seconds().max(0),
        None => 0,
    }
}

/// Rakit state lengkap (soal tanpa kunci + jawaban tersimpan) untuk sebuah attempt.
async fn build_state(st: &AppState, attempt_id: Uuid, row: &AttemptRow) -> AppResult<AttemptState> {
    let order: Vec<Uuid> = match &row.soal_order {
        Some(v) => serde_json::from_value(v.clone()).unwrap_or_default(),
        None => Vec::new(),
    };

    // Ambil semua soal ujian sekali, lalu urutkan sesuai soal_order attempt.
    let rows: Vec<(Uuid, f64, String, serde_json::Value)> = sqlx::query_as(
        "SELECT eq.question_id, eq.points::float8 AS points, q.question_text, q.content
         FROM exam_questions eq
         JOIN questions q ON q.id = eq.question_id
         WHERE eq.exam_id = $1",
    )
    .bind(row.exam_id)
    .fetch_all(st.db_ref())
    .await?;

    let mut by_id: HashMap<Uuid, (f64, String, serde_json::Value)> = HashMap::new();
    for (qid, points, text, content) in rows {
        by_id.insert(qid, (points, text, content));
    }

    let mut soal = Vec::new();
    for (i, qid) in order.iter().enumerate() {
        if let Some((points, text, content)) = by_id.get(qid) {
            let parsed: PgContent = serde_json::from_value(content.clone())
                .map_err(|_| AppError::Internal("konten soal rusak".into()))?;
            soal.push(SoalOut {
                nomor: i + 1,
                question_id: *qid,
                points: *points,
                question_text: text.clone(),
                opsi: parsed.opsi, // kunci sengaja dibuang
            });
        }
    }

    // Jawaban tersimpan
    let ans: Vec<(Uuid, Option<String>)> = sqlx::query_as(
        "SELECT question_id, answer_content->>'pilihan' AS pilihan
         FROM answers WHERE attempt_id = $1",
    )
    .bind(attempt_id)
    .fetch_all(st.db_ref())
    .await?;
    let mut answers = HashMap::new();
    for (qid, pilihan) in ans {
        if let Some(p) = pilihan {
            answers.insert(qid, p);
        }
    }

    Ok(AttemptState {
        attempt_id,
        exam_judul: row.judul.clone(),
        status: row.status.clone(),
        time_left_seconds: time_left(row.deadline_efektif),
        soal,
        answers,
    })
}

// ---------- Mulai / resume attempt ----------

pub async fn start_attempt(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(exam_id): Path<Uuid>,
) -> AppResult<Json<AttemptState>> {
    auth.require_role(&["mahasiswa"])?;

    // Kelayakan: terdaftar (enrollment) di kelas yang mengikuti ujian ini.
    let eligible: bool = sqlx::query_scalar(
        "SELECT EXISTS(
             SELECT 1 FROM exam_kelas ek
             JOIN enrollment en ON en.kelas_id = ek.kelas_id
             WHERE ek.exam_id = $1 AND en.mahasiswa_id = $2 AND en.status = 'aktif')",
    )
    .bind(exam_id)
    .bind(auth.user_id)
    .fetch_one(st.db_ref())
    .await?;
    if !eligible {
        return Err(AppError::Unauthorized(
            "Anda tidak terdaftar pada ujian ini".into(),
        ));
    }

    // Ujian harus dalam jendela waktu.
    let exam: Option<(bool, bool)> = sqlx::query_as(
        "SELECT (now() BETWEEN waktu_mulai AND waktu_selesai) AS dalam_jendela,
                (status IN ('dijadwalkan','berlangsung')) AS status_ok
         FROM exams WHERE id = $1",
    )
    .bind(exam_id)
    .fetch_optional(st.db_ref())
    .await?;
    let (dalam_jendela, status_ok) =
        exam.ok_or_else(|| AppError::NotFound("Ujian tidak ditemukan".into()))?;
    if !dalam_jendela || !status_ok {
        return Err(AppError::Validation(
            "Ujian belum dibuka atau sudah ditutup".into(),
        ));
    }

    // Attempt yang sudah ada?
    let existing: Option<(Uuid, String)> =
        sqlx::query_as("SELECT id, status FROM exam_attempts WHERE exam_id = $1 AND mahasiswa_id = $2")
            .bind(exam_id)
            .bind(auth.user_id)
            .fetch_optional(st.db_ref())
            .await?;

    if let Some((att_id, status)) = existing {
        if status == "berlangsung" {
            let row = load_attempt(&st, att_id, auth.user_id).await?;
            return Ok(Json(build_state(&st, att_id, &row).await?));
        }
        return Err(AppError::Conflict(
            "Anda sudah menyelesaikan ujian ini".into(),
        ));
    }

    // Susun urutan soal (acak bila diminta ujiannya).
    let acak: bool = sqlx::query_scalar("SELECT acak_soal FROM exams WHERE id = $1")
        .bind(exam_id)
        .fetch_one(st.db_ref())
        .await?;
    let order_sql = if acak {
        "SELECT question_id FROM exam_questions WHERE exam_id = $1 ORDER BY random()"
    } else {
        "SELECT question_id FROM exam_questions WHERE exam_id = $1 ORDER BY urutan"
    };
    let order_ids: Vec<Uuid> = sqlx::query_scalar(order_sql)
        .bind(exam_id)
        .fetch_all(st.db_ref())
        .await?;
    if order_ids.is_empty() {
        return Err(AppError::Validation("Ujian ini belum memiliki soal".into()));
    }

    // Buat attempt: deadline dihitung SERVER = min(waktu_selesai, sekarang + durasi).
    let attempt_id: Uuid = sqlx::query_scalar(
        "INSERT INTO exam_attempts (exam_id, mahasiswa_id, started_at, deadline_efektif, status, soal_order)
         SELECT $1, $2, now(),
                LEAST(e.waktu_selesai, now() + make_interval(mins => e.durasi_menit)),
                'berlangsung', $3
         FROM exams e WHERE e.id = $1
         RETURNING id",
    )
    .bind(exam_id)
    .bind(auth.user_id)
    .bind(sqlx::types::Json(&order_ids))
    .fetch_one(st.db_ref())
    .await?;

    let row = load_attempt(&st, attempt_id, auth.user_id).await?;
    Ok(Json(build_state(&st, attempt_id, &row).await?))
}

// ---------- Ambil state attempt (resume) ----------

pub async fn get_attempt(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(attempt_id): Path<Uuid>,
) -> AppResult<Json<AttemptState>> {
    auth.require_role(&["mahasiswa"])?;
    let mut row = load_attempt(&st, attempt_id, auth.user_id).await?;

    // Kalau waktu sudah habis tapi belum disubmit -> finalisasi otomatis.
    if row.status == "berlangsung" && time_left(row.deadline_efektif) == 0 {
        finalize(&st, attempt_id, row.exam_id).await?;
        row = load_attempt(&st, attempt_id, auth.user_id).await?;
    }

    Ok(Json(build_state(&st, attempt_id, &row).await?))
}

// ---------- Auto-save jawaban ----------

#[derive(Debug, Deserialize)]
pub struct SaveAnswerRequest {
    pub question_id: Uuid,
    pub pilihan: String,
}

#[derive(Debug, Serialize)]
pub struct SaveAnswerResponse {
    pub saved: bool,
    pub time_left_seconds: i64,
}

pub async fn save_answer(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(attempt_id): Path<Uuid>,
    Json(body): Json<SaveAnswerRequest>,
) -> AppResult<Json<SaveAnswerResponse>> {
    auth.require_role(&["mahasiswa"])?;
    let row = load_attempt(&st, attempt_id, auth.user_id).await?;

    if row.status != "berlangsung" {
        return Err(AppError::Conflict("Ujian sudah tidak aktif".into()));
    }
    if time_left(row.deadline_efektif) == 0 {
        // Waktu habis: finalisasi, tolak penyimpanan.
        finalize(&st, attempt_id, row.exam_id).await?;
        return Err(AppError::Conflict("Waktu ujian sudah habis".into()));
    }

    // Pastikan soal memang bagian dari ujian ini.
    let milik: bool = sqlx::query_scalar(
        "SELECT EXISTS(SELECT 1 FROM exam_questions WHERE exam_id = $1 AND question_id = $2)",
    )
    .bind(row.exam_id)
    .bind(body.question_id)
    .fetch_one(st.db_ref())
    .await?;
    if !milik {
        return Err(AppError::Validation("Soal bukan bagian dari ujian ini".into()));
    }

    sqlx::query(
        "INSERT INTO answers (attempt_id, question_id, answer_content)
         VALUES ($1, $2, $3)
         ON CONFLICT (attempt_id, question_id)
         DO UPDATE SET answer_content = EXCLUDED.answer_content, updated_at = now()",
    )
    .bind(attempt_id)
    .bind(body.question_id)
    .bind(sqlx::types::Json(serde_json::json!({ "pilihan": body.pilihan })))
    .execute(st.db_ref())
    .await?;

    Ok(Json(SaveAnswerResponse {
        saved: true,
        time_left_seconds: time_left(row.deadline_efektif),
    }))
}

// ---------- Submit + hasil ----------

#[derive(Debug, Serialize)]
pub struct ResultResponse {
    pub status: String,
    pub tampilkan_hasil: bool,
    /// null bila dosen belum mengizinkan hasil ditampilkan.
    pub total_score: Option<f64>,
}

pub async fn submit(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(attempt_id): Path<Uuid>,
) -> AppResult<Json<ResultResponse>> {
    auth.require_role(&["mahasiswa"])?;
    let row = load_attempt(&st, attempt_id, auth.user_id).await?;

    if row.status == "berlangsung" {
        finalize(&st, attempt_id, row.exam_id).await?;
    }
    // Ambil ulang untuk skor final.
    let row = load_attempt(&st, attempt_id, auth.user_id).await?;
    Ok(Json(hasil_dari(&row)))
}

pub async fn get_result(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(attempt_id): Path<Uuid>,
) -> AppResult<Json<ResultResponse>> {
    auth.require_role(&["mahasiswa"])?;
    let row = load_attempt(&st, attempt_id, auth.user_id).await?;
    if row.status == "berlangsung" {
        return Err(AppError::Conflict("Ujian belum disubmit".into()));
    }
    Ok(Json(hasil_dari(&row)))
}

fn hasil_dari(row: &AttemptRow) -> ResultResponse {
    ResultResponse {
        status: row.status.clone(),
        tampilkan_hasil: row.tampilkan_hasil,
        total_score: if row.tampilkan_hasil {
            row.total_score
        } else {
            None
        },
    }
}

/// Auto-grade PG: bandingkan jawaban tersimpan dengan kunci, hitung skor,
/// tandai tiap jawaban, dan set attempt jadi 'dinilai'. Dibungkus transaksi.
async fn finalize(st: &AppState, attempt_id: Uuid, exam_id: Uuid) -> AppResult<f64> {
    let rows: Vec<(Uuid, f64, String, Option<String>)> = sqlx::query_as(
        "SELECT eq.question_id, eq.points::float8 AS points,
                q.content->>'kunci' AS kunci,
                a.answer_content->>'pilihan' AS pilihan
         FROM exam_questions eq
         JOIN questions q ON q.id = eq.question_id
         LEFT JOIN answers a ON a.attempt_id = $1 AND a.question_id = eq.question_id
         WHERE eq.exam_id = $2",
    )
    .bind(attempt_id)
    .bind(exam_id)
    .fetch_all(st.db_ref())
    .await?;

    let mut total = 0.0;
    let mut tx = st.db_ref().begin().await?;
    for (qid, points, kunci, pilihan) in rows {
        let (benar, skor) = nilai_pg(&kunci, pilihan.as_deref(), points);
        total += skor;
        if pilihan.is_some() {
            sqlx::query(
                "UPDATE answers SET is_correct = $1, score = $2, auto_graded = true, graded_at = now()
                 WHERE attempt_id = $3 AND question_id = $4",
            )
            .bind(benar)
            .bind(skor)
            .bind(attempt_id)
            .bind(qid)
            .execute(&mut *tx)
            .await?;
        }
    }

    sqlx::query(
        "UPDATE exam_attempts SET total_score = $1, status = 'dinilai', submitted_at = now()
         WHERE id = $2",
    )
    .bind(total)
    .bind(attempt_id)
    .execute(&mut *tx)
    .await?;

    tx.commit().await?;
    Ok(total)
}
