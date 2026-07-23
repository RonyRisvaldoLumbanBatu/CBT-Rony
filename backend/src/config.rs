//! Konfigurasi aplikasi, dibaca dari environment variable (.env).
//!
//! Konsep Rust: struct sederhana yang memiliki (owns) semua nilainya berupa
//! `String`/angka. Fungsi `from_env` mengembalikan `anyhow::Result<Config>` —
//! kalau ada env yang wajib tapi kosong, kita kembalikan error, bukan panic.

use anyhow::Context;

#[derive(Debug, Clone)]
pub struct Config {
    pub database_url: String,
    pub jwt_access_secret: String,
    pub jwt_refresh_secret: String,
    pub access_ttl_min: i64,
    pub refresh_ttl_days: i64,
    pub port: u16,
    pub frontend_origin: String,
}

impl Config {
    pub fn from_env() -> anyhow::Result<Self> {
        Ok(Self {
            database_url: env_required("DATABASE_URL")?,
            jwt_access_secret: env_required("JWT_ACCESS_SECRET")?,
            jwt_refresh_secret: env_required("JWT_REFRESH_SECRET")?,
            access_ttl_min: env_or("ACCESS_TTL_MIN", 15),
            refresh_ttl_days: env_or("REFRESH_TTL_DAYS", 7),
            port: env_or("PORT", 3000),
            frontend_origin: std::env::var("FRONTEND_ORIGIN")
                .unwrap_or_else(|_| "http://localhost:5173".to_string()),
        })
    }
}

/// Ambil env wajib; error kalau tidak ada.
fn env_required(key: &str) -> anyhow::Result<String> {
    std::env::var(key).with_context(|| format!("environment variable {key} wajib diisi"))
}

/// Ambil env opsional dengan nilai default kalau kosong/tak valid.
fn env_or<T: std::str::FromStr>(key: &str, default: T) -> T {
    std::env::var(key)
        .ok()
        .and_then(|v| v.parse().ok())
        .unwrap_or(default)
}
