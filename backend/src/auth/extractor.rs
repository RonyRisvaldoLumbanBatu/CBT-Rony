//! Extractor `AuthUser` — inti RBAC.
//!
//! Konsep Axum: sebuah tipe bisa jadi "extractor" dengan meng-implement
//! `FromRequestParts`. Saat handler menuliskan parameter `auth: AuthUser`,
//! Axum otomatis menjalankan kode di bawah SEBELUM handler: mengambil header
//! Authorization, memverifikasi JWT, lalu menyediakan `AuthUser`. Kalau gagal,
//! handler tak pernah dijalankan dan klien menerima 401.

use crate::{auth::jwt, error::AppError, state::AppState};
use axum::{extract::FromRequestParts, http::request::Parts};
use uuid::Uuid;

pub struct AuthUser {
    pub user_id: Uuid,
    pub role: String,
}

impl FromRequestParts<AppState> for AuthUser {
    type Rejection = AppError;

    async fn from_request_parts(
        parts: &mut Parts,
        state: &AppState,
    ) -> Result<Self, Self::Rejection> {
        let header = parts
            .headers
            .get(axum::http::header::AUTHORIZATION)
            .and_then(|h| h.to_str().ok())
            .ok_or_else(|| AppError::Unauthorized("Header Authorization tidak ada".into()))?;

        let token = header
            .strip_prefix("Bearer ")
            .ok_or_else(|| AppError::Unauthorized("Format harus 'Bearer <token>'".into()))?;

        let claims = jwt::decode_access(&state.config, token)?;
        let user_id = Uuid::parse_str(&claims.sub)
            .map_err(|_| AppError::Unauthorized("Token rusak".into()))?;

        Ok(AuthUser {
            user_id,
            role: claims.role,
        })
    }
}

impl AuthUser {
    /// Pastikan peran user termasuk salah satu yang diizinkan, atau error 401.
    pub fn require_role(&self, roles: &[&str]) -> Result<(), AppError> {
        if roles.contains(&self.role.as_str()) {
            Ok(())
        } else {
            Err(AppError::Unauthorized(
                "Anda tidak punya akses ke sumber daya ini".into(),
            ))
        }
    }
}
