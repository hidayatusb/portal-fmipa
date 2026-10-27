<?php

namespace App\Livewire\Skripsi;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\BimbinganSkripsi;
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

        $items = BimbinganSkripsi::query()
            ->with(['mahasiswa', 'pembimbing1', 'pembimbing2'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhereHas('mahasiswa', function ($mahasiswa) use ($search) {
                            $mahasiswa->where('name', 'like', "%{$search}%")
                                ->orWhere('username', 'like', "%{$search}%");
                        })
                        ->orWhereHas('pembimbing1', function ($dosen) use ($search) {
                            $dosen->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('pembimbing2', function ($dosen) use ($search) {
                            $dosen->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->get();

        return view('livewire.skripsi.daftar-judul', [
            'items' => $items,
        ]);
    }
}
