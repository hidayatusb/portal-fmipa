<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    {{ $assignment->title }}
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    {{ $course->title }}
                </p>
            </div>
            <div class="kt-card-toolbar">
                <a href="{{ route('mahasiswa.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @php
            $tone = $assignment->deadlineTone();

            $toneConfig = match ($tone) {
                'overdue' => [
                    'badge' => 'kt-badge-destructive',
                    'progress' => 'kt-progress-destructive',
                    'label' => 'Sudah Berakhir',
                ],
                'urgent' => [
                    'badge' => 'kt-badge-warning',
                    'progress' => 'kt-progress-warning',
                    'label' => 'Segera Berakhir',
                ],
                default => [
                    'badge' => 'kt-badge-success',
                    'progress' => 'kt-progress-primary',
                    'label' => 'Aktif',
                ],
            };
        @endphp

        <x-assignment-countdown
            wire:key="assignment-countdown-{{ $assignment->id }}-{{ $assignment->updated_at?->timestamp }}"
            :due-date="$assignment->due_date" :created-at="$assignment->created_at" class="mb-7.5 grid items-stretch gap-5">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Informasi Tugas</h3>
                    <span class="kt-badge kt-badge-outline {{ $toneConfig['badge'] }}">{{ $toneConfig['label'] }}</span>
                </div>
                <div class="kt-card-content flex flex-col gap-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="flex flex-col gap-1">
                            <span class="text-2sm text-secondary-foreground">Batas Waktu</span>
                            <span class="text-sm font-semibold text-mono">
                                {{ $assignment->due_date->locale('id')->translatedFormat('l, d M Y, H:i') }}
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-2sm text-secondary-foreground">Sisa Waktu</span>
                            <span class="text-sm font-semibold text-mono tabular-nums" x-text="remainingLabel"></span>
                        </div>
                        <div class="flex flex-col gap-1 sm:col-span-2">
                            <span class="text-2sm text-secondary-foreground">Setelah Deadline</span>
                            <span class="text-sm font-semibold text-mono">
                                {{ $assignment->lateSubmissionLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="kt-separator"></div>

                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-2sm text-secondary-foreground">Progress waktu</span>
                            <span class="text-2sm font-medium text-mono tabular-nums" x-text="`${progress}%`"></span>
                        </div>
                        <div class="kt-progress {{ $toneConfig['progress'] }}"
                            :style="`--progress-value: ${progress}%`">
                            <div class="kt-progress-indicator"></div>
                        </div>
                        <div class="flex justify-between text-2xs text-muted-foreground">
                            <span>Dibuat {{ $assignment->created_at->locale('id')->translatedFormat('d M Y') }}</span>
                            <span>Deadline {{ $assignment->due_date->locale('id')->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-assignment-countdown>

        <div class="flex flex-col gap-5">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Instruksi Tugas</h3>
                </div>
                <div class="kt-card-content">
                    @if ($assignment->description)
                        <p class="whitespace-pre-wrap text-sm leading-relaxed text-secondary-foreground">
                            {{ $assignment->description }}
                        </p>
                    @else
                        <div class="flex flex-col items-center gap-3 py-10 text-center">
                            <i class="ki-filled ki-notepad-edit text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">Belum ada instruksi untuk tugas ini.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Lampiran</h3>
                   
                </div>
                <div class="kt-card-content">
                    @if ($assignment->hasAttachment())
                        @if ($assignment->isImageAttachment())
                            <a href="{{ $assignment->attachmentUrl('mahasiswa') }}" target="_blank"
                                class="block overflow-hidden rounded-lg border border-border">
                                <img src="{{ $assignment->attachmentUrl('mahasiswa') }}"
                                    alt="{{ $assignment->attachment_name }}" class="max-h-72 w-full object-cover" />
                            </a>
                        @else
                            <a href="{{ $assignment->attachmentUrl('mahasiswa') }}" target="_blank"
                                class="flex items-center gap-3 rounded-lg border border-border p-4 transition-colors hover:bg-accent/50">
                                <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                    <i class="ki-filled ki-document"></i>
                                </span>
                                <div class="min-w-0 grow">
                                    <p class="truncate text-sm font-medium text-mono">
                                        {{ $assignment->attachment_name ?? 'Buka lampiran' }}
                                    </p>
                                    <p class="text-xs text-secondary-foreground">Klik untuk membuka file</p>
                                </div>
                                <i class="ki-filled ki-exit-right-corner text-xs text-muted-foreground"></i>
                            </a>
                        @endif
                    @else
                        <div class="flex flex-col items-center gap-3 py-10 text-center">
                            <i class="ki-filled ki-document text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">Tugas ini tidak memiliki lampiran.</p>
                        </div>
                    @endif
                </div>
               
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Kumpulkan Jawaban</h3>
                    @if ($submission)
                        <span class="kt-badge kt-badge-sm kt-badge-success">Sudah Dikumpulkan</span>
                        @if ($submission->isLate())
                            <span class="kt-badge kt-badge-sm kt-badge-warning">Terlambat</span>
                        @endif
                    @elseif ($assignment->isClosedForSubmissions())
                        <span class="kt-badge kt-badge-sm kt-badge-danger">Batas Waktu Habis</span>
                    @elseif ($assignment->isOverdue())
                        <span class="kt-badge kt-badge-sm kt-badge-warning">Lewat Deadline</span>
                    @endif
                </div>
                <div class="kt-card-content">
                    @if ($assignment->isClosedForSubmissions() && ! $submission)
                        <div class="flex flex-col items-center gap-3 py-10 text-center">
                            <i class="ki-filled ki-time text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">
                                Batas waktu pengumpulan sudah berakhir. Anda tidak dapat mengumpulkan tugas ini.
                            </p>
                        </div>
                    @else
                        @if ($assignment->isOverdue() && $assignment->acceptsLateSubmissions() && ! $submission)
                            <div class="mb-5 rounded-lg border border-warning/30 bg-warning/10 p-4">
                                <p class="text-sm text-secondary-foreground">
                                    Batas waktu sudah lewat, tetapi dosen masih membuka pengumpulan untuk tugas ini.
                                </p>
                            </div>
                        @endif
                        @if ($submission)
                            <div class="mb-5 rounded-lg border border-border bg-accent/30 p-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-xs text-secondary-foreground">Terakhir dikumpulkan</p>
                                    @if ($submission->isLate())
                                        <span class="kt-badge kt-badge-xs kt-badge-warning kt-badge-outline">Lewat Deadline</span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-mono">
                                    {{ $submission->submitted_at->locale('id')->translatedFormat('l, d M Y, H:i') }}
                                </p>
                            </div>
                        @endif

                        <form wire:submit.prevent="submitAssignment" class="flex flex-col gap-5">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="content">Catatan / Jawaban Teks</label>
                                <textarea id="content" rows="4" class="kt-input" wire:model="content"
                                    placeholder="Tulis jawaban atau catatan tambahan (opsional jika ada file)"
                                    @if ($assignment->isClosedForSubmissions()) disabled @endif></textarea>
                                @error('content')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="file">File Jawaban</label>

                                @if ($submission?->hasFile() && ! $removeExistingFile)
                                    <div class="kt-card border border-border">
                                        <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                                    <i class="ki-filled ki-document"></i>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="truncate text-sm font-medium text-mono">
                                                        {{ $submission->file_name }}</p>
                                                    <p class="text-xs text-secondary-foreground">File saat ini</p>
                                                </div>
                                            </div>
                                            @if ($assignment->acceptsSubmissions())
                                                <button type="button"
                                                    class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                    wire:click="markRemoveExistingFile">
                                                    Hapus
                                                </button>
                                            @else
                                                <a href="{{ $submission->fileDownloadUrl('mahasiswa') }}"
                                                    class="kt-btn kt-btn-sm kt-btn-outline">
                                                    Unduh
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($submission?->hasFile() && $removeExistingFile)
                                    <div
                                        class="flex items-center justify-between gap-3 rounded-lg border border-dashed border-border p-4">
                                        <p class="text-sm text-secondary-foreground">File akan dihapus setelah disimpan.</p>
                                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                            wire:click="undoRemoveExistingFile">
                                            Batalkan
                                        </button>
                                    </div>
                                @endif

                                @if ($assignment->acceptsSubmissions())
                                    <input id="file" type="file" class="kt-input" wire:model="file" />
                                    <p class="text-xs text-secondary-foreground">
                                        Maks. 10 MB.
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
                                                        <img src="{{ $file->temporaryUrl() }}" alt="Preview"
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
                                                <button type="button"
                                                    class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                    wire:click="removeFile">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            @if ($assignment->acceptsSubmissions())
                                <div class="flex items-center gap-2.5">
                                    <button type="submit" class="kt-btn kt-btn-primary">
                                        {{ $submission ? 'Perbarui Jawaban' : 'Kumpulkan Tugas' }}
                                    </button>
                                </div>
                            @endif
                        </form>
                    @endif
                </div>
            </div>

            @if ($submission?->isGraded())
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Penilaian</h3>
                        @if ($submission->feedback_at)
                            <span class="text-xs text-muted-foreground">
                                {{ $submission->feedback_at->locale('id')->translatedFormat('d M Y, H:i') }}
                            </span>
                        @endif
                    </div>
                    <div class="kt-card-content flex flex-col gap-4">
                        <div class="flex items-center gap-4 rounded-lg border border-border bg-accent/30 p-4">
                            <div
                                class="inline-flex size-16 shrink-0 items-center justify-center rounded-xl bg-background text-2xl font-bold text-mono">
                                {{ $submission->score }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-mono">Skor Anda</p>
                                <p class="text-xs text-secondary-foreground">Dari skala 0–100</p>
                                <span
                                    class="kt-badge kt-badge-sm kt-badge-{{ $submission->scoreTone() }} kt-badge-outline mt-2">
                                    @if ($submission->score >= 80)
                                        Sangat Baik
                                    @elseif ($submission->score >= 60)
                                        Cukup
                                    @else
                                        Perlu Perbaikan
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if ($submission->hasFeedback())
                            <div>
                                <p class="mb-2 text-sm font-medium text-mono">Feedback Dosen</p>
                                <div class="rounded-lg border border-border bg-accent/30 p-4">
                                    <p class="whitespace-pre-wrap text-sm leading-relaxed text-secondary-foreground">
                                        {{ $submission->feedback }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @elseif ($submission)
                <div class="kt-card">
                    <div class="kt-card-content flex items-center gap-3 p-5 text-sm text-secondary-foreground">
                        <i class="ki-filled ki-medal-star text-lg text-muted-foreground"></i>
                        Belum ada penilaian dari dosen untuk jawaban Anda.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
