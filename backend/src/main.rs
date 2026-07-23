//! Titik masuk binary server. Semua logika ada di `backend::run()`.

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    backend::run().await
}
