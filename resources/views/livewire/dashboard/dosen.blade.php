<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Dashboard Dosen
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    Selamat datang, {{ $user->name }}. Kelola pembelajaran Anda dari sini.
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dosen.elearning.index') }}" class="kt-btn kt-btn-primary" wire:navigate>
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
                    <span class="text-2sm text-secondary-foreground">Mata Kuliah</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['courses'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Mahasiswa</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['students'] }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Materi</span>
                    <span class="text-2xl font-semibold text-mono">{{ $stats['materials'] }}</span>
                </div>
            </div>
        </div>

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Mata Kuliah Terbaru</h3>
                <a href="{{ route('dosen.elearning.index') }}" class="kt-btn kt-btn-sm kt-btn-outline" wire:navigate>
                    Lihat Semua
                </a>
            </div>
            <div class="kt-card-content p-0">
                @if ($courses->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-book text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">Anda belum memiliki mata kuliah.</p>
                        <a href="{{ route('dosen.elearning.index') }}" class="kt-btn kt-btn-primary" wire:navigate>
                            Mulai E-Learning
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($courses as $course)
                            <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                                <div>
                                    <span class="kt-badge kt-badge-sm kt-badge-outline mb-1">{{ $course->code }}</span>
                                    <h4 class="text-sm font-semibold text-mono">{{ $course->title }}</h4>
                                    <p class="mt-1 text-xs text-secondary-foreground">
                                        {{ $course->students_count }} mahasiswa · {{ $course->materials_count }} materi
                                    </p>
                                </div>
                                <a href="{{ route('dosen.elearning.show', $course) }}"
                                    class="kt-btn kt-btn-sm kt-btn-primary" wire:navigate>
                                    Kelola
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @include('livewire.dashboard.partials.announcements', ['announcements' => $announcements])
    </div>
</div>
