-- Bagian 2 — Master data: pengguna & enrollment

-- Satu tabel users untuk semua peran (RBAC lewat kolom role).
-- nim_nip: NIM untuk mahasiswa, NIP/NIDN untuk dosen, null untuk admin.
CREATE TABLE users (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role          TEXT NOT NULL CHECK (role IN ('superadmin','admin','dosen','mahasiswa')),
    nama          TEXT NOT NULL,
    email         TEXT NOT NULL UNIQUE,
    nim_nip       TEXT UNIQUE,
    password_hash TEXT NOT NULL,
    prodi_id      UUID REFERENCES program_studi(id) ON DELETE SET NULL,
    is_active     BOOLEAN NOT NULL DEFAULT true,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Kelas / rombel: penyelenggaraan satu mata kuliah pada satu tahun akademik.
CREATE TABLE kelas (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    mata_kuliah_id    UUID NOT NULL REFERENCES mata_kuliah(id) ON DELETE RESTRICT,
    tahun_akademik_id UUID NOT NULL REFERENCES tahun_akademik(id) ON DELETE RESTRICT,
    dosen_id          UUID REFERENCES users(id) ON DELETE SET NULL,
    nama              TEXT NOT NULL,
    kapasitas         INTEGER,
    created_at        TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (mata_kuliah_id, tahun_akademik_id, nama)
);

-- Enrollment: mahasiswa yang terdaftar di sebuah kelas.
-- Menentukan siapa BERHAK ikut ujian tertentu.
CREATE TABLE enrollment (
    id           UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    mahasiswa_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    kelas_id     UUID NOT NULL REFERENCES kelas(id) ON DELETE CASCADE,
    status       TEXT NOT NULL DEFAULT 'aktif' CHECK (status IN ('aktif','nonaktif')),
    created_at   TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (mahasiswa_id, kelas_id)
);
