<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="flex items-center gap-2.5 text-xl font-medium leading-none text-mono">
                    E-Learning
                    <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $courseCount }}</span>
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    Mata kuliah yang Anda ikuti
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <button type="button" class="kt-btn kt-btn-primary" wire:click="toggleJoinForm">
                    <i class="ki-filled ki-entrance-right"></i>
                    Gabung Kelas
                </button>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($showJoinForm)
            <div class="kt-card mb-7.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Gabung Kelas</h3>
                </div>
                <form wire:submit.prevent="joinClass" class="kt-card-content flex flex-col gap-5">
                    <p class="text-sm text-secondary-foreground">
                        Masukkan kode mata kuliah yang diberikan dosen pengampu untuk bergabung ke kelas.
                    </p>
                    <div class="flex flex-col gap-2 max-w-sm">
                        <label class="text-sm font-medium text-mono" for="joinCode">Kode Kelas</label>
                        <input id="joinCode" type="text" class="kt-input uppercase" wire:model="joinCode"
                            placeholder="Contoh: PW101" autofocus />
                        @error('joinCode')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Gabung
                        </button>
                        <button type="button" class="kt-btn kt-btn-outline" wire:click="toggleJoinForm">
                            Batal
                        </button>
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
                    <h3 class="text-base font-medium text-mono">Belum terdaftar di mata kuliah</h3>
                    <p class="text-sm text-secondary-foreground">
                        Anda belum terdaftar di mata kuliah manapun. Klik tombol
                        <span class="font-medium text-mono">Gabung Kelas</span> dan masukkan kode mata kuliah dari dosen
                        pengampu.
                    </p>
                    @unless ($showJoinForm)
                        <button type="button" class="kt-btn kt-btn-primary mt-2" wire:click="toggleJoinForm">
                            <i class="ki-filled ki-entrance-right"></i>
                            Gabung Kelas
                        </button>
                    @endunless
                </div>
            </div>
        @else
            <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($courses as $course)
                    <div class="kt-card flex flex-col">
                        <div class="kt-card-content flex grow flex-col gap-4 p-5">
                            <div>
                                <span class="kt-badge kt-badge-sm kt-badge-outline mb-2">{{ $course->code }}</span>
                                <h3 class="text-base font-semibold text-mono">{{ $course->title }}</h3>
                            </div>
                            <p class="line-clamp-2 text-sm text-secondary-foreground">
                                {{ $course->description ?: 'Belum ada deskripsi.' }}
                            </p>
                            <div class="flex flex-col gap-2 text-sm text-secondary-foreground">
                                <span class="flex items-center gap-1.5">
                                    <i class="ki-filled ki-profile-circle text-base"></i>
                                    {{ $course->lecturer->name }}
                                </span>
                                <div class="flex items-center gap-4">
                                    <span class="flex items-center gap-1.5">
                                        <i class="ki-filled ki-book-open text-base"></i>
                                        {{ $course->materials_count }} materi
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <i class="ki-filled ki-clipboard text-base"></i>
                                        {{ $course->assignments_count }} tugas
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="kt-card-footer justify-between">
                            <span class="text-xs text-muted-foreground">
                                Terdaftar {{ \Illuminate\Support\Carbon::parse($course->pivot->enrolled_at)->format('d M Y') }}
                            </span>
                            <a href="{{ route('mahasiswa.elearning.show', $course) }}"
                                class="kt-btn kt-btn-sm kt-btn-primary" wire:navigate>
                                Masuk Kelas
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
