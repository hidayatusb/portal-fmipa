<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    {{ $course->title }}
                </h1>
                <div class="text-sm font-normal text-secondary-foreground">
                    {{ $course->description ?: 'Belum ada deskripsi mata kuliah.' }}
                </div>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('mahasiswa.elearning.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        <div class="mb-7.5 grid grid-cols-3 gap-5">
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Dosen Pengampu</span>
                    <span class="text-sm font-semibold text-mono">{{ $course->lecturer->name }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Total Materi</span>
                    <span class="text-2xl font-semibold text-mono">{{ $course->materials->count() }}</span>
                </div>
            </div>
            <div class="kt-card">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Total Tugas</span>
                    <span class="text-2xl font-semibold text-mono">{{ $course->assignments->count() }}</span>
                </div>
            </div>
        </div>

        <div class="mb-7.5">
            <div class="kt-card flex flex-col">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Materi Pembelajaran</h3>
                </div>
                <div class="kt-card-content p-0">
                    @if ($course->materials->isEmpty())
                        <div class="flex flex-col items-center gap-3 p-10 text-center">
                            <i class="ki-filled ki-notepad-edit text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">Belum ada materi untuk mata kuliah ini.</p>
                        </div>
                    @else
                        <div class="divide-y divide-border">
                            @foreach ($course->materials as $material)
                                <div class="flex items-start gap-3 p-4" wire:key="material-{{ $material->id }}">
                                    <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                        @if ($material->type === 'video')
                                            <i class="ki-filled ki-youtube"></i>
                                        @elseif ($material->type === 'link')
                                            <i class="ki-filled ki-exit-right-corner"></i>
                                        @else
                                            <i class="ki-filled ki-document"></i>
                                        @endif
                                    </span>
                                    <div class="min-w-0 grow">
                                        <h4 class="text-sm font-semibold text-mono">
                                            @if ($material->hasFile())
                                                @if ($material->isImageFile())
                                                  

                                                    <a href="{{ $material->fileUrl('mahasiswa') }}" target="_blank">{{ $material->title }}</a>
                                            
                                                @endif
                                                
                                            @elseif ($material->content)
                                              

                                                <a href="{{ $material->content }}" target="_blank">{{ $material->title }}</a>
                                            
                                            @endif
                                            
                                           
                                            
                                            </h4>
                                        <p class="mt-1 text-xs text-secondary-foreground">
                                            {{ ucfirst($material->type) }}
                                            @if ($material->hasFile())
                                                · {{ $material->file_name }}
                                            @elseif ($material->content)
                                                · {{ \Illuminate\Support\Str::limit($material->content, 80) }}
                                            @endif
                                        </p>
                                       
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="kt-card flex flex-col">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Tugas</h3>
            </div>
            <div class="kt-card-content flex flex-col gap-0 p-0">
                @if ($course->assignments->isEmpty())
                    <div class="flex flex-col items-center gap-3 p-10 text-center">
                        <i class="ki-filled ki-clipboard text-4xl text-muted-foreground"></i>
                        <p class="text-sm text-secondary-foreground">Belum ada tugas untuk mata kuliah ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach ($course->assignments as $assignment)
                            @php($studentSubmission = $assignment->submissionFor(auth()->id()))
                            <a href="{{ route('mahasiswa.elearning.assignments.show', [$course, $assignment]) }}"
                                class="flex items-start gap-3 p-4 transition-colors hover:bg-accent/40"
                                wire:navigate wire:key="assignment-{{ $assignment->id }}">
                                <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                    <i class="ki-filled ki-clipboard"></i>
                                </span>
                                <div class="min-w-0 grow">
                                    <div class="mb-1 flex flex-wrap items-center gap-1.5">
                                        <h4 class="truncate text-sm font-semibold text-mono">{{ $assignment->title }}</h4>
                                        @if ($studentSubmission)
                                            <span class="kt-badge kt-badge-sm kt-badge-success">Sudah Kumpul</span>
                                            @if ($studentSubmission->isLate())
                                                <span class="kt-badge kt-badge-sm kt-badge-warning">Terlambat</span>
                                            @endif
                                        @elseif ($assignment->isClosedForSubmissions())
                                            <span class="kt-badge kt-badge-sm kt-badge-danger">Berakhir</span>
                                        @elseif ($assignment->isOverdue())
                                            <span class="kt-badge kt-badge-sm kt-badge-warning">Lewat Deadline</span>
                                        @elseif ($assignment->isDueSoon())
                                            <span class="kt-badge kt-badge-sm kt-badge-warning">Segera Berakhir</span>
                                        @else
                                            <span class="kt-badge kt-badge-sm kt-badge-success">Aktif</span>
                                        @endif
                                    </div>
                                    <p class="text-2xs text-muted-foreground">
                                        Batas waktu: {{ $assignment->due_date->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <i class="ki-filled ki-right shrink-0 text-xs text-muted-foreground"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
