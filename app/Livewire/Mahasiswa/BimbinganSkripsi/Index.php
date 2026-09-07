<?php

namespace App\Livewire\Mahasiswa\BimbinganSkripsi;

use App\Enums\BimbinganApprovalStatus;
use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\BimbinganSkripsi;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public ?int $pembimbing1_id = null;

    public ?int $pembimbing2_id = null;

    public bool $showLogbookForm = false;

    public ?int $editingLogbookId = null;

    public string $tanggal = '';

    public ?int $logbook_pembimbing_id = null;

    public string $judul = '';

    public string $catatan = '';

    public function mount(): void
    {
        abort_unless(Auth::user()?->isMahasiswa(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Bimbingan Skripsi'],
        ]);

        $this->tanggal = now()->toDateString();

        $bimbingan = $this->currentBimbingan();

        if ($bimbingan && $bimbingan->canBeResubmitted()) {
            $this->pembimbing1_id = $bimbingan->pembimbing1_id;
            $this->pembimbing2_id = $bimbingan->pembimbing2_id;
        }
    }

    public function submit(): void
    {
        abort_unless(Auth::user()?->isMahasiswa(), 403);

        $bimbingan = $this->currentBimbingan();

        if ($bimbingan && ! $bimbingan->canBeResubmitted()) {
            session()->flash('error', 'Permintaan bimbingan sudah diajukan dan sedang diproses.');

            return;
        }

        $dosenIds = $this->dosenOptions()->pluck('id')->all();

        $this->validate([
            'pembimbing1_id' => ['required', 'integer', Rule::in($dosenIds)],
            'pembimbing2_id' => [
                'required',
                'integer',
                Rule::in($dosenIds),
                'different:pembimbing1_id',
            ],
        ], [
            'pembimbing1_id.required' => 'Pembimbing 1 wajib dipilih.',
            'pembimbing2_id.required' => 'Pembimbing 2 wajib dipilih.',
            'pembimbing2_id.different' => 'Pembimbing 1 dan Pembimbing 2 harus berbeda.',
        ]);

        $payload = [
            'mahasiswa_id' => Auth::id(),
            'pembimbing1_id' => $this->pembimbing1_id,
            'pembimbing2_id' => $this->pembimbing2_id,
            'pembimbing1_status' => BimbinganApprovalStatus::Pending,
            'pembimbing2_status' => BimbinganApprovalStatus::Pending,
            'pembimbing1_responded_at' => null,
            'pembimbing2_responded_at' => null,
        ];

        if ($bimbingan) {
            $bimbingan->update($payload);
        } else {
            BimbinganSkripsi::create($payload);
        }

        session()->flash('success', 'Permintaan bimbingan skripsi berhasil diajukan. Menunggu persetujuan dosen pembimbing.');
    }

    public function openLogbookForm(): void
    {
        $this->resetLogbookForm();
        $this->showLogbookForm = true;
    }

    public function editLogbook(int $logbookId): void
    {
        $bimbingan = $this->requireLogbookAccess();
        $logbook = $bimbingan->logbooks()->whereKey($logbookId)->firstOrFail();

        if (! $logbook->mahasiswaCanModify()) {
            session()->flash('error', 'Logbook yang sudah disetujui tidak dapat diedit. Minta dosen pembimbing menolak/membuka ulang terlebih dahulu.');

            return;
        }

        $this->editingLogbookId = $logbook->id;
        $this->tanggal = $logbook->tanggal->toDateString();
        $this->logbook_pembimbing_id = $logbook->pembimbing_id;
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
        $bimbingan = $this->requireLogbookAccess()->load(['pembimbing1', 'pembimbing2']);
        $approvedIds = collect($bimbingan->approvedPembimbingOptions())->pluck('id')->all();

        $this->catatan = $this->sanitizeCatatan($this->catatan);

        $this->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'logbook_pembimbing_id' => ['required', 'integer', Rule::in($approvedIds)],
            'judul' => ['required', 'string', 'max:255'],
            'catatan' => ['required', 'string', 'max:20000'],
        ], [
            'tanggal.required' => 'Tanggal bimbingan wajib diisi.',
            'tanggal.before_or_equal' => 'Tanggal bimbingan tidak boleh melebihi hari ini.',
            'logbook_pembimbing_id.required' => 'Dosen pembimbing wajib dipilih.',
            'judul.required' => 'Judul logbook wajib diisi.',
            'catatan.required' => 'Catatan bimbingan wajib diisi.',
        ]);

        if (trim(strip_tags($this->catatan)) === '') {
            $this->addError('catatan', 'Catatan bimbingan wajib diisi.');

            return;
        }

        $payload = [
            'pembimbing_id' => $this->logbook_pembimbing_id,
            'tanggal' => $this->tanggal,
            'judul' => trim($this->judul),
            'catatan' => $this->catatan,
            'status' => BimbinganApprovalStatus::Pending,
            'status_responded_at' => null,
        ];

        if ($this->editingLogbookId) {
            $logbook = $bimbingan->logbooks()->whereKey($this->editingLogbookId)->firstOrFail();

            if (! $logbook->mahasiswaCanModify()) {
                session()->flash('error', 'Logbook yang sudah disetujui tidak dapat diedit.');

                return;
            }

            $logbook->update($payload);
            session()->flash('success', 'Logbook berhasil diperbarui dan menunggu persetujuan dosen pembimbing.');
        } else {
            $bimbingan->logbooks()->create($payload);
            session()->flash('success', 'Logbook berhasil ditambahkan dan menunggu persetujuan dosen pembimbing.');
        }

        $this->resetLogbookForm();
    }

    public function deleteLogbook(int $logbookId): void
    {
        $bimbingan = $this->requireLogbookAccess();
        $logbook = $bimbingan->logbooks()->whereKey($logbookId)->firstOrFail();

        if (! $logbook->mahasiswaCanModify()) {
            session()->flash('error', 'Logbook yang sudah disetujui tidak dapat dihapus. Minta dosen pembimbing menolak/membuka ulang terlebih dahulu.');

            return;
        }

        $logbook->delete();

        if ($this->editingLogbookId === $logbookId) {
            $this->resetLogbookForm();
        }

        session()->flash('success', 'Logbook bimbingan berhasil dihapus.');
    }

    public function render(): View
    {
        $bimbingan = $this->currentBimbingan()?->load(['pembimbing1', 'pembimbing2']);

        $logbooks = $bimbingan?->canUseLogbook()
            ? $bimbingan->logbooks()->with('pembimbing')->latest('tanggal')->latest('id')->get()
            : collect();

        return view('livewire.mahasiswa.bimbingan-skripsi.index', [
            'user' => Auth::user(),
            'bimbingan' => $bimbingan,
            'dosenOptions' => $this->dosenOptions(),
            'logbooks' => $logbooks,
            'approvedPembimbingOptions' => $bimbingan?->approvedPembimbingOptions() ?? [],
        ]);
    }

    protected function currentBimbingan(): ?BimbinganSkripsi
    {
        return BimbinganSkripsi::query()
            ->where('mahasiswa_id', Auth::id())
            ->first();
    }

    protected function requireLogbookAccess(): BimbinganSkripsi
    {
        abort_unless(Auth::user()?->isMahasiswa(), 403);

        $bimbingan = $this->currentBimbingan();

        abort_unless($bimbingan && $bimbingan->canUseLogbook(), 403);

        return $bimbingan;
    }

    protected function resetLogbookForm(): void
    {
        $this->reset([
            'editingLogbookId',
            'logbook_pembimbing_id',
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

    protected function dosenOptions()
    {
        return User::query()
            ->where('role', UserRole::Dosen)
            ->where('approval_status', UserApprovalStatus::Approved)
            ->orderBy('name')
            ->get(['id', 'name', 'username']);
    }
}
