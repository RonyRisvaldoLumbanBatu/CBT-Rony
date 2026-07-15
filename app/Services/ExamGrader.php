<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Collection;

/**
 * Satu-satunya sumber kebenaran untuk logika penilaian ujian.
 * Dipakai oleh TakeExam (submit), GradeExam (rekalkulasi), dan ItemAnalysis (statistik).
 *
 * Semua method bekerja dari relasi options yang sudah di-eager-load,
 * jadi tidak ada query tambahan per soal/per siswa.
 */
class ExamGrader
{
    // Tipe soal yang dinilai otomatis oleh sistem (selain essay)
    public const AUTO_GRADED_TYPES = ['pg', 'pg_kompleks', 'benar_salah', 'isian'];

    /**
     * Cek apakah jawaban siswa untuk satu soal benar.
     * Soal essay selalu false di sini karena dinilai manual oleh guru.
     */
    public function isAnswerCorrect(Question $question, mixed $answer): bool
    {
        return match ($question->type) {
            'pg', 'benar_salah' => $question->options
                ->contains(fn ($opt) => $opt->is_correct && (int) $opt->id === (int) $answer),

            'pg_kompleks' => is_array($answer) && $this->matchesAllCorrectOptions($question, $answer),

            'isian' => is_string($answer) && $this->matchesAnswerKey($question, $answer),

            default => false,
        };
    }

    /**
     * Hitung jumlah jawaban benar dari kumpulan soal.
     *
     * @param  Collection<int, Question>  $questions
     * @param  array<int|string, mixed>  $answers  [question_id => jawaban siswa]
     */
    public function countCorrect(Collection $questions, array $answers): int
    {
        return $questions
            ->filter(fn ($q) => array_key_exists($q->id, $answers)
                && $this->isAnswerCorrect($q, $answers[$q->id]))
            ->count();
    }

    /**
     * Skor 0-100 untuk soal yang dinilai otomatis saja (essay diabaikan).
     */
    public function autoScore(Collection $questions, array $answers): float
    {
        $autoQuestions = $questions->whereIn('type', self::AUTO_GRADED_TYPES);

        if ($autoQuestions->isEmpty()) {
            return 0.0;
        }

        return ($this->countCorrect($autoQuestions, $answers) / $autoQuestions->count()) * 100;
    }

    /**
     * Nilai akhir gabungan (dibulatkan 0-100).
     *
     * Skenario A: Ada soal otomatis dan essay -> rata-rata keduanya
     * Skenario B: Hanya soal otomatis        -> skor otomatis
     * Skenario C: Hanya essay                -> rata-rata skor essay
     *
     * @param  array<int|string, int|float|string>  $essayScores  [question_id => skor 0-100]
     */
    public function finalScore(Collection $questions, array $answers, array $essayScores = []): int
    {
        $hasAuto = $questions->whereIn('type', self::AUTO_GRADED_TYPES)->isNotEmpty();
        $totalEssay = $questions->where('type', 'essay')->count();

        $autoScore = $this->autoScore($questions, $answers);
        $essayScore = $totalEssay > 0 ? array_sum($essayScores) / $totalEssay : 0;

        if ($hasAuto && $totalEssay > 0) {
            return (int) round(($autoScore + $essayScore) / 2);
        }

        if ($hasAuto) {
            return (int) round($autoScore);
        }

        return (int) round($essayScore);
    }

    private function matchesAllCorrectOptions(Question $question, array $answer): bool
    {
        $chosenIds = collect($answer)->map(fn ($id) => (int) $id)->sort()->values()->all();
        $correctIds = $question->options
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        return $correctIds !== [] && $chosenIds === $correctIds;
    }

    private function matchesAnswerKey(Question $question, string $answer): bool
    {
        $key = $question->options->firstWhere('is_correct', true);

        return $key !== null
            && strtolower(trim($answer)) === strtolower(trim($key->option_text));
    }
}
