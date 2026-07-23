-- Bagian 5 — Ujian & komposisi

CREATE TABLE exams (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    judul             TEXT NOT NULL,
    deskripsi         TEXT,
    mata_kuliah_id    UUID NOT NULL REFERENCES mata_kuliah(id) ON DELETE RESTRICT,
    tahun_akademik_id UUID NOT NULL REFERENCES tahun_akademik(id) ON DELETE RESTRICT,
    jenis_ujian_id    UUID NOT NULL REFERENCES jenis_ujian(id) ON DELETE RESTRICT,
    created_by        UUID REFERENCES users(id) ON DELETE SET NULL,
    durasi_menit      INTEGER NOT NULL,
    waktu_mulai       TIMESTAMPTZ NOT NULL,
    waktu_selesai     TIMESTAMPTZ NOT NULL,
    acak_soal         BOOLEAN NOT NULL DEFAULT true,
    acak_opsi         BOOLEAN NOT NULL DEFAULT true,
    tampilkan_hasil   BOOLEAN NOT NULL DEFAULT false,
    status            TEXT NOT NULL DEFAULT 'draft'
                        CHECK (status IN ('draft','dijadwalkan','berlangsung','selesai')),
    created_at        TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at        TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Kelas mana saja yang mengikuti ujian ini.
CREATE TABLE exam_kelas (
    exam_id  UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    kelas_id UUID NOT NULL REFERENCES kelas(id) ON DELETE CASCADE,
    PRIMARY KEY (exam_id, kelas_id)
);

-- Penugasan ujian ke ruang + sesi pada tanggal tertentu.
CREATE TABLE exam_sesi (
    id                    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    exam_id               UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    ruang_id              UUID NOT NULL REFERENCES ruang(id) ON DELETE RESTRICT,
    sesi_id               UUID NOT NULL REFERENCES sesi(id) ON DELETE RESTRICT,
    tanggal               DATE NOT NULL,
    token                 TEXT,
    kapasitas             INTEGER,
    waktu_mulai_efektif   TIMESTAMPTZ,
    waktu_selesai_efektif TIMESTAMPTZ,
    created_at            TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (exam_id, ruang_id, sesi_id, tanggal)
);

-- Penempatan peserta: mahasiswa -> sesi/ruang + nomor peserta (+ kursi).
CREATE TABLE peserta_ujian (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    exam_sesi_id  UUID NOT NULL REFERENCES exam_sesi(id) ON DELETE CASCADE,
    mahasiswa_id  UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    nomor_peserta TEXT,
    kursi         TEXT,
    status        TEXT NOT NULL DEFAULT 'terdaftar'
                    CHECK (status IN ('terdaftar','hadir','tidak_hadir')),
    UNIQUE (exam_sesi_id, nomor_peserta),
    UNIQUE (exam_sesi_id, mahasiswa_id)
);

-- Komposisi ujian: soal dari bank + urutan + bobot khusus ujian ini.
CREATE TABLE exam_questions (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    exam_id     UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    question_id UUID NOT NULL REFERENCES questions(id) ON DELETE RESTRICT,
    urutan      INTEGER NOT NULL,
    points      NUMERIC(6,2) NOT NULL DEFAULT 1,
    UNIQUE (exam_id, question_id),
    UNIQUE (exam_id, urutan)
);
