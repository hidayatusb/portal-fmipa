<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    {{ $pageTitle }}
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Masukkan judul, teks, dan gambar pengumuman
                </p>
            </div>
            <a href="{{ route('admin.pengumuman.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                Kembali
            </a>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="kt-card max-w-3xl">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Form Pengumuman</h3>
            </div>
            <form wire:submit.prevent="save" class="kt-card-content flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-mono" for="title">Judul</label>
                    <input id="title" type="text" class="kt-input" wire:model="title"
                        placeholder="Contoh: Jadwal Ujian Tengah Semester" />
                    @error('title')
                        <span class="text-xs text-destructive">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-mono" for="body">Isi Pengumuman</label>
                    <textarea id="body" class="kt-textarea min-h-40" wire:model="body" rows="8"
                        placeholder="Tulis isi pengumuman di sini..."></textarea>
                    @error('body')
                        <span class="text-xs text-destructive">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-mono" for="image">Gambar</label>
                    <input id="image" type="file" class="kt-input" wire:model="image" accept="image/*" />
                    <p class="text-xs text-secondary-foreground">
                        Opsional. Format JPG, PNG, GIF, atau WEBP. Maks. 4 MB.
                    </p>
                    @error('image')
                        <span class="text-xs text-destructive">{{ $message }}</span>
                    @enderror

                    <div wire:loading wire:target="image" class="text-xs text-secondary-foreground">
                        Mengunggah gambar...
                    </div>

                    @if ($image)
                        <div class="kt-card border border-border">
                            <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <img src="{{ $image->temporaryUrl() }}" alt="Preview gambar"
                                        class="size-20 rounded-lg border border-border object-cover" />
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-mono">
                                            {{ $image->getClientOriginalName() }}
                                        </p>
                                        <p class="text-xs text-secondary-foreground">
                                            {{ number_format($image->getSize() / 1024, 1) }} KB
                                        </p>
                                    </div>
                                </div>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                    wire:click="removeImage">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @elseif ($existingImageUrl)
                        <div class="kt-card border border-border">
                            <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    <img src="{{ $existingImageUrl }}" alt="Gambar pengumuman"
                                        class="size-20 rounded-lg border border-border object-cover" />
                                    <p class="text-sm text-secondary-foreground">Gambar saat ini</p>
                                </div>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                    wire:click="markRemoveExistingImage">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @elseif (property_exists($this, 'removeExistingImage') && $this->removeExistingImage)
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-dashed border-border p-4">
                            <p class="text-sm text-secondary-foreground">Gambar akan dihapus saat disimpan.</p>
                            <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                wire:click="undoRemoveExistingImage">
                                Batalkan
                            </button>
                        </div>
                    @endif
                </div>

                <label class="flex items-center gap-2.5 text-sm text-mono">
                    <input type="checkbox" class="kt-checkbox" wire:model="is_published" />
                    Publikasikan sekarang
                </label>

                <div class="flex items-center gap-2.5 pt-2">
                    <button type="submit" class="kt-btn kt-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $submitLabel }}</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                    <a href="{{ route('admin.pengumuman.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
