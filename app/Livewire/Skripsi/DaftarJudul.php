<?php

namespace App\Livewire\Skripsi;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\JudulSkripsi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class DaftarJudul extends Component
{
    use SetsBreadcrumbs;

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Daftar Judul Skripsi'],
        ]);
    }

    public function render(): View
    {
        return view('livewire.skripsi.daftar-judul', [
            'items' => JudulSkripsi::query()->latest()->get(),
        ]);
    }
}
