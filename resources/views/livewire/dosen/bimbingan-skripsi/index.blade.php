<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Bimbingan Skripsi
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Setujui atau tolak permintaan bimbingan dari mahasiswa.
                </p>
            </div>
            @if ($pendingCount > 0)
                <span class="kt-badge kt-badge-warning kt-badge-outline">
                    {{ $pendingCount }} menunggu persetujuan
                </span>
            @endif
        </div>
    </div>

    <div class="kt-container-fixed space-y-5">
        @if (session('success'))
            <div class="kt-alert kt-alert-success flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="kt-alert kt-alert-destructive flex items-center gap-2">
                <i class="ki-filled ki-information-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Permintaan Bimbingan</h3>
            </div>
            <div class="kt-card-content p-0">
                @if ($requests->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-teacher text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">Belum ada permintaan bimbingan skripsi.</p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($requests as $request)
                            @php($myStatus = $request->statusForDosen($dosenId))
                            <div class="flex flex-wrap items-center justify-between gap-4 p-5"
                                wire:key="bimbingan-{{ $request->id }}">
                                <div class="min-w-0">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <h4 class="text-sm font-semibold text-mono">
                                            {{ $request->mahasiswa?->name }}
                                        </h4>
                                        <span class="kt-badge kt-badge-sm kt-badge-outline">
                                            {{ $request->roleLabelForDosen($dosenId) }}
                                        </span>
                                        @if ($myStatus)
                                            <span class="kt-badge kt-badge-sm px-1 {{ $myStatus->badgeClass() }}"
                                                title="{{ $myStatus->label() }}">
                                                <i class="ki-filled {{ $myStatus->icon() }}"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-secondary-foreground">
                                        NIM: {{ $request->mahasiswa?->username }}
                                        · Diajukan {{ $request->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-2">
                                    @if ($myStatus === \App\Enums\BimbinganApprovalStatus::Pending)
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                            wire:click="approve({{ $request->id }})"
                                            wire:confirm="Setujui bimbingan untuk {{ $request->mahasiswa?->name }}?">
                                            <i class="ki-filled ki-check text-xs"></i>
                                            Setujui
                                        </button>
                                        <button type="button"
                                            class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                            wire:click="reject({{ $request->id }})"
                                            wire:confirm="Tolak bimbingan untuk {{ $request->mahasiswa?->name }}?">
                                            <i class="ki-filled ki-cross text-xs"></i>
                                            Tolak
                                        </button>
                                    @endif
                                    <a href="{{ route('dosen.bimbingan-skripsi.show', $request) }}"
                                        class="kt-btn kt-btn-sm kt-btn-outline" wire:navigate>
                                        <i class="ki-filled ki-notepad text-xs"></i>
                                        Logbook
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
