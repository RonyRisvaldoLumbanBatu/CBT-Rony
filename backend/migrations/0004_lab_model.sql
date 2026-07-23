-- Bagian 3 — Master data: jenis ujian, ruang, sesi (model lab)

CREATE TABLE jenis_ujian (
    id    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama  TEXT NOT NULL UNIQUE,
    bobot NUMERIC(5,2)
);

CREATE TABLE ruang (
    id        UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kode      TEXT NOT NULL UNIQUE,
    nama      TEXT NOT NULL,
    lokasi    TEXT,
    kapasitas INTEGER NOT NULL
);

CREATE TABLE sesi (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nama        TEXT NOT NULL,
    jam_mulai   TIME NOT NULL,
    jam_selesai TIME NOT NULL
);
