//! Endpoint autentikasi: register, login, refresh, me.
//!
//! Pola tiap handler: terima `State<AppState>` (akses DB & config) + `Json<...>`
//! (body request), kembalikan `AppResult<Json<...>>`. Operator `?` otomatis
//! mengubah error DB/validasi menjadi respons HTTP lewat `AppError`.
//!
//! Catatan: sqlx 0.9 hanya menerima SQL literal (&'static str) demi mencegah
//! injeksi — jadi kolom ditulis lengkap di tiap query, data selalu via bind ($1).

use crate::{
    auth::{jwt, password, AuthUser},
    error::{AppError, AppResult},
    models::{User, UserPublic},
    state::AppState,
};
use axum::{extract::State, Json};
use serde::{Deserialize, Serialize};
use uuid::Uuid;
use validator::Validate;

#[derive(Debug, Deserialize, Validate)]
pub struct RegisterRequest {
    #[validate(length(min = 3, message = "nama minimal 3 karakter"))]
    pub nama: String,
    #[validate(email(message = "email tidak valid"))]
    pub email: String,
    #[validate(length(min = 8, message = "password minimal 8 karakter"))]
    pub password: String,
    pub nim_nip: Option<String>,
}

#[derive(Debug, Deserialize)]
pub struct LoginRequest {
    /// Bisa email ATAU NIM/NIP.
    pub identifier: String,
    pub password: String,
}

#[derive(Debug, Deserialize)]
pub struct RefreshRequest {
    pub refresh_token: String,
}

#[derive(Debug, Serialize)]
pub struct TokenPair {
    pub access_token: String,
    pub refresh_token: String,
    pub user: UserPublic,
}

/// Self-register: selalu membuat akun 'mahasiswa'. Akun dosen/admin dibuat
/// lewat seed atau (nanti) panel admin.
pub async fn register(
    State(st): State<AppState>,
    Json(body): Json<RegisterRequest>,
) -> AppResult<Json<TokenPair>> {
    body.validate()
        .map_err(|e| AppError::Validation(e.to_string()))?;

    let exists: Option<(Uuid,)> = sqlx::query_as("SELECT id FROM users WHERE email = $1")
        .bind(&body.email)
        .fetch_optional(&st.db)
        .await?;
    if exists.is_some() {
        return Err(AppError::Conflict("Email sudah terdaftar".into()));
    }

    let hash = password::hash_password(&body.password)?;
    let user: User = sqlx::query_as(
        "INSERT INTO users (role, nama, email, nim_nip, password_hash)
         VALUES ('mahasiswa', $1, $2, $3, $4)
         RETURNING id, role, nama, email, nim_nip, password_hash, prodi_id, is_active",
    )
    .bind(&body.nama)
    .bind(&body.email)
    .bind(&body.nim_nip)
    .bind(&hash)
    .fetch_one(&st.db)
    .await?;

    Ok(Json(terbitkan_token(&st, user)?))
}

pub async fn login(
    State(st): State<AppState>,
    Json(body): Json<LoginRequest>,
) -> AppResult<Json<TokenPair>> {
    let user: Option<User> = sqlx::query_as(
        "SELECT id, role, nama, email, nim_nip, password_hash, prodi_id, is_active
         FROM users WHERE email = $1 OR nim_nip = $1",
    )
    .bind(&body.identifier)
    .fetch_optional(&st.db)
    .await?;

    let user = user.ok_or_else(|| AppError::Unauthorized("Kredensial salah".into()))?;
    if !user.is_active {
        return Err(AppError::Unauthorized("Akun dinonaktifkan".into()));
    }
    if !password::verify_password(&body.password, &user.password_hash) {
        return Err(AppError::Unauthorized("Kredensial salah".into()));
    }

    Ok(Json(terbitkan_token(&st, user)?))
}

pub async fn refresh(
    State(st): State<AppState>,
    Json(body): Json<RefreshRequest>,
) -> AppResult<Json<TokenPair>> {
    let claims = jwt::decode_refresh(&st.config, &body.refresh_token)?;
    let id = Uuid::parse_str(&claims.sub)
        .map_err(|_| AppError::Unauthorized("Token rusak".into()))?;

    let user: User = sqlx::query_as(
        "SELECT id, role, nama, email, nim_nip, password_hash, prodi_id, is_active
         FROM users WHERE id = $1",
    )
    .bind(id)
    .fetch_optional(&st.db)
    .await?
    .ok_or_else(|| AppError::Unauthorized("User tidak ditemukan".into()))?;

    Ok(Json(terbitkan_token(&st, user)?))
}

/// Endpoint terproteksi: butuh access token valid (via extractor AuthUser).
pub async fn me(State(st): State<AppState>, auth: AuthUser) -> AppResult<Json<UserPublic>> {
    let user: User = sqlx::query_as(
        "SELECT id, role, nama, email, nim_nip, password_hash, prodi_id, is_active
         FROM users WHERE id = $1",
    )
    .bind(auth.user_id)
    .fetch_optional(&st.db)
    .await?
    .ok_or_else(|| AppError::NotFound("User tidak ditemukan".into()))?;
    Ok(Json(user.into()))
}

/// Bikin sepasang token + data user publik.
fn terbitkan_token(st: &AppState, user: User) -> AppResult<TokenPair> {
    let id = user.id.to_string();
    let access_token = jwt::create_access_token(&st.config, &id, &user.role)?;
    let refresh_token = jwt::create_refresh_token(&st.config, &id, &user.role)?;
    Ok(TokenPair {
        access_token,
        refresh_token,
        user: user.into(),
    })
}
