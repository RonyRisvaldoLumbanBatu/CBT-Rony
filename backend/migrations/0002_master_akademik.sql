-- Bagian 1 — Master data: akademik / institusi

CREATE TABLE tahun_akademik (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tahun           TEXT NOT NULL,
    semester        TEXT NOT NULL CHECK (semester IN ('ganjil','genap','pendek')),
    tanggal_mulai   DATE,
    tanggal_selesai DATE,
    is_active       BOOLEAN NOT NULL DEFAULT false,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (tahun, semester)
);

CREATE TABLE fakultas (
    id         UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kode       TEXT NOT NULL UNIQUE,
    nama       TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE program_studi (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    fakultas_id UUID NOT NULL REFERENCES fakultas(id) ON DELETE RESTRICT,
    kode        TEXT NOT NULL UNIQUE,
    nama        TEXT NOT NULL,
    jenjang     TEXT NOT NULL CHECK (jenjang IN ('D3','D4','S1','S2','S3')),
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE mata_kuliah (
    id         UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    prodi_id   UUID NOT NULL REFERENCES program_studi(id) ON DELETE RESTRICT,
    kode       TEXT NOT NULL UNIQUE,
    nama       TEXT NOT NULL,
    sks        SMALLINT NOT NULL DEFAULT 3,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);
