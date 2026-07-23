//! Struct yang memetakan baris database & payload.
//!
//! `FromRow` (sqlx) memetakan kolom hasil query ke field struct berdasarkan
//! nama. `Serialize`/`Deserialize` (serde) untuk konversi ke/dari JSON.

use serde::{Deserialize, Serialize};
use sqlx::FromRow;
use uuid::Uuid;

/// Representasi penuh baris `users` (termasuk hash — JANGAN diserialisasi ke klien).
#[derive(Debug, FromRow)]
pub struct User {
    pub id: Uuid,
    pub role: String,
    pub nama: String,
    pub email: String,
    pub nim_nip: Option<String>,
    pub password_hash: String,
    pub prodi_id: Option<Uuid>,
    pub is_active: bool,
}

/// Versi aman untuk dikirim ke klien (tanpa password_hash).
#[derive(Debug, Serialize)]
pub struct UserPublic {
    pub id: Uuid,
    pub role: String,
    pub nama: String,
    pub email: String,
    pub nim_nip: Option<String>,
}

impl From<User> for UserPublic {
    fn from(u: User) -> Self {
        Self {
            id: u.id,
            role: u.role,
            nama: u.nama,
            email: u.email,
            nim_nip: u.nim_nip,
        }
    }
}

/// Isi (payload) JWT.
#[derive(Debug, Serialize, Deserialize)]
pub struct Claims {
    pub sub: String,  // user id (UUID sebagai string)
    pub role: String, // peran, untuk RBAC cepat tanpa query DB
    pub typ: String,  // "access" atau "refresh"
    pub iat: i64,     // issued-at (unix)
    pub exp: i64,     // expiry (unix) — divalidasi otomatis oleh jsonwebtoken
}
