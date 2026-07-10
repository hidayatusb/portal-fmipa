<?php

namespace App\Livewire\Admin\Pengumuman;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class Edit extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public Announcement $announcement;

    public string $title = '';

    public string $body = '';

    public $image = null;

    public bool $is_published = true;

    public bool $removeExistingImage = false;

    public function mount(Announcement $announcement): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->announcement = $announcement;
        $this->title = $announcement->title;
        $this->body = $announcement->body;
        $this->is_published = $announcement->is_published;

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => 'Edit'],
        ]);
    }

    public function removeImage(): void
    {
        $this->reset('image');
        $this->resetValidation('image');
    }

    public function markRemoveExistingImage(): void
    {
        $this->removeExistingImage = true;
        $this->reset('image');
    }

    public function undoRemoveExistingImage(): void
    {
        $this->removeExistingImage = false;
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

        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'is_published' => $this->is_published,
            'published_at' => $this->is_published
                ? ($this->announcement->published_at ?? now())
                : $this->announcement->published_at,
        ];

        if ($this->removeExistingImage && $this->announcement->image_path) {
            Storage::disk('public')->delete($this->announcement->image_path);
            $data['image_path'] = null;
        } elseif ($this->image) {
            if ($this->announcement->image_path) {
                Storage::disk('public')->delete($this->announcement->image_path);
            }

            $data['image_path'] = $this->image->store('announcements', 'public');
        }

        $this->announcement->update($data);

        session()->flash('success', 'Pengumuman berhasil diperbarui.');

        $this->redirect(route('admin.pengumuman.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.pengumuman.form', [
            'pageTitle' => 'Edit Pengumuman',
            'submitLabel' => 'Perbarui',
            'existingImageUrl' => (! $this->removeExistingImage && $this->announcement->hasImage())
                ? $this->announcement->imageUrl()
                : null,
        ]);
    }
}
