<?php

namespace App\Livewire;

use App\Models\Classroom;
use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageClassrooms extends Component
{
    public $name = '';

    public $editingId = null;

    public $editingName = '';

    public function createClassroom()
    {
        $this->validate(['name' => 'required|min:2|max:50|unique:classrooms,name']);

        Classroom::create(['name' => $this->name]);
        $this->reset('name');
        session()->flash('sukses', term('kelas').' baru berhasil dibuat!');
    }

    public function startEdit($id)
    {
        $classroom = Classroom::findOrFail($id);
        $this->editingId = $classroom->id;
        $this->editingName = $classroom->name;
    }

    public function saveEdit()
    {
        $this->validate(['editingName' => 'required|min:2|max:50|unique:classrooms,name,'.$this->editingId]);

        Classroom::findOrFail($this->editingId)->update(['name' => $this->editingName]);
        $this->reset('editingId', 'editingName');
        session()->flash('sukses', 'Nama '.strtolower(term('kelas')).' berhasil diubah!');
    }

    public function cancelEdit()
    {
        $this->reset('editingId', 'editingName');
    }

    public function deleteClassroom($id)
    {
        // FK nullOnDelete: siswa & ujian kelas ini otomatis jadi "tanpa kelas"
        Classroom::findOrFail($id)->delete();
        session()->flash('sukses', term('kelas').' berhasil dihapus. Anggotanya menjadi tanpa '.strtolower(term('kelas')).'.');
    }

    public function setMode($mode)
    {
        if (! in_array($mode, ['sekolah', 'kampus'])) {
            return;
        }

        Setting::set('institution_mode', $mode);
        session()->flash('sukses', 'Mode aplikasi diubah ke: '.ucfirst($mode).'.');
    }

    public function render()
    {
        $classrooms = Classroom::withCount('students')->orderBy('name')->get();

        return view('livewire.manage-classrooms', [
            'classrooms' => $classrooms,
            'currentMode' => app_mode(),
        ]);
    }
}
