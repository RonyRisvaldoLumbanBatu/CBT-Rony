//! Tipe konten & jawaban untuk soal Pilihan Ganda (Fase 1) + penilaian.
//!
//! Struktur JSONB (sesuai dokumentasi skema):
//!   questions.content       -> { "opsi": [{"id","teks"}], "kunci": "a" }
//!   answers.answer_content   -> { "pilihan": "a" }

use serde::{Deserialize, Serialize};

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct Opsi {
    pub id: String,
    pub teks: String,
}

/// Konten soal PG yang disusun dosen (berisi kunci — JANGAN kirim ke mahasiswa).
#[derive(Debug, Serialize, Deserialize)]
pub struct PgContent {
    pub opsi: Vec<Opsi>,
    pub kunci: String,
}

/// Nilai satu soal PG: benar bila pilihan sama dengan kunci.
pub fn nilai_pg(kunci: &str, pilihan: Option<&str>, points: f64) -> (bool, f64) {
    let benar = pilihan == Some(kunci);
    (benar, if benar { points } else { 0.0 })
}
