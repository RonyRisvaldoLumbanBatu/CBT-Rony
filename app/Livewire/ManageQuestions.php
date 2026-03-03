<?php

namespace App\Livewire;

use App\Imports\QuestionsImport;
use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class ManageQuestions extends Component
{
    use WithFileUploads;

    public Exam $exam;

    public $file;

    // === VARIABEL UNTUK FITUR MANUAL & EDIT ===
    public $isModalOpen = false;

    public $isEditMode = false;

    public $questionIdToEdit = null;

    // Variabel Form
    public $question_text = '';

    public $type = 'pg'; // Type soal (pg atau essay)

    public $opsi_a = '';

    public $opsi_b = '';

    public $opsi_c = '';

    public $opsi_d = '';

    public $jawaban_benar = 'A'; // Default A untuk PG Biasa

    // Tambahan variable untuk tipe soal lain
    public $jawaban_benar_kompleks = []; // Array untuk PG Kompleks

    // Variabel Tambahan Media
    public $image;

    public $existing_image = null;

    public $youtube_url = '';

    public function mount($id)
    {
        $this->exam = Exam::findOrFail($id);
    }

    // === FITUR DOWNLOAD TEMPLATE ===
    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\TemplateExport, 'Template_Soal_Ujian.xlsx');
    }

    // === FITUR IMPORT EXCEL ===
    public function importExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        Excel::import(new QuestionsImport($this->exam->id), $this->file);
        $this->reset('file');
        session()->flash('sukses', 'Sihir berhasil! Ratusan soal sukses diimport!');
    }

    // === FITUR BUKA TUTUP MODAL ===
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
        $this->reset(['question_text', 'type', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'isEditMode', 'questionIdToEdit', 'jawaban_benar_kompleks', 'kunci_isian', 'image', 'existing_image', 'youtube_url']);
        $this->jawaban_benar = 'A';
        $this->jawaban_benar_bs = 'Benar';
    }

    // === FITUR KLIK EDIT ===
    public function editQuestion($id)
    {
        $question = Question::with('options')->findOrFail($id);
        $this->questionIdToEdit = $question->id;
        $this->question_text = $question->question_text;
        $this->type = $question->type;
        $this->existing_image = $question->image_path;
        $this->youtube_url = $question->youtube_url;

        if ($this->type === 'pg' || $this->type === 'pg_kompleks') {
            // Asumsi selalu ada 4 opsi berurutan
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

    // === FITUR SIMPAN DATA (CREATE & UPDATE) ===
    public function saveQuestion()
    {
        $rules = [
            'question_text' => 'required|string',
            'type' => 'required|in:pg,essay,pg_kompleks,benar_salah,isian',
            'image' => 'nullable|image|max:2048',
            'youtube_url' => 'nullable|url',
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

        $dataToSave = [
            'question_text' => $this->question_text,
            'type' => $this->type,
            'youtube_url' => $this->youtube_url,
        ];

        if ($this->image) {
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }
            $dataToSave['image_path'] = $this->image->store('questions', 'public');
        }

        if ($this->isEditMode) {
            // Update Soal Lama
            $question = Question::findOrFail($this->questionIdToEdit);
            $question->update($dataToSave);

            // Hapus opsi lama, kita buat ulang jika bukan pure text essay
            if ($this->type !== 'essay') {
                $question->options()->delete();
            }
        } else {
            $dataToSave['exam_id'] = $this->exam->id;
            // Buat Soal Baru
            $question = Question::create($dataToSave);
        }

        // Masukkan Opsi ke Database jika type bukan essay
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

                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $teksOpsi,
                    'is_correct' => $is_correct,
                ]);
            }
        } elseif ($this->type === 'benar_salah') {
            Option::create([
                'question_id' => $question->id,
                'option_text' => 'Benar',
                'is_correct' => ($this->jawaban_benar_bs === 'Benar'),
            ]);
            Option::create([
                'question_id' => $question->id,
                'option_text' => 'Salah',
                'is_correct' => ($this->jawaban_benar_bs === 'Salah'),
            ]);
        } elseif ($this->type === 'isian') {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $this->kunci_isian,
                'is_correct' => true,
            ]);
        }

        $this->closeModal();
        session()->flash('sukses', $this->isEditMode ? 'Soal berhasil direvisi!' : 'Soal baru berhasil ditambahkan manual!');
    }

    public function deleteQuestion($id)
    {
        Question::findOrFail($id)->delete();
        session()->flash('sukses', 'Soal berhasil dihapus!');
    }

    public function render()
    {
        $questions = Question::with('options')->where('exam_id', $this->exam->id)->latest()->get();

        return view('livewire.manage-questions', compact('questions'));
    }
}
