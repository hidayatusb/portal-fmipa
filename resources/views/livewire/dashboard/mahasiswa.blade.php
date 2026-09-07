<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Dashboard Mahasiswa
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    Selamat datang, {{ $user->name }}. Pantau kelas dan tugas Anda dari sini.
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('mahasiswa.elearning.index') }}" class="kt-btn kt-btn-primary" wire:navigate>
                    <i class="ki-filled ki-book-open"></i>
                    Buka E-Learning
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="mb-7.5 grid gap-5 sm:grid-cols-3">
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Kelas Diikuti</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['courses'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Tugas Belum Dikerjakan</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['pending_assignments'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Tugas Sudah Dikumpulkan</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['submitted'] }}</span>
                </div>
            </div>
        </div>

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Kelas Terbaru</h3>
                <a href="{{ route('mahasiswa.elearning.index') }}" class="kt-btn kt-btn-sm kt-btn-outline" wire:navigate>
                    Lihat Semua
                </a>
            </div>
            <div class="kt-card-content p-0">
                @if ($courses->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-book text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">Anda belum bergabung ke mata kuliah mana pun.</p>
                        <a href="{{ route('mahasiswa.elearning.index') }}" class="kt-btn kt-btn-primary" wire:navigate>
                            Gabung Kelas
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($courses as $course)
                            <div class="flex flex-wrap items-center justify-between gap-4 p-5"
                                wire:key="dash-course-{{ $course->id }}">
                                <div>
                                    <span class="kt-badge kt-badge-sm kt-badge-outline mb-1">{{ $course->code }}</span>
                                    <h4 class="text-sm font-semibold text-mono">{{ $course->title }}</h4>
                                    <p class="mt-1 text-xs text-secondary-foreground">
                                        {{ $course->lecturer?->name ?? 'Dosen' }}
                                        · {{ $course->materials_count }} materi
                                        · {{ $course->assignments_count }} tugas
                                    </p>
                                </div>
                                <a href="{{ route('mahasiswa.elearning.show', $course) }}"
                                    class="kt-btn kt-btn-sm kt-btn-primary" wire:navigate>
                                    Buka
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>


    </div>
</div>
