-- Bagian 4 — Bank soal (dipisah dari komposisi ujian)

CREATE TABLE kategori_soal (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    mata_kuliah_id UUID REFERENCES mata_kuliah(id) ON DELETE SET NULL,
    nama           TEXT NOT NULL,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Bank soal reusable. Kunci jawaban + opsi di kolom content (JSONB).
CREATE TABLE questions (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    kategori_id    UUID REFERENCES kategori_soal(id) ON DELETE SET NULL,
    mata_kuliah_id UUID REFERENCES mata_kuliah(id) ON DELETE SET NULL,
    type           TEXT NOT NULL CHECK (type IN (
                       'pilihan_ganda',
                       'pilihan_ganda_kompleks',
                       'benar_salah',
                       'menjodohkan',
                       'isian_singkat',
                       'uraian')),
    question_text  TEXT NOT NULL,
    media_url      TEXT,
    default_points NUMERIC(6,2) NOT NULL DEFAULT 1,
    content        JSONB NOT NULL DEFAULT '{}',
    created_by     UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now()
);
