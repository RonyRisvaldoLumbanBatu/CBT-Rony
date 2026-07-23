//! Endpoint untuk dosen: menyusun ujian & soal (Fase 1, tipe Pilihan Ganda).
//! Semua endpoint mewajibkan peran 'dosen' dan kepemilikan ujian (created_by).

use crate::{
    auth::AuthUser,
    error::{AppError, AppResult},
    services::grading::{Opsi, PgContent},
    state::AppState,
};
use axum::{
    extract::{Path, State},
    Json,
};
use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use uuid::Uuid;

// ---------- Buat ujian ----------

#[derive(Debug, Deserialize)]
pub struct CreateExamRequest {
    pub judul: String,
    pub deskripsi: Option<String>,
    pub mata_kuliah_id: Uuid,
    pub jenis_ujian_id: Uuid,
    pub durasi_menit: i32,
    pub waktu_mulai: DateTime<Utc>,
    pub waktu_selesai: DateTime<Utc>,
    #[serde(default = "d_true")]
    pub acak_soal: bool,
    #[serde(default)]
    pub acak_opsi: bool,
    #[serde(default)]
    pub tampilkan_hasil: bool,
    /// Kelas yang mengikuti ujian ini (kelayakan via enrollment).
    #[serde(default)]
    pub kelas_ids: Vec<Uuid>,
}

fn d_true() -> bool {
    true
}

#[derive(Debug, Serialize)]
pub struct ExamCreated {
    pub id: Uuid,
    pub judul: String,
    pub status: String,
}

pub async fn create_exam(
    State(st): State<AppState>,
    auth: AuthUser,
    Json(body): Json<CreateExamRequest>,
) -> AppResult<Json<ExamCreated>> {
    auth.require_role(&["dosen"])?;

    if body.judul.trim().len() < 3 {
        return Err(AppError::Validation("Judul minimal 3 karakter".into()));
    }
    if body.durasi_menit <= 0 {
        return Err(AppError::Validation("Durasi harus lebih dari 0 menit".into()));
    }
    if body.waktu_selesai <= body.waktu_mulai {
        return Err(AppError::Validation(
            "Waktu selesai harus setelah waktu mulai".into(),
        ));
    }

    let tahun_id: Uuid = sqlx::query_scalar(
        "SELECT id FROM tahun_akademik WHERE is_active = true ORDER BY created_at DESC LIMIT 1",
    )
    .fetch_optional(&st.db)
    .await?
    .ok_or_else(|| AppError::Validation("Belum ada tahun akademik aktif".into()))?;

    let exam_id: Uuid = sqlx::query_scalar(
        "INSERT INTO exams
           (judul, deskripsi, mata_kuliah_id, tahun_akademik_id, jenis_ujian_id, created_by,
            durasi_menit, waktu_mulai, waktu_selesai, acak_soal, acak_opsi, tampilkan_hasil, status)
         VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,'dijadwalkan')
         RETURNING id",
    )
    .bind(&body.judul)
    .bind(&body.deskripsi)
    .bind(body.mata_kuliah_id)
    .bind(tahun_id)
    .bind(body.jenis_ujian_id)
    .bind(auth.user_id)
    .bind(body.durasi_menit)
    .bind(body.waktu_mulai)
    .bind(body.waktu_selesai)
    .bind(body.acak_soal)
    .bind(body.acak_opsi)
    .bind(body.tampilkan_hasil)
    .fetch_one(&st.db)
    .await?;

    for kelas_id in &body.kelas_ids {
        sqlx::query("INSERT INTO exam_kelas (exam_id, kelas_id) VALUES ($1,$2) ON CONFLICT DO NOTHING")
            .bind(exam_id)
            .bind(kelas_id)
            .execute(&st.db)
            .await?;
    }

    Ok(Json(ExamCreated {
        id: exam_id,
        judul: body.judul,
        status: "dijadwalkan".into(),
    }))
}

// ---------- Tambah soal PG ----------

#[derive(Debug, Deserialize)]
pub struct AddPgQuestionRequest {
    pub question_text: String,
    pub opsi: Vec<Opsi>,
    pub kunci: String,
    #[serde(default = "d_one")]
    pub points: f64,
}

fn d_one() -> f64 {
    1.0
}

#[derive(Debug, Serialize)]
pub struct QuestionAdded {
    pub question_id: Uuid,
}

pub async fn add_question(
    State(st): State<AppState>,
    auth: AuthUser,
    Path(exam_id): Path<Uuid>,
    Json(body): Json<AddPgQuestionRequest>,
) -> AppResult<Json<QuestionAdded>> {
    auth.require_role(&["dosen"])?;
    pastikan_pemilik_ujian(&st, exam_id, auth.user_id).await?;

    if body.question_text.trim().is_empty() {
        return Err(AppError::Validation("Teks soal wajib diisi".into()));
    }
    if body.opsi.len() < 2 {
        return Err(AppError::Validation("Minimal 2 opsi".into()));
    }
    if !body.opsi.iter().any(|o| o.id == body.kunci) {
        return Err(AppError::Validation(
            "Kunci jawaban harus salah satu id opsi".into(),
        ));
    }

    let content = PgContent {
        opsi: body.opsi,
        kunci: body.kunci,
    };

    // Soal masuk bank (mengikuti mata kuliah ujian), lalu di-link ke ujian.
    let question_id: Uuid = sqlx::query_scalar(
        "INSERT INTO questions (mata_kuliah_id, type, question_text, default_points, content, created_by)
         SELECT e.mata_kuliah_id, 'pilihan_ganda', $2, $3, $4, $5
         FROM exams e WHERE e.id = $1
         RETURNING id",
    )
    .bind(exam_id)
    .bind(&body.question_text)
    .bind(body.points)
    .bind(sqlx::types::Json(content))
    .bind(auth.user_id)
    .fetch_one(&st.db)
    .await?;

    sqlx::query(
        "INSERT INTO exam_questions (exam_id, question_id, urutan, points)
         VALUES ($1, $2, (SELECT COALESCE(MAX(urutan),0)+1 FROM exam_questions WHERE exam_id=$1), $3)",
    )
    .bind(exam_id)
    .bind(question_id)
    .bind(body.points)
    .execute(&st.db)
    .await?;

    Ok(Json(QuestionAdded { question_id }))
}

// ---------- Daftar ujian milik dosen ----------

#[derive(Debug, Serialize, sqlx::FromRow)]
pub struct ExamSummary {
    pub id: Uuid,
    pub judul: String,
    pub status: String,
    pub durasi_menit: i32,
    pub jumlah_soal: i64,
}

pub async fn list_exams(
    State(st): State<AppState>,
    auth: AuthUser,
) -> AppResult<Json<Vec<ExamSummary>>> {
    auth.require_role(&["dosen"])?;

    let exams: Vec<ExamSummary> = sqlx::query_as(
        "SELECT e.id, e.judul, e.status, e.durasi_menit,
                (SELECT count(*) FROM exam_questions eq WHERE eq.exam_id = e.id) AS jumlah_soal
         FROM exams e
         WHERE e.created_by = $1
         ORDER BY e.created_at DESC",
    )
    .bind(auth.user_id)
    .fetch_all(&st.db)
    .await?;

    Ok(Json(exams))
}

/// Pastikan ujian ada & dimiliki dosen ini.
async fn pastikan_pemilik_ujian(st: &AppState, exam_id: Uuid, dosen_id: Uuid) -> AppResult<()> {
    let owner: Option<Uuid> = sqlx::query_scalar("SELECT created_by FROM exams WHERE id = $1")
        .bind(exam_id)
        .fetch_optional(&st.db)
        .await?;
    match owner {
        None => Err(AppError::NotFound("Ujian tidak ditemukan".into())),
        Some(o) if o == dosen_id => Ok(()),
        Some(_) => Err(AppError::Unauthorized("Ujian ini milik dosen lain".into())),
    }
}
