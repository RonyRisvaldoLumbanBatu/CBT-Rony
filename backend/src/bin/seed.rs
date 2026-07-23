//! Binary seed: mengisi data awal agar alur bisa langsung diuji.
//! Jalankan: `cargo run --bin seed`
//!
//! Idempotent: kalau superadmin sudah ada, seed dilewati.

use anyhow::Result;
use backend::{auth::password, config::Config};
use sqlx::postgres::PgPoolOptions;
use uuid::Uuid;

#[tokio::main]
async fn main() -> Result<()> {
    dotenvy::dotenv().ok();
    let config = Config::from_env()?;
    let db = PgPoolOptions::new()
        .max_connections(5)
        .connect(&config.database_url)
        .await?;

    let sudah: Option<(Uuid,)> = sqlx::query_as("SELECT id FROM users WHERE email = $1")
        .bind("superadmin@kampus.ac.id")
        .fetch_optional(&db)
        .await?;
    if sudah.is_some() {
        println!("Data seed sudah ada — dilewati.");
        return Ok(());
    }

    // --- Master akademik ---
    let tahun_id: Uuid = sqlx::query_scalar(
        "INSERT INTO tahun_akademik (tahun, semester, is_active)
         VALUES ('2025/2026','ganjil',true) RETURNING id",
    )
    .fetch_one(&db)
    .await?;

    let fakultas_id: Uuid = sqlx::query_scalar(
        "INSERT INTO fakultas (kode, nama) VALUES ('FT','Fakultas Teknik') RETURNING id",
    )
    .fetch_one(&db)
    .await?;

    let prodi_id: Uuid = sqlx::query_scalar(
        "INSERT INTO program_studi (fakultas_id, kode, nama, jenjang)
         VALUES ($1,'TI','Teknik Informatika','S1') RETURNING id",
    )
    .bind(fakultas_id)
    .fetch_one(&db)
    .await?;

    let matkul_id: Uuid = sqlx::query_scalar(
        "INSERT INTO mata_kuliah (prodi_id, kode, nama, sks)
         VALUES ($1,'TI101','Pemrograman Dasar',3) RETURNING id",
    )
    .bind(prodi_id)
    .fetch_one(&db)
    .await?;

    // --- Pengguna (semua password: password123) ---
    let pw = password::hash_password("password123")?;
    insert_user(&db, "superadmin", "Super Admin", "superadmin@kampus.ac.id", None, &pw, None).await?;
    insert_user(&db, "admin", "Admin Kampus", "admin@kampus.ac.id", None, &pw, None).await?;
    let dosen_id = insert_user(
        &db, "dosen", "Dr. Budi Dosen", "budi@kampus.ac.id",
        Some("198001012005011001"), &pw, Some(prodi_id),
    )
    .await?;

    // --- Kelas (diampu dosen) ---
    let kelas_id: Uuid = sqlx::query_scalar(
        "INSERT INTO kelas (mata_kuliah_id, tahun_akademik_id, dosen_id, nama, kapasitas)
         VALUES ($1,$2,$3,'TI-1A',40) RETURNING id",
    )
    .bind(matkul_id)
    .bind(tahun_id)
    .bind(dosen_id)
    .fetch_one(&db)
    .await?;

    // --- Mahasiswa + enrollment ---
    for (nama, nim) in [
        ("Ani Mahasiswa", "2025001"),
        ("Budi Mahasiswa", "2025002"),
        ("Citra Mahasiswa", "2025003"),
    ] {
        let email = format!("{nim}@student.kampus.ac.id");
        let mid = insert_user(&db, "mahasiswa", nama, &email, Some(nim), &pw, Some(prodi_id)).await?;
        sqlx::query("INSERT INTO enrollment (mahasiswa_id, kelas_id) VALUES ($1,$2)")
            .bind(mid)
            .bind(kelas_id)
            .execute(&db)
            .await?;
    }

    // --- Master lab (biar siap dipakai fase berikutnya) ---
    sqlx::query("INSERT INTO jenis_ujian (nama, bobot) VALUES ('UTS',30),('UAS',40),('Kuis',10)")
        .execute(&db)
        .await?;
    sqlx::query("INSERT INTO ruang (kode, nama, lokasi, kapasitas) VALUES ('LAB1','Lab Komputer 1','Gedung A',40)")
        .execute(&db)
        .await?;
    sqlx::query("INSERT INTO sesi (nama, jam_mulai, jam_selesai) VALUES ('Sesi 1','08:00','10:00'),('Sesi 2','10:30','12:30')")
        .execute(&db)
        .await?;

    println!("Seed selesai! Semua akun berpassword: password123");
    println!("  superadmin : superadmin@kampus.ac.id");
    println!("  admin      : admin@kampus.ac.id");
    println!("  dosen      : budi@kampus.ac.id  (atau NIP 198001012005011001)");
    println!("  mahasiswa  : NIM 2025001 / 2025002 / 2025003");
    Ok(())
}

async fn insert_user(
    db: &sqlx::PgPool,
    role: &str,
    nama: &str,
    email: &str,
    nim: Option<&str>,
    hash: &str,
    prodi: Option<Uuid>,
) -> Result<Uuid> {
    let id = sqlx::query_scalar::<_, Uuid>(
        "INSERT INTO users (role, nama, email, nim_nip, password_hash, prodi_id)
         VALUES ($1,$2,$3,$4,$5,$6) RETURNING id",
    )
    .bind(role)
    .bind(nama)
    .bind(email)
    .bind(nim)
    .bind(hash)
    .bind(prodi)
    .fetch_one(db)
    .await?;
    Ok(id)
}
