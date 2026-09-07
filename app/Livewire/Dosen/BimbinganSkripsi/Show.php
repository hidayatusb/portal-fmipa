<?php

namespace App\Livewire\Dosen\BimbinganSkripsi;

use App\Enums\BimbinganApprovalStatus;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\BimbinganSkripsi;
use App\Models\LogbookBimbinganSkripsi;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Show extends Component
{
    use SetsBreadcrumbs;

    public BimbinganSkripsi $bimbingan;

    public bool $showLogbookForm = false;

    public ?int $editingLogbookId = null;

    public string $tanggal = '';

    public string $judul = '';

    public string $catatan = '';

    public function mount(BimbinganSkripsi $bimbingan): void
    {
        abort_unless(Auth::user()?->isDosen(), 403);

        $dosenId = Auth::id();

        abort_unless(
            $bimbingan->pembimbing1_id === $dosenId || $bimbingan->pembimbing2_id === $dosenId,
            403,
        );

        $this->bimbingan = $bimbingan->load(['mahasiswa', 'pembimbing1', 'pembimbing2']);
        $this->tanggal = now()->toDateString();

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Bimbingan Skripsi', 'url' => route('dosen.bimbingan-skripsi.index')],
            ['label' => $this->bimbingan->mahasiswa?->name ?? 'Detail'],
        ]);
    }

    public function openLogbookForm(): void
    {
        $this->requireLogbookAccess();
        $this->resetLogbookForm();
        $this->showLogbookForm = true;
    }

    public function editLogbook(int $logbookId): void
    {
        $logbook = $this->ownLogbook($logbookId);

        $this->editingLogbookId = $logbook->id;
        $this->tanggal = $logbook->tanggal->toDateString();
        $this->judul = $logbook->judul ?? '';
        $this->catatan = $logbook->catatan ?? '';
        $this->showLogbookForm = true;
        $this->resetValidation();
    }

    public function cancelLogbookForm(): void
    {
        $this->resetLogbookForm();
    }

    public function saveLogbook(): void
    {
        $this->requireLogbookAccess();

        $this->catatan = $this->sanitizeCatatan($this->catatan);

        $this->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'judul' => ['required', 'string', 'max:255'],
            'catatan' => ['required', 'string', 'max:20000'],
        ], [
            'tanggal.required' => 'Tanggal bimbingan wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal bimbingan tidak boleh melebihi hari ini.',
            'judul.required' => 'Judul logbook wajib diisi.',
            'catatan.required' => 'Catatan bimbingan wajib diisi.',
        ]);

        if (trim(strip_tags($this->catatan)) === '') {
            $this->addError('catatan', 'Catatan bimbingan wajib diisi.');

            return;
        }

        $payload = [
            'pembimbing_id' => Auth::id(),
            'tanggal' => $this->tanggal,
            'judul' => trim($this->judul),
            'catatan' => $this->catatan,
            'status' => BimbinganApprovalStatus::Approved,
            'status_responded_at' => now(),
        ];

        if ($this->editingLogbookId) {
            $this->ownLogbook($this->editingLogbookId)->update($payload);
            session()->flash('success', 'Logbook bimbingan berhasil diperbarui.');
        } else {
            $this->bimbingan->logbooks()->create($payload);
            session()->flash('success', 'Logbook bimbingan berhasil ditambahkan.');
        }

        $this->resetLogbookForm();
    }

    public function deleteLogbook(int $logbookId): void
    {
        $this->ownLogbook($logbookId)->delete();

        if ($this->editingLogbookId === $logbookId) {
            $this->resetLogbookForm();
        }

        session()->flash('success', 'Logbook bimbingan berhasil dihapus.');
    }

    public function approveLogbook(int $logbookId): void
    {
        $this->respondLogbook($logbookId, BimbinganApprovalStatus::Approved, 'Logbook berhasil disetujui.');
    }

    public function rejectLogbook(int $logbookId): void
    {
        $this->respondLogbook($logbookId, BimbinganApprovalStatus::Rejected, 'Logbook ditolak. Mahasiswa dapat memperbaiki dan mengirim ulang.');
    }

    public function render(): View
    {
        $dosenId = Auth::id();
        $canManageLogbook = $this->bimbingan->statusForDosen($dosenId) === BimbinganApprovalStatus::Approved;

        $logbooks = $this->bimbingan
            ->logbooks()
            ->where('pembimbing_id', $dosenId)
            ->with('pembimbing')
            ->latest('tanggal')
            ->latest('id')
            ->get();

        return view('livewire.dosen.bimbingan-skripsi.show', [
            'dosenId' => $dosenId,
            'canManageLogbook' => $canManageLogbook,
            'logbooks' => $logbooks,
        ]);
    }

    protected function respondLogbook(int $logbookId, BimbinganApprovalStatus $status, string $message): void
    {
        $logbook = $this->ownLogbook($logbookId);

        $logbook->update([
            'status' => $status,
            'status_responded_at' => now(),
        ]);

        session()->flash('success', $message);
    }

    protected function requireLogbookAccess(): void
    {
        abort_unless(Auth::user()?->isDosen(), 403);
        abort_unless(
            $this->bimbingan->statusForDosen(Auth::id()) === BimbinganApprovalStatus::Approved,
            403,
        );
    }

    protected function ownLogbook(int $logbookId): LogbookBimbinganSkripsi
    {
        $this->requireLogbookAccess();

        return $this->bimbingan
            ->logbooks()
            ->where('pembimbing_id', Auth::id())
            ->whereKey($logbookId)
            ->firstOrFail();
    }

    protected function resetLogbookForm(): void
    {
        $this->reset([
            'editingLogbookId',
            'judul',
            'catatan',
            'showLogbookForm',
        ]);
        $this->tanggal = now()->toDateString();
        $this->resetValidation();
    }

    protected function sanitizeCatatan(string $html): string
    {
        $allowed = '<div><p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><a><span><blockquote><pre><code>';

        return trim(strip_tags($html, $allowed));
    }
}
