//! Pembuatan & verifikasi JWT (access + refresh).
//!
//! Access token: masa hidup pendek, dipakai tiap request via header
//! `Authorization: Bearer <token>`. Refresh token: masa hidup panjang, dipakai
//! menukar access token baru tanpa login ulang. Keduanya pakai secret berbeda.

use crate::{
    config::Config,
    error::{AppError, AppResult},
    models::Claims,
};
use chrono::{Duration, Utc};
use jsonwebtoken::{decode, encode, Algorithm, DecodingKey, EncodingKey, Header, Validation};

pub fn create_access_token(cfg: &Config, user_id: &str, role: &str) -> AppResult<String> {
    create(
        cfg.jwt_access_secret.as_bytes(),
        user_id,
        role,
        "access",
        Duration::minutes(cfg.access_ttl_min),
    )
}

pub fn create_refresh_token(cfg: &Config, user_id: &str, role: &str) -> AppResult<String> {
    create(
        cfg.jwt_refresh_secret.as_bytes(),
        user_id,
        role,
        "refresh",
        Duration::days(cfg.refresh_ttl_days),
    )
}

fn create(secret: &[u8], sub: &str, role: &str, typ: &str, ttl: Duration) -> AppResult<String> {
    let now = Utc::now();
    let claims = Claims {
        sub: sub.to_string(),
        role: role.to_string(),
        typ: typ.to_string(),
        iat: now.timestamp(),
        exp: (now + ttl).timestamp(),
    };
    encode(
        &Header::new(Algorithm::HS256),
        &claims,
        &EncodingKey::from_secret(secret),
    )
    .map_err(|e| AppError::Internal(format!("gagal membuat token: {e}")))
}

pub fn decode_access(cfg: &Config, token: &str) -> AppResult<Claims> {
    decode_with(cfg.jwt_access_secret.as_bytes(), token, "access")
}

pub fn decode_refresh(cfg: &Config, token: &str) -> AppResult<Claims> {
    decode_with(cfg.jwt_refresh_secret.as_bytes(), token, "refresh")
}

fn decode_with(secret: &[u8], token: &str, expected_typ: &str) -> AppResult<Claims> {
    // Validation::new sudah otomatis memeriksa `exp` (kedaluwarsa).
    let data = decode::<Claims>(
        token,
        &DecodingKey::from_secret(secret),
        &Validation::new(Algorithm::HS256),
    )
    .map_err(|_| AppError::Unauthorized("Token tidak valid atau kedaluwarsa".into()))?;

    // Pastikan token access tidak dipakai sebagai refresh, dan sebaliknya.
    if data.claims.typ != expected_typ {
        return Err(AppError::Unauthorized("Jenis token salah".into()));
    }
    Ok(data.claims)
}
