# Aplikasi Ujian Online Universitas

Aplikasi ujian online berbasis web (model lab/ruang/sesi/token) untuk lingkungan
universitas. Dibangun ulang dari nol dengan **Rust (Axum)** di backend dan
**React + TypeScript** di frontend.

> Versi lama (Laravel) diarsipkan di branch `laravel-legacy`.

## Struktur

```
backend/    Rust + Axum + SQLx (PostgreSQL) + JWT + Argon2
frontend/   React + TypeScript + Vite + Tailwind + TanStack Query
```

## Prasyarat (mesin ini)

Toolchain Rust ada di `C:\Rony\rust-tools` (dipindah ke sana karena home user
mengandung spasi yang mematahkan linker GNU). **Sebelum menjalankan cargo,
source dulu env-nya:**

```bash
source /c/Rony/rust-tools/env.sh
```

## Menjalankan backend

```bash
source /c/Rony/rust-tools/env.sh
cd backend
cp .env.example .env          # sesuaikan DATABASE_URL bila perlu
cargo run --bin seed          # isi data awal (sekali)
cargo run                     # server di http://localhost:3000
```

Database `ujian_online` di PostgreSQL lokal. Migrasi otomatis dijalankan saat
server start (`sqlx::migrate!`).

### Akun seed (password semua: `password123`)

| Peran      | Login                                    |
|------------|------------------------------------------|
| superadmin | superadmin@kampus.ac.id                  |
| admin      | admin@kampus.ac.id                       |
| dosen      | budi@kampus.ac.id / NIP 198001012005011001 |
| mahasiswa  | NIM 2025001 / 2025002 / 2025003          |

### Endpoint (Fase 0)

- `GET  /health`
- `POST /api/auth/register` (self-register → mahasiswa)
- `POST /api/auth/login` (identifier = email atau NIM/NIP)
- `POST /api/auth/refresh`
- `GET  /api/auth/me` (perlu `Authorization: Bearer <access_token>`)

## Status

Fase 0 (fondasi): skema 20 tabel, autentikasi JWT + RBAC, seed. Selesai.
Berikutnya — Fase 1: alur inti ujian (buat ujian → kerjakan → auto-grade).
