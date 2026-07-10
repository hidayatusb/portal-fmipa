<?php

namespace App\Livewire\Admin\Pengumuman;

use App\Enums\AnnouncementContentType;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class Create extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public string $title = '';

    public string $content_type = 'text';

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

        Announcement::query()->create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'content_type' => $this->content_type,
            'body' => $this->body,
            'image_path' => $this->image->store('announcements', 'public'),
            'is_published' => $this->is_published,
            'published_at' => $this->is_published ? now() : null,
        ]);

        session()->flash('success', 'Pengumuman berhasil ditambahkan.');

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

        return [
            'title' => ['required', 'string', 'min:3', 'max:200'],
            'content_type' => ['required', Rule::enum(AnnouncementContentType::class)],
            'body' => $bodyRules,
            'image' => ['required', 'image', 'max:4096'],
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
            'pageTitle' => 'Tambah Pengumuman',
            'submitLabel' => 'Simpan',
            'existingImageUrl' => null,
            'imageRequired' => true,
        ]);
    }
}
