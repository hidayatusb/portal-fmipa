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
                <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-left"></i>
                    Kembali
                </a>
                <button type="button" class="kt-btn kt-btn-outline" wire:click="toggleEditForm">
                    <i class="ki-filled ki-notepad-edit"></i>
                    {{ $showEditForm ? 'Batal Edit' : 'Edit' }}
                </button>
                <button type="button" class="kt-btn kt-btn-outline kt-btn-destructive" wire:click="deleteAssignment"
                    wire:confirm="Hapus tugas ini?">
                    <i class="ki-filled ki-trash"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @if ($showEditForm)
            <div class="kt-card mb-7.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Edit Tugas</h3>
                </div>
                <form wire:submit.prevent="updateAssignment" class="kt-card-content flex flex-col gap-5">
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="editTitle">Judul Tugas</label>
                            <input id="editTitle" type="text" class="kt-input" wire:model="editTitle"
                                placeholder="Contoh: Tugas 1 - Landing Page" />
                            @error('editTitle')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="editDueDate">Batas Waktu</label>
                            <input id="editDueDate" type="datetime-local" class="kt-input" wire:model="editDueDate" />
                            @error('editDueDate')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 rounded-lg border border-border p-4">
                        <label class="flex items-start gap-2.5">
                            <input type="checkbox" class="kt-checkbox kt-checkbox-sm mt-0.5"
                                wire:model="editAcceptLateSubmissions" />
                            <span class="flex flex-col gap-0.5">
                                <span class="text-sm font-medium text-mono">Terima pengumpulan setelah deadline</span>
                                <span class="text-xs text-secondary-foreground">
                                    Jika tidak dicentang, mahasiswa tidak dapat mengumpulkan tugas setelah batas waktu berakhir.
                                </span>
                            </span>
                        </label>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="editDescription">Instruksi Tugas</label>
                        <textarea id="editDescription" rows="5" class="kt-input" wire:model="editDescription"
                            placeholder="Jelaskan detail tugas yang harus dikerjakan mahasiswa"></textarea>
                        @error('editDescription')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="editAttachment">Lampiran (Gambar /
                            Dokumen)</label>

                        @if ($assignment->hasAttachment() && !$removeExistingAttachment)
                            <div class="kt-card border border-border">
                                <div class="kt-card-content flex items-center justify-between gap-4 p-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                            <i class="ki-filled ki-document"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-mono">
                                                {{ $assignment->attachment_name }}</p>
                                            <p class="text-xs text-secondary-foreground">Lampiran saat ini</p>
                                        </div>
                                    </div>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                        wire:click="markRemoveExistingAttachment">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @elseif ($assignment->hasAttachment() && $removeExistingAttachment)
                            <div
                                class="flex items-center justify-between gap-3 rounded-lg border border-dashed border-border p-4">
                                <p class="text-sm text-secondary-foreground">Lampiran akan dihapus setelah disimpan.</p>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                    wire:click="undoRemoveExistingAttachment">
                                    Batalkan
                                </button>
                            </div>
                        @endif

                        <input id="editAttachment" type="file" class="kt-input" wire:model="attachment" />
                        <p class="text-xs text-secondary-foreground">
                            Opsional. Unggah file baru untuk mengganti lampiran. Maks. 10 MB.
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
                                            <p class="truncate text-sm font-medium text-mono">
                                                {{ $attachment->getClientOriginalName() }}</p>
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
                        <button type="submit" class="kt-btn kt-btn-primary">Simpan Perubahan</button>
                        <button type="button" class="kt-btn kt-btn-outline" wire:click="toggleEditForm">Batal</button>
                    </div>
                </form>
            </div>
        @endif

        @php
            $tone = $assignment->deadlineTone();

            $toneConfig = match ($tone) {
                'overdue' => [
                    'badge' => 'kt-badge-destructive',
                    'progress' => 'kt-progress-destructive',
                    'alert' => 'kt-alert-destructive',
                    'label' => 'Sudah Berakhir',
                ],
                'urgent' => [
                    'badge' => 'kt-badge-warning',
                    'progress' => 'kt-progress-warning',
                    'alert' => 'kt-alert-warning',
                    'label' => 'Segera Berakhir',
                ],
                default => [
                    'badge' => 'kt-badge-success',
                    'progress' => 'kt-progress-primary',
                    'alert' => null,
                    'label' => 'Aktif',
                ],
            };
        @endphp

        <x-assignment-countdown
            wire:key="assignment-countdown-{{ $assignment->id }}-{{ $assignment->updated_at?->timestamp }}"
            :due-date="$assignment->due_date" :created-at="$assignment->created_at" class="mb-7.5 grid items-stretch gap-5 lg:grid-cols-4">
            <div class="kt-card">
                <div class="kt-card-header">

                    


                    <h3 class="kt-card-title">Informasi Tugas</h3> 

                    @if ($tone === 'active')
                    <span class="kt-badge kt-badge-outline {{ $toneConfig['badge'] }}">{{ $toneConfig['label'] }}</span>
                    @else
                    <span class="kt-badge kt-badge-outline {{ $toneConfig['badge'] }}">{{ $toneConfig['label'] }}</span>
                    @endif
                    
                </div>
                <div class="kt-card-content flex flex-col gap-5">
                    <div class="grid gap-5 sm:grid-cols-3">
                        
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
                        <div class="flex flex-col gap-1">
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

                    <div class="kt-separator"></div>

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
                            <a href="{{ $assignment->attachmentUrl() }}" target="_blank"
                                class="block overflow-hidden rounded-lg border border-border">
                                <img src="{{ $assignment->attachmentUrl() }}"
                                    alt="{{ $assignment->attachment_name }}" class="max-h-72 w-full object-cover" />
                            </a>
                        @else
                            <a href="{{ $assignment->attachmentUrl() }}" target="_blank"
                                class="flex items-center gap-3 rounded-lg border border-border p-4 transition-colors hover:bg-accent/50">
                                <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                    <i class="ki-filled ki-document"></i>
                                </span>
                                <div class="min-w-0 grow">
                                    <p class="truncate text-sm font-medium text-mono">
                                        {{ $assignment->attachment_name ?? 'Buka lampiran' }}
                                    </p>
                                    
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
                    <h3 class="kt-card-title">Pengumpulan Mahasiswa</h3>
                    <span class="kt-badge kt-badge-sm kt-badge-outline">
                        {{ $assignment->submissions->count() }} / {{ $course->students->count() }}
                    </span>
                </div>
                <div class="kt-card-content p-0">
                    @if ($assignment->submissions->isEmpty())
                        <div class="flex flex-col items-center gap-3 p-10 text-center">
                            <i class="ki-filled ki-people text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">Belum ada mahasiswa yang mengumpulkan tugas ini.</p>
                        </div>
                    @else
                        <div class="divide-y divide-border">
                            @foreach ($assignment->submissions->sortByDesc('submitted_at') as $submission)
                                <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4"
                                    wire:key="submission-{{ $submission->id }}">
                                    <div class="flex min-w-0 flex-1 items-center gap-3">
                                        <span
                                            class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-sm font-semibold text-primary">
                                            {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="flex min-w-0 items-center gap-2">
                                                <p class="min-w-0 flex-1 truncate text-sm font-semibold text-mono"
                                                    title="{{ $submission->student->name }}">
                                                    {{ $submission->student->name }}
                                                </p>
                                              
                                                @if ($submission->isGraded())
                                                    <span
                                                        class="kt-badge kt-badge-sm kt-badge-{{ $submission->scoreTone() }} kt-badge-outline shrink-0">
                                                        {{ $submission->score }}
                                                    </span>
                                                @endif
                                                @if ($submission->isLate())
                                                    <span class="kt-badge kt-badge-sm kt-badge-warning kt-badge-outline shrink-0">
                                                        Terlambat
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="truncate text-xs text-secondary-foreground"
                                                title="{{ $submission->student->email }}">
                                                {{ $submission->student->email }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-muted-foreground">
                                                <i class="ki-filled ki-calendar text-2xs"></i>
                                                {{ $submission->submitted_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-2">
                                        <a href="{{ $submission->viewUrl('dosen') }}" target="_blank" rel="noopener"
                                            class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-eye text-xs"></i>
                                            Lihat Jawaban
                                        </a>
                                        @if ($submission->hasFile())
                                            <a href="{{ $submission->fileDownloadUrl('dosen') }}"
                                                class="kt-btn kt-btn-sm kt-btn-outline">
                                                <i class="ki-filled ki-cloud-download text-xs"></i>
                                                Unduh
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
