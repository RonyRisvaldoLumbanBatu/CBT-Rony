-- Bagian 6 — Pelaksanaan: attempt & jawaban

-- Satu attempt = satu percobaan ujian oleh satu mahasiswa.
-- deadline_efektif dihitung & DISIMPAN saat mulai (otoritatif server).
-- soal_order menyimpan tata letak acak per mahasiswa (konsisten saat resume).
CREATE TABLE exam_attempts (
    id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    exam_id          UUID NOT NULL REFERENCES exams(id) ON DELETE CASCADE,
    mahasiswa_id     UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    peserta_ujian_id UUID REFERENCES peserta_ujian(id) ON DELETE SET NULL,
    started_at       TIMESTAMPTZ,
    deadline_efektif TIMESTAMPTZ,
    submitted_at     TIMESTAMPTZ,
    status           TEXT NOT NULL DEFAULT 'belum_mulai'
                       CHECK (status IN ('belum_mulai','berlangsung','submitted','expired','dinilai')),
    soal_order       JSONB,
    total_score      NUMERIC(8,2),
    created_at       TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at       TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (exam_id, mahasiswa_id)
);

-- Jawaban per soal per attempt. Auto-save = UPSERT ke tabel ini.
-- is_correct/score NULL sampai dinilai (uraian & isian yang tak cocok otomatis).
CREATE TABLE answers (
    id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    attempt_id     UUID NOT NULL REFERENCES exam_attempts(id) ON DELETE CASCADE,
    question_id    UUID NOT NULL REFERENCES questions(id) ON DELETE RESTRICT,
    answer_content JSONB NOT NULL DEFAULT '{}',
    is_correct     BOOLEAN,
    score          NUMERIC(6,2),
    auto_graded    BOOLEAN NOT NULL DEFAULT false,
    graded_by      UUID REFERENCES users(id) ON DELETE SET NULL,
    graded_at      TIMESTAMPTZ,
    updated_at     TIMESTAMPTZ NOT NULL DEFAULT now(),
    UNIQUE (attempt_id, question_id)
);
