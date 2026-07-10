<?php

namespace App\Livewire\Admin\Pengumuman;

use App\Enums\AnnouncementContentType;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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

    public string $content_type = 'text';

    public string $body = '';

    public $image = null;

    public bool $is_published = true;

    public function mount(Announcement $announcement): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->announcement = $announcement;
        $this->title = $announcement->title;
        $this->content_type = $announcement->content_type->value;
        $this->body = $announcement->body;
        $this->is_published = $announcement->is_published;

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Pengumuman', 'url' => route('admin.pengumuman.index')],
            ['label' => 'Edit'],
        ]);
    }

    public function updatedContentType(): void
    {
        $this->resetValidation('body');
    }

    public function removeImage(): void
    {
        $this->reset('image');
        $this->resetValidation('image');
    }

    public function save(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->validate($this->rules(), $this->messages());

        $data = [
            'title' => $this->title,
            'content_type' => $this->content_type,
            'body' => $this->body,
            'is_published' => $this->is_published,
            'published_at' => $this->is_published
                ? ($this->announcement->published_at ?? now())
                : $this->announcement->published_at,
        ];

        if ($this->image) {
            if ($this->announcement->image_path) {
                Storage::disk('public')->delete($this->announcement->image_path);
            }

            $data['image_path'] = $this->image->store('announcements', 'public');
        }

        $this->announcement->update($data);

        session()->flash('success', 'Pengumuman berhasil diperbarui.');

        $this->redirect(route('admin.pengumuman.index'), navigate: true);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $bodyRules = $this->content_type === AnnouncementContentType::Url->value
            ? ['required', 'url', 'max:2000']
            : ['required', 'string', 'min:3'];

        $imageRules = $this->announcement->hasImage()
            ? ['nullable', 'image', 'max:4096']
            : ['required', 'image', 'max:4096'];

        return [
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'content_type' => ['required', Rule::enum(AnnouncementContentType::class)],
            'body' => $bodyRules,
            'image' => $imageRules,
            'is_published' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.min' => 'Judul minimal 3 karakter.',
            'content_type.required' => 'Tipe konten wajib dipilih.',
            'body.required' => $this->content_type === AnnouncementContentType::Url->value
                ? 'URL wajib diisi.'
                : 'Isi pengumuman wajib diisi.',
            'body.url' => 'Format URL tidak valid.',
            'body.min' => 'Isi pengumuman minimal 3 karakter.',
            'image.required' => 'Gambar wajib diunggah.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 4 MB.',
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.pengumuman.form', [
            'pageTitle' => 'Edit Pengumuman',
            'submitLabel' => 'Perbarui',
            'existingImageUrl' => $this->announcement->hasImage()
                ? $this->announcement->imageUrl()
                : null,
            'imageRequired' => ! $this->announcement->hasImage(),
        ]);
    }
}
