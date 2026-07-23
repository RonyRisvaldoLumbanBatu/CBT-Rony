//! Tipe error terpusat untuk seluruh aplikasi.
//!
//! Konsep Rust: alih-alih tiap handler mengembalikan tipe error berbeda, kita
//! punya SATU enum `AppError`. Dengan `thiserror` kita dapat `Display` + trait
//! `Error` otomatis, dan `#[from]` membuat operator `?` mengubah error lain
//! (mis. `sqlx::Error`) menjadi `AppError` secara otomatis.
//!
//! `impl IntoResponse` memberi tahu Axum cara mengubah `AppError` menjadi
//! respons HTTP (status code + body JSON).

use axum::{
    http::StatusCode,
    response::{IntoResponse, Response},
    Json,
};
use serde_json::json;

pub type AppResult<T> = Result<T, AppError>;

#[derive(Debug, thiserror::Error)]
pub enum AppError {
    #[error("kesalahan database")]
    Db(#[from] sqlx::Error),

    #[error("{0}")]
    Validation(String),

    #[error("{0}")]
    Unauthorized(String),

    #[error("{0}")]
    Conflict(String),

    #[error("{0}")]
    NotFound(String),

    #[error("{0}")]
    Internal(String),
}

impl IntoResponse for AppError {
    fn into_response(self) -> Response {
        let (status, message) = match self {
            AppError::Db(e) => {
                // Detail error DB tidak boleh bocor ke klien — cukup di-log.
                tracing::error!("database error: {e}");
                (
                    StatusCode::INTERNAL_SERVER_ERROR,
                    "Terjadi kesalahan pada server".to_string(),
                )
            }
            AppError::Validation(m) => (StatusCode::UNPROCESSABLE_ENTITY, m),
            AppError::Unauthorized(m) => (StatusCode::UNAUTHORIZED, m),
            AppError::Conflict(m) => (StatusCode::CONFLICT, m),
            AppError::NotFound(m) => (StatusCode::NOT_FOUND, m),
            AppError::Internal(m) => {
                tracing::error!("internal error: {m}");
                (
                    StatusCode::INTERNAL_SERVER_ERROR,
                    "Terjadi kesalahan pada server".to_string(),
                )
            }
        };

        (status, Json(json!({ "error": message }))).into_response()
    }
}
