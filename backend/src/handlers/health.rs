//! Health check sederhana — dipakai untuk cek server hidup & terhubung.

use axum::Json;
use serde_json::{json, Value};

pub async fn health() -> Json<Value> {
    Json(json!({ "status": "ok", "service": "ujian-online-backend" }))
}
