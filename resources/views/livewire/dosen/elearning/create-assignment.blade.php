<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Tambah Tugas
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    {{ $course->title }}
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="kt-card max-w-3xl">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Form Tugas Baru</h3>
            </div>
            <form wire:submit.prevent="save" class="kt-card-content flex flex-col gap-5">
                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="title">Judul Tugas</label>
                        <input id="title" type="text" class="kt-input" wire:model="title"
                            placeholder="Contoh: Tugas 1 - Landing Page" />
                        @error('title')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="dueDate">Batas Waktu</label>
                        <input id="dueDate" type="datetime-local" class="kt-input" wire:model="dueDate" />
                        @error('dueDate')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex flex-col gap-2 rounded-lg border border-border p-4">
                    <label class="flex items-start gap-2.5">
                        <input type="checkbox" class="kt-checkbox kt-checkbox-sm mt-0.5"
                            wire:model="acceptLateSubmissions" />
                        <span class="flex flex-col gap-0.5">
                            <span class="text-sm font-medium text-mono">Terima pengumpulan setelah deadline</span>
                            <span class="text-xs text-secondary-foreground">
                                Jika tidak dicentang, mahasiswa tidak dapat mengumpulkan tugas setelah batas waktu berakhir.
                            </span>
                        </span>
                    </label>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-mono" for="description">Instruksi Tugas</label>
                    <textarea id="description" rows="5" class="kt-textarea" wire:model="description"
                        placeholder="Jelaskan detail tugas yang harus dikerjakan mahasiswa"></textarea>
                    @error('description')
                        <span class="text-xs text-destructive">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-mono" for="attachment">Lampiran</label>
                    <input id="attachment" type="file" class="kt-input" wire:model="attachment" />
                    <p class="text-xs text-secondary-foreground">
                        Opsional. Maks. 10 MB.
                    </p>
                    @error('attachment')
                        <span class="text-xs text-destructive">{{ $message }}</span>
                    @enderror

                    <div wire:loading wire:target="attachment" class="text-xs text-secondary-foreground">
                        Mengunggah file...
                    </div>

                    @if ($attachment)
                        <div class="kt-card border border-border">
                            <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if (str_starts_with($attachment->getMimeType(), 'image/'))
                                        <img src="{{ $attachment->temporaryUrl() }}" alt="Preview lampiran"
                                            class="size-16 rounded-lg border border-border object-cover" />
                                    @else
                                        <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                            <i class="ki-filled ki-document"></i>
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-mono">{{ $attachment->getClientOriginalName() }}</p>
                                        <p class="text-xs text-secondary-foreground">
                                            {{ number_format($attachment->getSize() / 1024, 1) }} KB
                                        </p>
                                    </div>
                                </div>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                    wire:click="removeAttachment">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2.5">
                    <button type="submit" class="kt-btn kt-btn-primary">Simpan Tugas</button>
                    <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
