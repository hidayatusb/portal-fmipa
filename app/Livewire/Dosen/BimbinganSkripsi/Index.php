<?php

namespace App\Livewire\Dosen\BimbinganSkripsi;

use App\Enums\BimbinganApprovalStatus;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\BimbinganSkripsi;
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
        abort_unless(Auth::user()?->isDosen(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Bimbingan Skripsi'],
        ]);
    }

    public function approve(int $bimbinganId): void
    {
        $this->respond($bimbinganId, BimbinganApprovalStatus::Approved, 'Permintaan bimbingan disetujui.');
    }

    public function reject(int $bimbinganId): void
    {
        $this->respond($bimbinganId, BimbinganApprovalStatus::Rejected, 'Permintaan bimbingan ditolak.');
    }

    public function render(): View
    {
        $dosenId = Auth::id();

        $requests = BimbinganSkripsi::query()
            ->with('mahasiswa')
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing1_id', $dosenId)
                    ->orWhere('pembimbing2_id', $dosenId);
            })
            ->latest()
            ->get();

        return view('livewire.dosen.bimbingan-skripsi.index', [
            'requests' => $requests,
            'dosenId' => $dosenId,
            'pendingCount' => $requests->filter(
                fn (BimbinganSkripsi $item) => $item->statusForDosen($dosenId) === BimbinganApprovalStatus::Pending
            )->count(),
        ]);
    }

    protected function respond(int $bimbinganId, BimbinganApprovalStatus $status, string $message): void
    {
        abort_unless(Auth::user()?->isDosen(), 403);

        $dosenId = Auth::id();

        $bimbingan = BimbinganSkripsi::query()
            ->whereKey($bimbinganId)
            ->where(function ($query) use ($dosenId) {
                $query->where('pembimbing1_id', $dosenId)
                    ->orWhere('pembimbing2_id', $dosenId);
            })
            ->firstOrFail();

        if ($bimbingan->statusForDosen($dosenId) !== BimbinganApprovalStatus::Pending) {
            session()->flash('error', 'Permintaan ini sudah Anda respons sebelumnya.');

            return;
        }

        $bimbingan->respondAsDosen($dosenId, $status);

        session()->flash('success', $message);
    }
}
