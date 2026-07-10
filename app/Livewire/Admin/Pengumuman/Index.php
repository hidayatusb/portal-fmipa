<?php

namespace App\Livewire\Admin\Pengumuman;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Pengumuman'],
        ]);
    }

    public function delete(int $announcementId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $announcement = Announcement::query()->whereKey($announcementId)->firstOrFail();
        $title = $announcement->title;
        $announcement->delete();

        session()->flash('success', "Pengumuman \"{$title}\" berhasil dihapus.");
    }

    public function togglePublish(int $announcementId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $announcement = Announcement::query()->whereKey($announcementId)->firstOrFail();

        if ($announcement->is_published) {
            $announcement->update([
                'is_published' => false,
            ]);
            session()->flash('success', "Pengumuman \"{$announcement->title}\" disembunyikan.");

            return;
        }

        $announcement->update([
            'is_published' => true,
            'published_at' => $announcement->published_at ?? now(),
        ]);

        session()->flash('success', "Pengumuman \"{$announcement->title}\" dipublikasikan.");
    }

    public function render(): View
    {
        return view('livewire.admin.pengumuman.index', [
            'announcements' => Announcement::query()
                ->with('author')
                ->latest('published_at')
                ->latest('id')
                ->get(),
        ]);
    }
}
