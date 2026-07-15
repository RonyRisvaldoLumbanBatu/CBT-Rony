<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Services\ExamGrader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamGraderTest extends TestCase
{
    use RefreshDatabase;

    private ExamGrader $grader;

    private Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grader = new ExamGrader;
        $this->exam = Exam::create([
            'title' => 'Ujian Testing',
            'description' => 'Untuk unit test',
            'time_limit' => 60,
        ]);
    }

    private function makeQuestion(string $type, array $options = []): Question
    {
        $question = Question::create([
            'exam_id' => $this->exam->id,
            'question_text' => 'Soal tipe '.$type,
            'type' => $type,
        ]);

        foreach ($options as [$text, $isCorrect]) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => $isCorrect,
            ]);
        }

        return $question->load('options');
    }

    // === isAnswerCorrect ===

    public function test_pg_benar_jika_memilih_opsi_yang_benar(): void
    {
        $q = $this->makeQuestion('pg', [['A', false], ['B', true], ['C', false], ['D', false]]);
        $kunci = $q->options->firstWhere('is_correct', true);
        $salah = $q->options->firstWhere('is_correct', false);

        $this->assertTrue($this->grader->isAnswerCorrect($q, $kunci->id));
        $this->assertTrue($this->grader->isAnswerCorrect($q, (string) $kunci->id)); // id dari form berupa string
        $this->assertFalse($this->grader->isAnswerCorrect($q, $salah->id));
    }

    public function test_benar_salah_dinilai_seperti_pg(): void
    {
        $q = $this->makeQuestion('benar_salah', [['Benar', false], ['Salah', true]]);
        $kunci = $q->options->firstWhere('is_correct', true);

        $this->assertTrue($this->grader->isAnswerCorrect($q, $kunci->id));
    }

    public function test_pg_kompleks_harus_memilih_semua_kunci_dengan_tepat(): void
    {
        $q = $this->makeQuestion('pg_kompleks', [['A', false], ['B', true], ['C', false], ['D', true]]);
        $kunciIds = $q->options->where('is_correct', true)->pluck('id')->all();

        // Tepat semua -> benar (urutan bebas, id string dari checkbox tetap dikenali)
        $this->assertTrue($this->grader->isAnswerCorrect($q, array_reverse($kunciIds)));
        $this->assertTrue($this->grader->isAnswerCorrect($q, array_map('strval', $kunciIds)));

        // Kurang satu, kelebihan, atau bukan array -> salah
        $this->assertFalse($this->grader->isAnswerCorrect($q, [$kunciIds[0]]));
        $this->assertFalse($this->grader->isAnswerCorrect($q, $q->options->pluck('id')->all()));
        $this->assertFalse($this->grader->isAnswerCorrect($q, $kunciIds[0]));
    }

    public function test_isian_mengabaikan_kapital_dan_spasi_pinggir(): void
    {
        $q = $this->makeQuestion('isian', [['Soekarno', true]]);

        $this->assertTrue($this->grader->isAnswerCorrect($q, 'soekarno'));
        $this->assertTrue($this->grader->isAnswerCorrect($q, '  SOEKARNO  '));
        $this->assertFalse($this->grader->isAnswerCorrect($q, 'Hatta'));
        $this->assertFalse($this->grader->isAnswerCorrect($q, ['soekarno'])); // bukan string -> salah
    }

    public function test_essay_tidak_pernah_dinilai_otomatis(): void
    {
        $q = $this->makeQuestion('essay');

        $this->assertFalse($this->grader->isAnswerCorrect($q, 'Jawaban panjang apapun'));
    }

    // === autoScore ===

    public function test_auto_score_menghitung_persentase_soal_otomatis_saja(): void
    {
        $pg = $this->makeQuestion('pg', [['A', true], ['B', false]]);
        $isian = $this->makeQuestion('isian', [['Jakarta', true]]);
        $this->makeQuestion('essay'); // essay tidak mempengaruhi pembagi

        $questions = $this->exam->questions()->with('options')->get();

        // 1 dari 2 soal otomatis benar (isian salah) -> 50
        $answers = [
            $pg->id => $pg->options->firstWhere('is_correct', true)->id,
            $isian->id => 'Bandung',
        ];
        $this->assertEquals(50.0, $this->grader->autoScore($questions, $answers));

        // Tidak menjawab dihitung salah
        $this->assertEquals(0.0, $this->grader->autoScore($questions, []));
    }

    public function test_auto_score_nol_jika_semua_soal_essay(): void
    {
        $this->makeQuestion('essay');
        $questions = $this->exam->questions()->with('options')->get();

        $this->assertEquals(0.0, $this->grader->autoScore($questions, []));
    }

    // === finalScore (3 skenario gabungan PG + Essay) ===

    public function test_final_score_gabungan_pg_dan_essay_dirata_rata(): void
    {
        $pg = $this->makeQuestion('pg', [['A', true], ['B', false]]);
        $essay = $this->makeQuestion('essay');
        $questions = $this->exam->questions()->with('options')->get();

        $answers = [$pg->id => $pg->options->firstWhere('is_correct', true)->id]; // PG = 100
        $essayScores = [$essay->id => 80];

        // (100 + 80) / 2 = 90
        $this->assertEquals(90, $this->grader->finalScore($questions, $answers, $essayScores));
    }

    public function test_final_score_hanya_pg(): void
    {
        $pg1 = $this->makeQuestion('pg', [['A', true], ['B', false]]);
        $this->makeQuestion('pg', [['A', true], ['B', false]]);
        $questions = $this->exam->questions()->with('options')->get();

        $answers = [$pg1->id => $pg1->options->firstWhere('is_correct', true)->id];

        // 1 dari 2 benar -> 50
        $this->assertEquals(50, $this->grader->finalScore($questions, $answers));
    }

    public function test_final_score_hanya_essay_dirata_rata(): void
    {
        $e1 = $this->makeQuestion('essay');
        $e2 = $this->makeQuestion('essay');
        $questions = $this->exam->questions()->with('options')->get();

        $this->assertEquals(75, $this->grader->finalScore($questions, [], [$e1->id => 100, $e2->id => 50]));
    }

    public function test_final_score_nol_jika_tidak_ada_soal(): void
    {
        $questions = $this->exam->questions()->with('options')->get();

        $this->assertEquals(0, $this->grader->finalScore($questions, []));
    }
}
