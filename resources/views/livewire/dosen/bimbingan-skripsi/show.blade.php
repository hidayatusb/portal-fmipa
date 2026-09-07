<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Logbook — {{ $bimbingan->mahasiswa?->name }}
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    NIM {{ $bimbingan->mahasiswa?->username }}
                    · Anda sebagai {{ $bimbingan->roleLabelForDosen($dosenId) }}
                </p>
            </div>
            <a href="{{ route('dosen.bimbingan-skripsi.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                <i class="ki-filled ki-left"></i>
                Kembali
            </a>
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
                <h3 class="kt-card-title">Status Pembimbing</h3>
            </div>
            <div class="kt-card-content grid gap-4 p-5 sm:grid-cols-2">
                <div class="rounded-lg border border-border p-4">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="text-xs font-medium uppercase text-muted-foreground">Pembimbing 1</span>
                        <span class="kt-badge kt-badge-sm px-1 {{ $bimbingan->pembimbing1_status->badgeClass() }}"
                            title="{{ $bimbingan->pembimbing1_status->label() }}">
                            <i class="ki-filled {{ $bimbingan->pembimbing1_status->icon() }}"></i>
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-mono">{{ $bimbingan->pembimbing1?->name }}</p>
                </div>
                <div class="rounded-lg border border-border p-4">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <span class="text-xs font-medium uppercase text-muted-foreground">Pembimbing 2</span>
                        <span class="kt-badge kt-badge-sm px-1 {{ $bimbingan->pembimbing2_status->badgeClass() }}"
                            title="{{ $bimbingan->pembimbing2_status->label() }}">
                            <i class="ki-filled {{ $bimbingan->pembimbing2_status->icon() }}"></i>
                        </span>
                    </div>
                    <p class="text-sm font-semibold text-mono">{{ $bimbingan->pembimbing2?->name }}</p>
                </div>
            </div>
        </div>

        <div class="kt-card">
            <div class="kt-card-header flex-wrap gap-3">
                <div>
                    <h3 class="kt-card-title">Logbook Bimbingan</h3>
                    
                </div>
                @if ($canManageLogbook && ! $showLogbookForm)
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" wire:click="openLogbookForm">
                        <i class="ki-filled ki-plus-squared text-xs"></i>
                        Tambah Logbook
                    </button>
                @endif
            </div>

            @if ($canManageLogbook && $showLogbookForm)
                <form wire:submit.prevent="saveLogbook" class="border-b border-border p-5">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <h4 class="text-sm font-semibold text-mono">
                            {{ $editingLogbookId ? 'Edit Logbook' : 'Logbook Baru' }}
                        </h4>
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline" wire:click="cancelLogbookForm">
                            Batal
                        </button>
                    </div>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 flex flex-col gap-1.5 sm:col-span-6">
                            <label class="text-sm font-medium">Tanggal Bimbingan</label>
                            <input type="date" wire:model="tanggal" class="kt-input w-full" />
                            @error('tanggal') <p class="text-xs text-destructive">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-span-12 flex flex-col gap-1.5 sm:col-span-6">
                            <label class="text-sm font-medium">Mahasiswa</label>
                            <input type="text" class="kt-input w-full" value="{{ $bimbingan->mahasiswa?->name }}"
                                disabled />
                        </div>
                        <div class="col-span-12 flex flex-col gap-1.5">
                            <label class="text-sm font-medium">Judul</label>
                            <input type="text" wire:model="judul" class="kt-input w-full"
                                placeholder="Contoh: Revisi Bab 2 — Landasan Teori" />
                            @error('judul') <p class="text-xs text-destructive">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-span-12 flex flex-col gap-1.5">
                            <label class="text-sm font-medium">Catatan</label>
                            <div class="w-full overflow-hidden"
                                wire:key="dosen-logbook-trix-{{ $editingLogbookId ?? 'new' }}">
                                <x-trix-livewire
                                    id="dosen_logbook_catatan"
                                    wire:model="catatan"
                                    class="min-h-40"
                                />
                            </div>
                            @error('catatan') <p class="text-xs text-destructive">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="kt-btn kt-btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveLogbook">
                                {{ $editingLogbookId ? 'Simpan Perubahan' : 'Simpan Logbook' }}
                            </span>
                            <span wire:loading wire:target="saveLogbook">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            @endif

            <div class="kt-card-content p-0">
                @if (! $canManageLogbook)
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-information-2 text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">
                            Setujui permintaan bimbingan terlebih dahulu untuk mengisi dan melihat logbook.
                        </p>
                    </div>
                @elseif ($logbooks->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-notepad text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">
                            Belum ada logbook bimbingan dengan Anda.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($logbooks as $index => $logbook)
                            <div wire:key="dosen-logbook-{{ $logbook->id }}">
                                <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                                    <div class="min-w-0">
                                        <div class="mb-1 flex flex-wrap items-center gap-2">
                                            <span class="kt-badge kt-badge-sm kt-badge-outline">
                                                Pertemuan {{ $logbooks->count() - $index }}
                                            </span>
                                            <span class="kt-badge kt-badge-sm px-1 {{ $logbook->status->badgeClass() }}"
                                                title="{{ $logbook->status->label() }}">
                                                <i class="ki-filled {{ $logbook->status->icon() }}"></i>
                                            </span>
                                            <span class="text-xs text-secondary-foreground">
                                                {{ $logbook->tanggal->translatedFormat('d F Y') }}
                                            </span>
                                        </div>
                                        <h4 class="truncate text-sm font-semibold text-mono">{{ $logbook->judul }}</h4>
                                    </div>
                                    <div class="flex shrink-0 flex-wrap gap-2">
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                            data-kt-modal-toggle="#dosen_logbook_detail_modal_{{ $logbook->id }}">
                                            Detail
                                        </button>
                                        @if ($logbook->isPending())
                                            <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                                wire:click="approveLogbook({{ $logbook->id }})"
                                                wire:confirm="Setujui logbook ini?">
                                                Setujui
                                            </button>
                                            <button type="button"
                                                class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                wire:click="rejectLogbook({{ $logbook->id }})"
                                                wire:confirm="Tolak logbook ini?">
                                                Tolak
                                            </button>
                                        @elseif ($logbook->isApproved())
                                            <button type="button"
                                                class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                wire:click="rejectLogbook({{ $logbook->id }})"
                                                wire:confirm="Buka ulang logbook agar mahasiswa bisa edit/hapus?">
                                                Buka Ulang
                                            </button>
                                        @elseif ($logbook->isRejected())
                                            <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                                wire:click="approveLogbook({{ $logbook->id }})"
                                                wire:confirm="Setujui logbook ini?">
                                                Setujui
                                            </button>
                                        @endif
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                            wire:click="editLogbook({{ $logbook->id }})">
                                            Edit
                                        </button>
                                        <button type="button"
                                            class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                            wire:click="deleteLogbook({{ $logbook->id }})"
                                            wire:confirm="Hapus logbook ini?">
                                            Hapus
                                        </button>
                                    </div>
                                </div>

                                <div class="kt-modal" data-kt-modal="true"
                                    id="dosen_logbook_detail_modal_{{ $logbook->id }}" wire:ignore.self>
                                    <div class="kt-modal-content max-w-2xl top-[10%]">
                                        <div class="kt-modal-header">
                                            <div class="min-w-0">
                                                <h3 class="kt-modal-title">{{ $logbook->judul }}</h3>
                                                <p class="mt-1 text-xs text-secondary-foreground">
                                                    {{ $logbook->tanggal->translatedFormat('d F Y') }}
                                                    · {{ $logbook->status->label() }}
                                                </p>
                                            </div>
                                            <button type="button" class="kt-modal-close" aria-label="Close modal"
                                                data-kt-modal-dismiss="#dosen_logbook_detail_modal_{{ $logbook->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-x" aria-hidden="true">
                                                    <path d="M18 6 6 18"></path>
                                                    <path d="m6 6 12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="kt-modal-body max-h-[60vh] overflow-y-auto">
                                            <p class="mb-2 text-xs font-medium uppercase text-muted-foreground">
                                                Catatan
                                            </p>
                                            <div class="trix-content prose-logbook text-sm text-secondary-foreground">
                                                {!! $logbook->catatan !!}
                                            </div>
                                        </div>
                                        <div class="kt-modal-footer flex-wrap gap-2.5">
                                            <button type="button" class="kt-btn kt-btn-outline"
                                                data-kt-modal-dismiss="#dosen_logbook_detail_modal_{{ $logbook->id }}">
                                                Tutup
                                            </button>
                                            @if ($logbook->isPending())
                                                <button type="button"
                                                    class="kt-btn kt-btn-outline text-destructive"
                                                    data-kt-modal-dismiss="#dosen_logbook_detail_modal_{{ $logbook->id }}"
                                                    wire:click="rejectLogbook({{ $logbook->id }})">
                                                    Tolak
                                                </button>
                                                <button type="button" class="kt-btn kt-btn-primary"
                                                    data-kt-modal-dismiss="#dosen_logbook_detail_modal_{{ $logbook->id }}"
                                                    wire:click="approveLogbook({{ $logbook->id }})">
                                                    Setujui
                                                </button>
                                            @else
                                                <button type="button" class="kt-btn kt-btn-primary"
                                                    data-kt-modal-dismiss="#dosen_logbook_detail_modal_{{ $logbook->id }}"
                                                    wire:click="editLogbook({{ $logbook->id }})">
                                                    Edit
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
