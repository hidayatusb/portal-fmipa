<?php

namespace App\Livewire\Skripsi;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\JudulSkripsi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.demo1.base')]
class DaftarJudul extends Component
{
    use SetsBreadcrumbs;
    use WithPagination;

    #[Url]
    public string $search = '';

    public int $perPage = 25;

    #[Url]
    public string $sortField = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    /** @var list<string> */
    protected array $sortable = [
        'nim',
        'nama_mahasiswa',
        'judul',
        'pembimbing1',
        'created_at',
    ];

    public function mount(): void
    {
        abort_unless(Auth::check(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Daftar Judul Skripsi'],
        ]);
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, $this->sortable, true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $search = trim($this->search);
        $sortField = in_array($this->sortField, $this->sortable, true)
            ? $this->sortField
            : 'created_at';
        $sortDirection = $this->sortDirection === 'asc' ? 'asc' : 'desc';

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
            ->orderBy($sortField, $sortDirection)
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.skripsi.daftar-judul', [
            'items' => $items,
        ]);
    }
}
