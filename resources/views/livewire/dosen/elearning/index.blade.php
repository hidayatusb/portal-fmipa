<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="flex items-center gap-2.5 text-xl font-medium leading-none text-mono">
                    E-Learning
                    <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $courseCount }}</span>
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    Kelola mata kuliah dan materi pembelajaran Anda
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" class="kt-btn kt-btn-primary" wire:click="toggleForm">
                    <i class="ki-filled ki-plus"></i>
                    Tambah Mata Kuliah
                </button>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @if ($showForm)
            <div class="kt-card mb-7.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Tambah Mata Kuliah Baru</h3>
                </div>
                <form wire:submit.prevent="saveCourse" class="kt-card-content flex flex-col gap-5">
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="title">Nama Mata Kuliah</label>
                            <input id="title" type="text" class="kt-input" wire:model="title"
                                placeholder="Contoh: Pemrograman Web" />
                            @error('title')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="code">Kode Mata Kuliah</label>
                            <input id="code" type="text" class="kt-input uppercase" wire:model="code"
                                placeholder="Contoh: PW101" />
                            @error('code')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="description">Deskripsi</label>
                        <textarea id="description" rows="3" class="kt-input" wire:model="description"
                            placeholder="Deskripsi singkat mata kuliah"></textarea>
                        @error('description')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="submit" class="kt-btn kt-btn-primary">Simpan</button>
                        <button type="button" class="kt-btn kt-btn-outline" wire:click="toggleForm">Batal</button>
                    </div>
                </form>
            </div>
        @endif

        <div class="mb-5 flex flex-wrap items-center gap-3">
            <div class="kt-input min-w-[200px] max-w-md flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-search text-muted-foreground" aria-hidden="true">
                    <path d="m21 21-4.34-4.34"></path>
                    <circle cx="11" cy="11" r="8"></circle>
                </svg>
                <input type="search" class="kt-input" wire:model.live.debounce.300ms="search"
                    placeholder="Cari mata kuliah..." />
            </div>
            @if ($search !== '')
                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline" wire:click="$set('search', '')">
                    Reset
                </button>
            @endif
        </div>

        @if ($courses->isEmpty() && $search !== '')
            <div class="kt-card">
                <div class="kt-card-content flex flex-col items-center gap-3 p-10 text-center">
                    <i class="ki-filled ki-magnifier text-4xl text-muted-foreground"></i>
                    <h3 class="text-base font-medium text-mono">Mata kuliah tidak ditemukan</h3>
                    <p class="text-sm text-secondary-foreground">
                        Tidak ada mata kuliah yang cocok dengan pencarian
                        <span class="font-medium text-mono">"{{ $search }}"</span>.
                    </p>
                    <button type="button" class="kt-btn kt-btn-outline mt-2" wire:click="$set('search', '')">
                        Hapus Pencarian
                    </button>
                </div>
            </div>
        @elseif ($courses->isEmpty())
            <div class="kt-card">
                <div class="kt-card-content flex flex-col items-center gap-3 p-10 text-center">
                    <i class="ki-filled ki-book text-4xl text-muted-foreground"></i>
                    <h3 class="text-base font-medium text-mono">Belum ada mata kuliah</h3>
                    <p class="text-sm text-secondary-foreground">
                        Mulai dengan menambahkan mata kuliah pertama Anda.
                    </p>
                    <button type="button" class="kt-btn kt-btn-primary" wire:click="toggleForm">
                        Tambah Mata Kuliah
                    </button>
                </div>
            </div>
        @else
            <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($courses as $course)
                    <div class="kt-card flex flex-col">
                        <div class="kt-card-content flex grow flex-col gap-4 p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="kt-badge kt-badge-sm kt-badge-outline mb-2">{{ $course->code }}</span>
                                    <h3 class="text-base font-semibold text-mono">{{ $course->title }}</h3>
                                </div>
                            </div>
                            <p class="line-clamp-2 text-sm text-secondary-foreground">
                                {{ $course->description ?: 'Belum ada deskripsi.' }}
                            </p>
                            <div class="flex items-center gap-4 text-sm text-secondary-foreground">
                                <span class="flex items-center gap-1.5">
                                    <i class="ki-filled ki-people text-base"></i>
                                    {{ $course->students_count }} mahasiswa
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i class="ki-filled ki-book-open text-base"></i>
                                    {{ $course->materials_count }} materi
                                </span>
                            </div>
                        </div>
                        <div class="kt-card-footer justify-between">
                            <span class="text-xs text-muted-foreground">
                                Diperbarui {{ $course->updated_at->diffForHumans() }}
                            </span>
                            <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-sm kt-btn-primary"
                                wire:navigate>
                                Kelola
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
