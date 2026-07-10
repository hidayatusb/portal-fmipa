<?php

namespace App\Livewire\Admin\Pengumuman;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class Create extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public string $title = '';

    public string $body = '';

    public $image = null;

    public bool $is_published = true;

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => 'Tambah'],
        ]);
    }

    public function removeImage(): void
    {
        $this->reset('image');
        $this->resetValidation('image');
    }

    public function save(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'body' => ['required', 'string', 'min:3'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['boolean'],
        ], [
            'title.required' => 'Judul wajib diisi.',
            'title.min' => 'Judul minimal 3 karakter.',
            'body.required' => 'Isi pengumuman wajib diisi.',
            'body.min' => 'Isi pengumuman minimal 3 karakter.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('announcements', 'public');
        }

        Announcement::query()->create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'body' => $this->body,
            'image_path' => $imagePath,
            'is_published' => $this->is_published,
            'published_at' => $this->is_published ? now() : null,
        ]);

        session()->flash('success', 'Pengumuman berhasil ditambahkan.');

        $this->redirect(route('admin.pengumuman.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.pengumuman.form', [
            'pageTitle' => 'Tambah Pengumuman',
            'submitLabel' => 'Simpan',
            'existingImageUrl' => null,
        ]);
    }
}
