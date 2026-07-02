<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Review Akun
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Setujui atau tolak pendaftaran dosen dan mahasiswa
                </p>
            </div>
            @if ($pendingCount > 0)
                <span class="kt-badge kt-badge-warning kt-badge-outline">
                    {{ $pendingCount }} menunggu review
                </span>
            @endif
        </div>
    </div>

    <div class="kt-container-fixed">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="kt-card mb-5">
            <div class="kt-card-content flex flex-col gap-4 p-5 lg:flex-row lg:items-end">
                <div class="flex flex-col gap-2 grow">
                    <label class="text-sm font-medium text-mono" for="search">Cari</label>
                    <input id="search" type="text" class="kt-input" wire:model.live.debounce.300ms="search"
                        placeholder="Nama, username, atau email..." />
                </div>
                <div class="flex flex-col gap-2 w-full lg:w-56">
                    <label class="text-sm font-medium text-mono" for="status">Status</label>
                    <select id="status" class="kt-select" wire:model.live="status">
                        <option value="pending">Menunggu Review</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Daftar Akun</h3>
            </div>
            <div class="kt-card-content p-0">
                @if ($users->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-people text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">Tidak ada akun yang cocok dengan filter ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($users as $user)
                            <div class="flex flex-wrap items-start justify-between gap-4 p-5" wire:key="user-{{ $user->id }}">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-sm font-semibold text-mono">{{ $user->name }}</h4>
                                        <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $user->role->label() }}</span>
                                        <span class="kt-badge kt-badge-sm {{ $user->approval_status->badgeClass() }}">
                                            {{ $user->approval_status->label() }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-secondary-foreground">
                                        {{ $user->username }} · {{ $user->email }}
                                    </p>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        Daftar {{ $user->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                @if ($user->isPendingApproval())
                                    <div class="flex shrink-0 items-center gap-2">
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                            wire:click="approve({{ $user->id }})"
                                            wire:confirm="Setujui akun {{ $user->name }}?">
                                            <i class="ki-filled ki-check text-xs"></i>
                                            Setujui
                                        </button>
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                            wire:click="reject({{ $user->id }})"
                                            wire:confirm="Tolak akun {{ $user->name }}?">
                                            <i class="ki-filled ki-cross text-xs"></i>
                                            Tolak
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($users->hasPages())
                        <div class="border-t border-border p-5">
                            {{ $users->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
