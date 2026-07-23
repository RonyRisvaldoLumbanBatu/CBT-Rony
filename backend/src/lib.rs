//! Library crate `backend`. Semua modul diekspos di sini agar bisa dipakai
//! oleh binary utama (main.rs) MAUPUN binary seed (bin/seed.rs).
//!
//! `run()` adalah titik masuk server: baca config, buka pool DB, jalankan
//! migrasi, rakit router, lalu serve.

pub mod auth;
pub mod config;
pub mod error;
pub mod handlers;
pub mod models;
pub mod state;

use axum::{
    routing::{get, post},
    Router,
};
use sqlx::postgres::PgPoolOptions;
use std::sync::Arc;
use tower_http::{
    cors::{Any, CorsLayer},
    trace::TraceLayer,
};

use crate::{config::Config, state::AppState};

pub async fn run() -> anyhow::Result<()> {
    // Muat variabel dari .env (kalau ada). `.ok()` = abaikan error kalau file tak ada.
    dotenvy::dotenv().ok();

    // Setup logging. RUST_LOG bisa override level via env.
    tracing_subscriber::fmt()
        .with_env_filter(
            tracing_subscriber::EnvFilter::try_from_default_env()
                .unwrap_or_else(|_| "backend=debug,tower_http=info,info".into()),
        )
        .init();

    let config = Config::from_env()?;

    // Pool koneksi: dibagi ke semua request. Cheap untuk di-clone (Arc di dalam).
    let db = PgPoolOptions::new()
        .max_connections(10)
        .connect(&config.database_url)
        .await?;

    // Migrasi embedded: file .sql dari ./migrations ditanam saat kompilasi,
    // lalu yang belum jalan diterapkan di sini.
    sqlx::migrate!("./migrations").run(&db).await?;
    tracing::info!("migrasi database selesai");

    // CORS supaya frontend (Vite dev server) boleh memanggil API ini.
    let cors = CorsLayer::new()
        .allow_origin(config.frontend_origin.parse::<axum::http::HeaderValue>()?)
        .allow_methods(Any)
        .allow_headers(Any);

    let port = config.port;
    // State dibungkus & dipindahkan (moved) ke router. Config di-Arc agar murah di-clone.
    let state = AppState {
        db,
        config: Arc::new(config),
    };

    let app = Router::new()
        .route("/health", get(handlers::health::health))
        .route("/api/auth/register", post(handlers::auth::register))
        .route("/api/auth/login", post(handlers::auth::login))
        .route("/api/auth/refresh", post(handlers::auth::refresh))
        .route("/api/auth/me", get(handlers::auth::me))
        .layer(TraceLayer::new_for_http())
        .layer(cors)
        .with_state(state);

    let addr = format!("0.0.0.0:{port}");
    let listener = tokio::net::TcpListener::bind(&addr).await?;
    tracing::info!("backend berjalan di http://localhost:{port}");
    axum::serve(listener, app).await?;
    Ok(())
}
