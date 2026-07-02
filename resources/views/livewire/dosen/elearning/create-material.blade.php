<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Tambah Materi
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
                <h3 class="kt-card-title">Form Materi Baru</h3>
            </div>
            <form wire:submit.prevent="save" class="kt-card-content flex flex-col gap-5">
                <div class="grid gap-5 lg:grid-cols-2">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="title">Judul Materi</label>
                        <input id="title" type="text" class="kt-input" wire:model="title"
                            placeholder="Contoh: Pengenalan HTML" />
                        @error('title')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="type">Tipe Materi</label>
                        <select id="type" class="kt-select" wire:model.live="type">
    
                            <option value="document">Dokumen</option>
                            <option value="video">Video</option>
                            <option value="link">Tautan</option>
                        </select>
                        @error('type')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($type === 'document')
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="file">File Dokumen</label>
                        <input id="file" type="file" class="kt-input" wire:model="file" />
                        <p class="text-xs text-secondary-foreground">
                            Wajib. Maks. 10 MB.
                        </p>
                        @error('file')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror

                        <div wire:loading wire:target="file" class="text-xs text-secondary-foreground">
                            Mengunggah file...
                        </div>

                        @if ($file)
                            <div class="kt-card border border-border">
                                <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if (str_starts_with($file->getMimeType(), 'image/'))
                                            <img src="{{ $file->temporaryUrl() }}" alt="Preview file"
                                                class="size-16 rounded-lg border border-border object-cover" />
                                        @else
                                            <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                                <i class="ki-filled ki-document"></i>
                                            </span>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-mono">
                                                {{ $file->getClientOriginalName() }}</p>
                                            <p class="text-xs text-secondary-foreground">
                                                {{ number_format($file->getSize() / 1024, 1) }} KB
                                            </p>
                                        </div>
                                    </div>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                        wire:click="removeFile">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="content">URL</label>
                        <input id="content" type="url" class="kt-input" wire:model="content"
                            placeholder="{{ $type === 'video' ? 'https://youtube.com/watch?v=...' : 'https://example.com/materi' }}" />
                        @error('content')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <div class="flex items-center gap-2.5">
                    <button type="submit" class="kt-btn kt-btn-primary">Simpan Materi</button>
                    <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
