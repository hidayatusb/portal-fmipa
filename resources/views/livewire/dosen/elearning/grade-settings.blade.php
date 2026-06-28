<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Pengaturan Nilai
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    {{ $course->title }}
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('dosen.elearning.show', $course) }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-left"></i>
                    Kembali
                </a>
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

        <div class="kt-card mb-7.5">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Bobot Penilaian</h3>
                <div class="kt-card-toolbar">
                    <span class="kt-badge kt-badge-sm kt-badge-outline">
                        Tersimpan: {{ $course->weight_attendance + $course->weight_assignment + $course->weight_uts + $course->weight_uas }}%
                    </span>
                </div>
            </div>
            <form wire:submit.prevent="saveGradeSettings" class="kt-card-content flex flex-col gap-5">
                <p class="text-sm text-secondary-foreground">
                    Atur persentase bobot untuk kehadiran, tugas, UTS, dan UAS. Total harus 100%.
                </p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="weightAttendance">Kehadiran (%)</label>
                        <input id="weightAttendance" type="number" min="0" max="100" class="kt-input"
                            wire:model="weightAttendance" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="weightAssignment">Tugas (%)</label>
                        <input id="weightAssignment" type="number" min="0" max="100" class="kt-input"
                            wire:model="weightAssignment" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="weightUts">UTS (%)</label>
                        <input id="weightUts" type="number" min="0" max="100" class="kt-input"
                            wire:model="weightUts" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="weightUas">UAS (%)</label>
                        <input id="weightUas" type="number" min="0" max="100" class="kt-input"
                            wire:model="weightUas" />
                    </div>
                </div>
                @error('weightAttendance')
                    <div class="kt-alert kt-alert-danger flex items-center gap-2">
                        <i class="ki-filled ki-information-2"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                <div class="flex items-center gap-2.5">
                    <button type="submit" class="kt-btn kt-btn-primary">
                        Simpan Bobot
                    </button>
                </div>
            </form>
        </div>

        <div class="kt-card">
            <div class="kt-card-header">
                <h3 class="kt-card-title">Nilai Kehadiran, UTS & UAS</h3>
            </div>
            <form wire:submit.prevent="saveStudentGrades" class="kt-card-content flex flex-col gap-4">
                <p class="text-sm text-secondary-foreground">
                    Nilai tugas diambil otomatis dari penilaian pengumpulan tugas mahasiswa.
                </p>

                @if ($course->students->isEmpty())
                    <p class="text-sm text-secondary-foreground">Belum ada mahasiswa terdaftar di kelas ini.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-border">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/40">
                                <tr>
                                    <th class="max-w-[220px] px-4 py-3 text-start font-medium text-mono">Mahasiswa</th>
                                    <th class="max-w-[120px] px-4 py-3 text-start font-medium text-mono">Username</th>
                                    <th class="px-4 py-3 text-center font-medium text-mono" style="width: 110px;">Kehadiran</th>
                                    <th class="px-4 py-3 text-center font-medium text-mono" style="width: 110px;">UTS</th>
                                    <th class="px-4 py-3 text-center font-medium text-mono" style="width: 110px;">UAS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @foreach ($course->students->sortBy('username') as $student)
                                    <tr wire:key="grade-student-{{ $student->id }}">
                                        <td class="max-w-[220px] px-4 py-3">
                                            <p class="truncate font-medium text-mono" title="{{ $student->name }}">
                                                {{ $student->name }}
                                            </p>
                                            <p class="truncate text-xs text-secondary-foreground" title="{{ $student->email }}">
                                                {{ $student->email }}
                                            </p>
                                        </td>
                                        <td class="max-w-[120px] px-4 py-3">
                                            <p class="truncate text-secondary-foreground" title="{{ $student->username }}">
                                                {{ $student->username }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" max="100" class="kt-input text-center"
                                                wire:model="studentGrades.{{ $student->id }}.attendance_score"
                                                placeholder="0-100" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" max="100" class="kt-input text-center"
                                                wire:model="studentGrades.{{ $student->id }}.uts_score"
                                                placeholder="0-100" />
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" min="0" max="100" class="kt-input text-center"
                                                wire:model="studentGrades.{{ $student->id }}.uas_score"
                                                placeholder="0-100" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="submit" class="kt-btn kt-btn-primary">
                            Simpan Nilai Mahasiswa
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
