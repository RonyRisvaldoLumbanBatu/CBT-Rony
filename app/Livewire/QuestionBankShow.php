<?php

namespace App\Livewire;

use App\Models\BankOption;
use App\Models\BankQuestion;
use App\Models\QuestionBank;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuestionBankShow extends Component
{
    public QuestionBank $bank;

    // === VARIABEL UNTUK FITUR MANUAL & EDIT ===
    public $isModalOpen = false;

    public $isEditMode = false;

    public $questionIdToEdit = null;

    // Variabel Form
    public $question_text = '';

    public $type = 'pg'; // Type soal (pg atau essay dsb)

    public $opsi_a = '';

    public $opsi_b = '';

    public $opsi_c = '';

    public $opsi_d = '';

    public $jawaban_benar = 'A'; // Default A untuk PG Biasa

    public $jawaban_benar_kompleks = []; // Array untuk PG Kompleks

    public $jawaban_benar_bs = 'Benar'; // Default untuk Benar/Salah

    public $kunci_isian = ''; // Untuk isian singkat

    public function mount($id)
    {
        // Pastikan hanya guru pemilik yang bisa akses
        $this->bank = QuestionBank::where('id', $id)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['question_text', 'type', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'isEditMode', 'questionIdToEdit', 'jawaban_benar_kompleks', 'kunci_isian']);
        $this->jawaban_benar = 'A';
        $this->jawaban_benar_bs = 'Benar';
    }

    public function editQuestion($id)
    {
        $question = BankQuestion::with('options')->findOrFail($id);

        // Cek Keamanan
        if ($question->question_bank_id !== $this->bank->id) {
            abort(403);
        }

        $this->questionIdToEdit = $question->id;
        $this->question_text = $question->question_text;
        $this->type = $question->type;

        if ($this->type === 'pg' || $this->type === 'pg_kompleks') {
            $options = $question->options;
            if ($options->count() >= 4) {
                $this->opsi_a = $options[0]->option_text;
                $this->opsi_b = $options[1]->option_text;
                $this->opsi_c = $options[2]->option_text;
                $this->opsi_d = $options[3]->option_text;

                if ($this->type === 'pg') {
                    if ($options[0]->is_correct) {
                        $this->jawaban_benar = 'A';
                    } elseif ($options[1]->is_correct) {
                        $this->jawaban_benar = 'B';
                    } elseif ($options[2]->is_correct) {
                        $this->jawaban_benar = 'C';
                    } elseif ($options[3]->is_correct) {
                        $this->jawaban_benar = 'D';
                    }
                } else {
                    $this->jawaban_benar_kompleks = [];
                    if ($options[0]->is_correct) {
                        $this->jawaban_benar_kompleks[] = 'A';
                    }
                    if ($options[1]->is_correct) {
                        $this->jawaban_benar_kompleks[] = 'B';
                    }
                    if ($options[2]->is_correct) {
                        $this->jawaban_benar_kompleks[] = 'C';
                    }
                    if ($options[3]->is_correct) {
                        $this->jawaban_benar_kompleks[] = 'D';
                    }
                }
            }
        } elseif ($this->type === 'benar_salah') {
            $correctOption = $question->options->where('is_correct', true)->first();
            if ($correctOption) {
                $this->jawaban_benar_bs = $correctOption->option_text;
            }
        } elseif ($this->type === 'isian') {
            $correctOption = $question->options->where('is_correct', true)->first();
            if ($correctOption) {
                $this->kunci_isian = $correctOption->option_text;
            }
        }

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function saveQuestion()
    {
        $rules = [
            'question_text' => 'required|string',
            'type' => 'required|in:pg,essay,pg_kompleks,benar_salah,isian',
        ];

        if ($this->type === 'pg' || $this->type === 'pg_kompleks') {
            $rules['opsi_a'] = 'required|string';
            $rules['opsi_b'] = 'required|string';
            $rules['opsi_c'] = 'required|string';
            $rules['opsi_d'] = 'required|string';

            if ($this->type === 'pg') {
                $rules['jawaban_benar'] = 'required|in:A,B,C,D';
            } else {
                $rules['jawaban_benar_kompleks'] = 'required|array|min:1';
            }
        } elseif ($this->type === 'benar_salah') {
            $rules['jawaban_benar_bs'] = 'required|in:Benar,Salah';
        } elseif ($this->type === 'isian') {
            $rules['kunci_isian'] = 'required|string';
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            $question = BankQuestion::findOrFail($this->questionIdToEdit);
            $question->update([
                'question_text' => $this->question_text,
                'type' => $this->type,
            ]);

            if ($this->type !== 'essay') {
                $question->options()->delete();
            }
        } else {
            $question = BankQuestion::create([
                'question_bank_id' => $this->bank->id,
                'question_text' => $this->question_text,
                'type' => $this->type,
            ]);
        }

        if ($this->type === 'pg' || $this->type === 'pg_kompleks') {
            $pilihan = [
                'A' => $this->opsi_a,
                'B' => $this->opsi_b,
                'C' => $this->opsi_c,
                'D' => $this->opsi_d,
            ];

            foreach ($pilihan as $huruf => $teksOpsi) {
                $is_correct = false;
                if ($this->type === 'pg') {
                    $is_correct = ($this->jawaban_benar === $huruf);
                } else {
                    $is_correct = in_array($huruf, $this->jawaban_benar_kompleks);
                }

                BankOption::create([
                    'bank_question_id' => $question->id,
                    'option_text' => $teksOpsi,
                    'is_correct' => $is_correct,
                ]);
            }
        } elseif ($this->type === 'benar_salah') {
            BankOption::create([
                'bank_question_id' => $question->id,
                'option_text' => 'Benar',
                'is_correct' => ($this->jawaban_benar_bs === 'Benar'),
            ]);
            BankOption::create([
                'bank_question_id' => $question->id,
                'option_text' => 'Salah',
                'is_correct' => ($this->jawaban_benar_bs === 'Salah'),
            ]);
        } elseif ($this->type === 'isian') {
            BankOption::create([
                'bank_question_id' => $question->id,
                'option_text' => $this->kunci_isian,
                'is_correct' => true,
            ]);
        }

        $this->closeModal();
        session()->flash('sukses', $this->isEditMode ? 'Soal berhasil direvisi!' : 'Soal baru berhasil disimpan ke bank!');
    }

    public function deleteQuestion($id)
    {
        $q = BankQuestion::findOrFail($id);
        if ($q->question_bank_id === $this->bank->id) {
            $q->delete();
            session()->flash('sukses', 'Soal berhasil dihapus dari bank!');
        }
    }

    public function render()
    {
        $questions = BankQuestion::with('options')->where('question_bank_id', $this->bank->id)->latest()->get();

        return view('livewire.question-bank-show', compact('questions'));
    }
}
