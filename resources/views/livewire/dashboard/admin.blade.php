<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Dashboard Admin
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Kelola persetujuan akun dan pantau aktivitas portal
                </p>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Menunggu Review</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['pending'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Total Dosen</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['dosen'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Total Mahasiswa</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['mahasiswa'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Total Mata Kuliah</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['courses'] }}</span>
                </div>
            </div>
        </div>

        <div class="kt-card mt-7.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Tindakan Cepat</h3>
            </div>
            <div class="kt-card-content flex flex-wrap gap-2.5">
                <a href="{{ route('admin.user-approvals.index') }}" class="kt-btn kt-btn-primary" wire:navigate>
                    <i class="ki-filled ki-people"></i>
                    Review Akun
                    @if ($stats['pending'] > 0)
                        <span class="kt-badge kt-badge-xs kt-badge-light">{{ $stats['pending'] }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.pengumuman.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-notification-status"></i>
                    Kelola Pengumuman
                </a>
                <a href="{{ route('admin.pengumuman.create') }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-plus-squared"></i>
                    Tambah Pengumuman
                </a>
            </div>
        </div>

        @include('livewire.dashboard.partials.announcements', ['announcements' => $announcements])
    </div>
</div>
