//! State bersama yang di-inject ke setiap handler oleh Axum.
//!
//! Konsep Rust: `#[derive(Clone)]` + `Arc` — Axum meng-clone state untuk tiap
//! request. `PgPool` sudah murah di-clone (Arc internal); `Config` kita bungkus
//! `Arc` agar clone-nya cuma menaikkan reference count, bukan menyalin data.

use crate::config::Config;
use sqlx::PgPool;
use std::sync::Arc;

#[derive(Clone)]
pub struct AppState {
    pub db: PgPool,
    pub config: Arc<Config>,
}

impl AppState {
    /// Pintasan ke pool DB (dipakai handler & helper).
    pub fn db_ref(&self) -> &PgPool {
        &self.db
    }
}
