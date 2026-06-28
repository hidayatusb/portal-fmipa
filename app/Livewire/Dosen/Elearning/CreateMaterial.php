<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use App\Support\CourseStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class CreateMaterial extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public Course $course;

    public string $title = '';

    public string $type = 'document';

    public string $content = '';

    public $file = null;

    public function mount(Course $course): void
    {
        abort_unless($course->user_id === Auth::id(), 403);

        $this->course = $course;

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('dosen.elearning.index')],
            ['label' => $course->code, 'url' => route('dosen.elearning.show', $course)],
            ['label' => 'Tambah Materi'],
        ]);
    }

    public function updatedType(): void
    {
        $this->reset(['file', 'content']);
        $this->resetValidation(['file', 'content']);
    }

    public function removeFile(): void
    {
        $this->reset('file');
        $this->resetValidation('file');
    }

    public function save(): void
    {
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'type' => ['required', 'in:video,document,link'],
        ];

        if ($this->type === 'document') {
            $rules['file'] = [
                'required',
                'file',
                'max:10240',
            ];
        } else {
            $rules['content'] = ['required', 'string', 'url', 'max:2000'];
        }

        $this->validate($rules, [
            'title.required' => 'Judul materi wajib diisi.',
            'type.required' => 'Tipe materi wajib dipilih.',
            'file.required' => 'File dokumen wajib diunggah.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'content.required' => 'URL wajib diisi.',
            'content.url' => 'Format URL tidak valid.',
        ]);

        $filePath = null;
        $fileName = null;

        if ($this->type === 'document' && $this->file) {
            $fileName = $this->file->getClientOriginalName();
            $filePath = $this->file->store(
                CourseStorage::materialsDirectory($this->course),
                CourseStorage::diskName()
            );
        }

        $this->course->materials()->create([
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->type === 'document' ? null : $this->content,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'sort_order' => $this->course->materials()->count() + 1,
        ]);

        session()->flash('success', 'Materi berhasil ditambahkan.');

        $this->redirect(route('dosen.elearning.show', $this->course), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.dosen.elearning.create-material');
    }
}
