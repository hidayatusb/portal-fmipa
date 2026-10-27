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

    public string $search = '';

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
        $search = trim($this->search);

        $items = JudulSkripsi::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('nim', 'like', "%{$search}%")
                        ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                        ->orWhere('judul', 'like', "%{$search}%")
                        ->orWhere('pembimbing1', 'like', "%{$search}%")
                        ->orWhere('pembimbing2', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('livewire.skripsi.daftar-judul', [
            'items' => $items,
        ]);
    }
}
