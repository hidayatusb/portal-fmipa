<?php

namespace App\Livewire\Pengumuman;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;
    use WithPagination;

    public function mount(): void
    {
        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Pengumuman'],
        ]);
    }

    public function render(): View
    {
        return view('livewire.pengumuman.index', [
            'announcements' => Announcement::query()
                ->published()
                ->with('author')
                ->latest('published_at')
                ->paginate(10),
        ]);
    }
}
