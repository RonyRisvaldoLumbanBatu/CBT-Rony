-- Bagian 7 — Index & trigger

CREATE INDEX idx_enrollment_kelas      ON enrollment(kelas_id);
CREATE INDEX idx_enrollment_mahasiswa  ON enrollment(mahasiswa_id);
CREATE INDEX idx_kelas_matkul          ON kelas(mata_kuliah_id);
CREATE INDEX idx_questions_matkul      ON questions(mata_kuliah_id);
CREATE INDEX idx_questions_kategori    ON questions(kategori_id);
CREATE INDEX idx_questions_type        ON questions(type);
CREATE INDEX idx_exam_questions_exam   ON exam_questions(exam_id);
CREATE INDEX idx_exam_sesi_exam        ON exam_sesi(exam_id);
CREATE INDEX idx_peserta_sesi          ON peserta_ujian(exam_sesi_id);
CREATE INDEX idx_peserta_mahasiswa     ON peserta_ujian(mahasiswa_id);
CREATE INDEX idx_attempts_exam         ON exam_attempts(exam_id);
CREATE INDEX idx_attempts_mahasiswa    ON exam_attempts(mahasiswa_id);
CREATE INDEX idx_attempts_status       ON exam_attempts(status);
-- Untuk job auto-submit: cari attempt berjalan yang mendekati deadline.
CREATE INDEX idx_attempts_deadline     ON exam_attempts(deadline_efektif)
                                       WHERE status = 'berlangsung';
CREATE INDEX idx_answers_attempt       ON answers(attempt_id);
-- Untuk antrean penilaian manual: jawaban yang belum dinilai.
CREATE INDEX idx_answers_belum_dinilai ON answers(attempt_id)
                                       WHERE score IS NULL;

-- Trigger updated_at pada tabel yang sering berubah.
CREATE TRIGGER trg_users_updated     BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_questions_updated BEFORE UPDATE ON questions
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_exams_updated     BEFORE UPDATE ON exams
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_attempts_updated  BEFORE UPDATE ON exam_attempts
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
CREATE TRIGGER trg_answers_updated   BEFORE UPDATE ON answers
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
