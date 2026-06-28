<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Jawaban — {{ $submission->student->name }} · {{ $assignment->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />
</head>

<body class="flex min-h-full flex-col bg-muted/30 text-base text-foreground antialiased">
    <header class="sticky top-0 z-10 border-b border-border bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/80">
        <div class="kt-container-fixed py-4">
            <x-kt-breadcrumb :items="[
                ['label' => 'E-Learning', 'url' => route('dosen.elearning.index'), 'wire' => false],
                ['label' => $course->code, 'url' => route('dosen.elearning.show', $course), 'wire' => false],
                [
                    'label' => $assignment->title,
                    'url' => route('dosen.elearning.assignments.show', [$course, $assignment]),
                    'wire' => false,
                ],
                ['label' => 'Jawaban Mahasiswa'],
            ]" />

            @if ($submissionTotal > 1)
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-border bg-muted/30 px-4 py-3">
                    
                    <span class="text-sm text-secondary-foreground">
                        <span class="font-medium text-mono">{{ $submissionPosition }}</span>
                        dari {{ $submissionTotal }} pengumpulan
                    </span>

                    <div class="flex items-center gap-2">
                        @if ($previousSubmission)
                            <a href="{{ route('dosen.elearning.submissions.show', [$course, $assignment, $previousSubmission]) }}"
                                class="kt-btn kt-btn-sm kt-btn-outline" title="{{ $previousSubmission->student->name }}">
                                <i class="ki-filled ki-left"></i>
                                Sebelumnya
                            </a>
                        @else
                            <span class="kt-btn kt-btn-sm kt-btn-outline pointer-events-none opacity-40">
                                <i class="ki-filled ki-left"></i>
                                Sebelumnya
                            </span>
                        @endif
                        @if ($nextSubmission)
                            <a href="{{ route('dosen.elearning.submissions.show', [$course, $assignment, $nextSubmission]) }}"
                                class="kt-btn kt-btn-sm kt-btn-outline" title="{{ $nextSubmission->student->name }}">
                                Berikutnya
                                <i class="ki-filled ki-right"></i>
                            </a>
                        @else
                            <span class="kt-btn kt-btn-sm kt-btn-outline pointer-events-none opacity-40">
                                Berikutnya
                                <i class="ki-filled ki-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-4">
                    <span
                        class="inline-flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-base font-semibold text-primary">
                        {{ strtoupper(substr($submission->student->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <h1 class="truncate text-lg font-semibold text-mono" title="{{ $submission->student->name }}">
                            {{ $submission->student->name }}
                        </h1>
                        <p class="truncate text-sm text-secondary-foreground">{{ $submission->student->email }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1">
                                <i class="ki-filled ki-calendar text-xs"></i>
                                {{ $submission->submitted_at->locale('id')->translatedFormat('d M Y, H:i') }}
                            </span>
                            <span class="text-border">·</span>
                            <span class="kt-badge kt-badge-sm kt-badge-success kt-badge-outline">Sudah Dikumpulkan</span>
                            @if ($submission->isLate())
                                <span class="kt-badge kt-badge-sm kt-badge-warning kt-badge-outline">Terlambat</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($submission->hasFile())
                        <a href="{{ $submission->fileUrl('dosen') }}" target="_blank" rel="noopener"
                            class="kt-btn kt-btn-sm kt-btn-outline">
                            <i class="ki-filled ki-exit-right-corner"></i>
                            Buka File
                        </a>
                        <a href="{{ $submission->fileDownloadUrl('dosen') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                            <i class="ki-filled ki-cloud-download"></i>
                            Unduh
                        </a>
                    @endif
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" onclick="window.close()">
                        <i class="ki-filled ki-cross"></i>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="kt-container-fixed grow py-7.5">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-4 flex flex-wrap items-center gap-2 text-sm text-secondary-foreground">
            <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $course->code }}</span>
            <span>{{ $assignment->title }}</span>
        </div>

        @if ($submission->content || $submission->hasFile())
            @php
                $hasFilePreview = $submission->hasFile() && ($submission->isPdfFile() || $submission->isImageFile());
            @endphp
            <div class="grid items-start gap-5 @if ($hasFilePreview) lg:grid-cols-3 @endif">
                <div class="flex flex-col gap-5 @if ($hasFilePreview) lg:col-span-1 @endif">
                    @if ($submission->content)
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">Jawaban Teks</h3>
                            </div>
                            <div class="kt-card-content">
                                <p class="whitespace-pre-wrap text-sm leading-relaxed text-secondary-foreground">
                                    {{ $submission->content }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Penilaian</h3>
                            @if ($submission->isGraded())
                                <span class="kt-badge kt-badge-sm kt-badge-{{ $submission->scoreTone() }} kt-badge-outline">
                                    Skor {{ $submission->score }}
                                </span>
                            @endif
                        </div>
                        <form method="POST"
                            action="{{ route('dosen.elearning.submissions.grade', [$course, $assignment, $submission]) }}"
                            class="kt-card-content flex flex-col gap-4">
                            @csrf
                            @method('PATCH')
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="score">Skor (0–100)</label>
                                <input id="score" type="number" name="score" min="0" max="100" step="1"
                                    class="kt-input" placeholder="Contoh: 85"
                                    value="{{ old('score', $submission->score) }}" />
                                @error('score')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="feedback">Feedback</label>
                                <textarea id="feedback" name="feedback" rows="5" class="kt-input"
                                    placeholder="Berikan catatan atau koreksi untuk mahasiswa...">{{ old('feedback', $submission->feedback) }}</textarea>
                                @error('feedback')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>
                            @if ($submission->feedback_at)
                                <p class="text-xs text-muted-foreground">
                                    Terakhir dinilai
                                    {{ $submission->feedback_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                </p>
                            @endif
                            <div class="flex items-center gap-2">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-check"></i>
                                    Simpan Penilaian
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($submission->hasFile())
                    <div class="@if ($hasFilePreview) lg:col-span-2 @endif h-full">
                        <div class="kt-card flex flex-col overflow-hidden h-full">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">File Jawaban</h3>
                                <div class="kt-card-toolbar">
                                    <span class="truncate text-xs text-secondary-foreground max-w-[200px]">
                                        {{ $submission->file_name }}
                                    </span>
                                </div>
                            </div>
                            <div class="kt-card-content p-0 h-full">
                                @if ($submission->isImageFile())
                                    <div class="flex items-center justify-center bg-muted/20 p-5">
                                        <img src="{{ $submission->fileUrl('dosen') }}"
                                            alt="{{ $submission->file_name }}"
                                            class="max-h-[90vh] min-h-[60vh] w-full rounded-lg object-contain" />
                                    </div>
                                @elseif ($submission->isPdfFile())
                                    <iframe src="{{ $submission->fileUrl('dosen') }}"
                                        title="{{ $submission->file_name }}"
                                        class="block h-full min-h-[60vh] w-full border-0 bg-white"></iframe>
                                @else
                                    <div class="flex flex-col items-center gap-4 p-12 text-center">
                                        <span class="kt-btn kt-btn-icon kt-btn-outline size-14">
                                            <i class="ki-filled ki-document text-2xl"></i>
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-mono">{{ $submission->file_name }}</p>
                                            <p class="mt-1 text-sm text-secondary-foreground">
                                                Preview tidak tersedia. Buka atau unduh file untuk melihat isinya.
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <a href="{{ $submission->fileUrl('dosen') }}" target="_blank"
                                                rel="noopener" class="kt-btn kt-btn-primary">
                                                <i class="ki-filled ki-exit-right-corner"></i>
                                                Buka File
                                            </a>
                                            <a href="{{ $submission->fileDownloadUrl('dosen') }}"
                                                class="kt-btn kt-btn-outline">
                                                <i class="ki-filled ki-cloud-download"></i>
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="kt-card">
                <div class="kt-card-content flex flex-col items-center gap-3 p-16 text-center">
                    <i class="ki-filled ki-document text-5xl text-muted-foreground"></i>
                    <h3 class="text-base font-medium text-mono">Tidak ada konten jawaban</h3>
                    <p class="text-sm text-secondary-foreground">Mahasiswa belum mengisi teks atau mengunggah file.</p>
                </div>
            </div>
        @endif
    </main>
</body>

</html>
