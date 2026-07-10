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
                <div class="kt-menu" data-kt-menu="true">
                    <div data-kt-dropdown="true" data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click" data-kt-dropdown-initialized="true">
                        <button type="button" class="kt-btn kt-btn-outline kt-dropdown-toggle" data-kt-dropdown-toggle="true">
                            <i class="ki-filled ki-file-down"></i>
                            Export Nilai
                            <i class="ki-filled ki-down text-xs"></i>
                        </button>
                        <div class="kt-dropdown-menu kt-menu-default w-48">
                            <div class="kt-menu-item">
                                <a class="kt-menu-link" href="{{ route('dosen.elearning.grades.export.excel', $course) }}">
                                    <span class="kt-menu-icon">
                                        <i class="ki-filled ki-some-files"></i>
                                    </span>
                                    <span class="kt-menu-title">Excel (.xlsx)</span>
                                </a>
                            </div>
                            <div class="kt-menu-item">
                                <a class="kt-menu-link" href="{{ route('dosen.elearning.grades.export.pdf', $course) }}">
                                    <span class="kt-menu-icon">
                                        <i class="ki-filled ki-document"></i>
                                    </span>
                                    <span class="kt-menu-title">PDF</span>
                                </a>
                            </div>
                        </div>
                        <div class="kt-dropdown-menu w-full max-w-[220px] hidden" data-kt-dropdown-menu="true" style="opacity: 0;">
                            <ul class="kt-dropdown-menu-sub">
                             <li>
                                <a class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" href="{{ route('dosen.elearning.grades.export.excel', $course) }}">
                                    <span class="kt-menu-icon">
                                        <i class="ki-filled ki-some-files"></i>
                                    </span>
                                    <span class="kt-menu-title">Excel (.xlsx)</span>
                                </a>
                             </li>
                             <li>
                                <a class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" href="{{ route('dosen.elearning.grades.export.pdf', $course) }}">
                                    <span class="kt-menu-icon">
                                        <i class="ki-filled ki-document"></i>
                                    </span>
                                    <span class="kt-menu-title">PDF</span>
                                </a>
                             </li>
                             
                            </ul>
                           </div>
                    </div>
                </div>
               
                <div data-kt-dropdown="true" data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click" data-kt-dropdown-initialized="true" class="">
                    <button class="kt-dropdown-toggle kt-btn kt-btn-primary" data-kt-dropdown-toggle="true">
                        <i class="ki-filled ki-down"></i>
                        Kelola Mata Kuliah
                    </button>
                    <div class="kt-dropdown-menu w-full max-w-[220px] hidden" data-kt-dropdown-menu="true" style="opacity: 0;">
                     <ul class="kt-dropdown-menu-sub">
                      <li>
                        <button  class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" wire:click="toggleEditForm">
                            <i class="ki-filled ki-notepad-edit"></i>
                           <span class="kt-menu-title">Edit Kelas</span>
                        </button>
                      </li>
                      <li>
                        <a  href="{{ route('dosen.elearning.materials.create', $course) }}" class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" wire:click="toggleEditForm">
                            <i class="ki-filled ki-book"></i>
                          
                            <span class="kt-menu-title">Tambah Materi</span>
                        </a>
                      </li>
                      <li>
                        <a  href="{{ route('dosen.elearning.assignments.create', $course) }}" class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" wire:click="toggleEditForm">
                            <i class="ki-filled ki-clipboard"></i>
                            <span class="kt-menu-title">Tambah Tugas</span>
                        </a>
                      </li>
                      <li>
                        <a href="{{ route('dosen.elearning.grades.settings', $course) }}" class="kt-dropdown-menu-link" data-kt-dropdown-dismiss="true" wire:click="toggleEditForm">
                            <i class="ki-filled ki-setting-2"></i>
                            <span class="kt-menu-title">Pengaturan Nilai</span>
                        </a>
                      </li>
                      <li>
                        <div class="kt-dropdown-menu-separator"></div>
                      </li>
                      <li>
                        <button type="button" class="kt-dropdown-menu-link text-destructive"
                            data-kt-dropdown-dismiss="true" data-kt-modal-toggle="#delete_course_modal"
                            wire:click="resetDeleteCourseForm">
                            <i class="ki-filled ki-trash"></i>
                            Hapus Kelas
                        </button>
                      </li>
                     </ul>
                    </div>
                   </div>
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

        <div class="mb-7.5 grid grid-cols-3 gap-5">
            <button type="button"
                class="kt-card w-full text-start transition-colors hover:bg-accent/40"
                data-kt-drawer-toggle="#course_students_drawer">
                <div class="kt-card-content flex flex-col gap-1 p-5">
                    <span class="text-2sm text-secondary-foreground">Mahasiswa Terdaftar</span>
                    <span class="text-2xl font-semibold text-mono">{{ $course->students->count() }}</span>
                </div>
            </button>
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

        @if ($showEditForm)
            <div class="kt-card mb-7.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Edit Mata Kuliah</h3>
                </div>
                <form wire:submit.prevent="updateCourse" class="kt-card-content flex flex-col gap-5">
                    <div class="grid gap-5 lg:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="editTitle">Nama Mata Kuliah</label>
                            <input id="editTitle" type="text" class="kt-input" wire:model="editTitle"
                                placeholder="Contoh: Pemrograman Web" />
                            @error('editTitle')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="editCode">Kode Mata Kuliah</label>
                            <input id="editCode" type="text" class="kt-input uppercase" wire:model="editCode"
                                placeholder="Contoh: PW101" />
                            @error('editCode')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="editDescription">Deskripsi</label>
                        <textarea id="editDescription" rows="3" class="kt-textarea" wire:model="editDescription"
                            placeholder="Deskripsi singkat mata kuliah"></textarea>
                        @error('editDescription')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button type="submit" class="kt-btn kt-btn-primary">Simpan Perubahan</button>
                        <button type="button" class="kt-btn kt-btn-outline" wire:click="toggleEditForm">Batal</button>
                    </div>
                </form>
            </div>
        @endif

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
                            <a href="{{ route('dosen.elearning.materials.create', $course) }}"
                                class="kt-btn kt-btn-primary" wire:navigate>
                                Tambah Materi
                            </a>
                        </div>
                    @else
                        <div class="kt-scrollable-y-auto max-h-[360px]" data-kt-scrollable="true"
                            data-kt-scrollable-max-height="360px">
                            <div class="divide-y divide-border">
                                @foreach ($course->materials as $material)
                                    <div class="flex items-start justify-between gap-3 p-4" wire:key="material-{{ $material->id }}">
                                        <div class="flex min-w-0 items-start gap-3">
                                            <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                                @if ($material->type === 'video')
                                                    <i class="ki-filled ki-youtube"></i>
                                                @elseif ($material->type === 'link')
                                                    <i class="ki-filled ki-exit-right-corner"></i>
                                                @else
                                                    <i class="ki-filled ki-document"></i>
                                                @endif
                                            </span>
                                            <div class="min-w-0">
                                                <h4 class="text-sm font-semibold text-mono">
                                                    @if ($material->isImageFile())
                                                    <a href="{{ $material->fileUrl() }}" target="_blank"
                                                        >
                                                        {{ $material->title }}
                                                    </a>
                                                    @else
                                                    <a href="{{ $material->content }}" target="_blank">{{ $material->title }}</a>    
                                                @endif
                                                </h4>
                                                <p class="mt-1 text-xs text-secondary-foreground">
                                                    {{ ucfirst($material->type) }}
                                                   
                                                </p>
                                              
                                            </div>
                                        </div>
                                        <div class="flex shrink-0 items-center gap-2">
                                            @if ($material->hasFile())
                                                <a href="{{ $material->fileDownloadUrl() }}"
                                                    class="kt-btn kt-btn-sm kt-btn-outline">
                                                    <i class="ki-filled ki-cloud-download text-xs"></i>
                                                    Unduh
                                                </a>
                                            @endif
                                            <button type="button" class="kt-btn kt-btn-sm kt-btn-outline text-destructive"
                                                wire:click="deleteMaterial({{ $material->id }})"
                                                wire:confirm="Hapus materi ini?">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 items-start gap-5">
            <div class="kt-card flex flex-col">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Tugas</h3>
                </div>
                <div class="kt-card-content flex flex-col gap-0 p-0">
                    @if ($course->assignments->isEmpty())
                        <div class="flex flex-col items-center gap-3 p-10 text-center">
                            <i class="ki-filled ki-clipboard text-4xl text-muted-foreground"></i>
                            <p class="text-sm text-secondary-foreground">Belum ada tugas untuk mata kuliah ini.</p>
                            <a href="{{ route('dosen.elearning.assignments.create', $course) }}"
                                class="kt-btn kt-btn-primary" wire:navigate>
                                Tambah Tugas
                            </a>
                        </div>
                    @else
                        <div class="kt-scrollable-y-auto max-h-[360px]" data-kt-scrollable="true"
                            data-kt-scrollable-max-height="360px">
                            <div class="divide-y divide-border">
                                @foreach ($course->assignments as $assignment)
                                    <a href="{{ route('dosen.elearning.assignments.show', [$course, $assignment]) }}"
                                        class="flex flex-col gap-2 p-4 transition-colors hover:bg-accent/40"
                                        wire:navigate wire:key="assignment-{{ $assignment->id }}">
                                        <div class="flex items-start gap-2">
                                            <span class="kt-btn kt-btn-icon kt-btn-outline kt-btn-sm shrink-0">
                                                <i class="ki-filled ki-clipboard"></i>
                                            </span>
                                            <div class="min-w-0 grow">
                                                <div class="mb-1 flex flex-wrap items-center gap-1.5">
                                                    <h4 class="truncate text-sm font-semibold text-mono">{{ $assignment->title }}</h4>
                                                    @if ($assignment->isOverdue() && ! $assignment->acceptsLateSubmissions())
                                                        <span class="kt-badge kt-badge-sm kt-badge-danger">Berakhir</span>
                                                    @elseif ($assignment->isOverdue())
                                                        <span class="kt-badge kt-badge-sm kt-badge-warning">Terbuka</span>
                                                    @else
                                                        <span class="kt-badge kt-badge-sm kt-badge-success">Aktif</span>
                                                    @endif
                                                </div>
                                                <p class="text-2xs text-muted-foreground">
                                                    {{ $assignment->due_date->format('d M Y, H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="kt-drawer kt-drawer-end card bottom-5 end-5 top-5 hidden w-[450px] max-w-[90%] flex-col rounded-xl border border-border"
        data-kt-drawer="true" data-kt-drawer-container="body" id="course_students_drawer">
        <div class="flex items-center justify-between gap-2.5 border-b border-b-border px-5 py-3.5">
            <div class="flex flex-col gap-0.5">
                <h3 class="text-sm font-semibold text-mono">Mahasiswa Terdaftar</h3>
                <p class="text-xs text-secondary-foreground">{{ $course->title }}</p>
                <p class="mt-1 text-xs text-muted-foreground">
                    Kode kelas:
                    <span class="font-medium text-mono text-foreground">{{ $course->code }}</span>
                
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="kt-badge kt-badge-sm kt-badge-outline">{{ $course->students->count() }} orang</span>
                <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>
        </div>
        <div class="grow overflow-hidden">
            @if ($course->students->isEmpty())
                <div class="flex flex-col items-center gap-3 p-10 text-center">
                    <i class="ki-filled ki-people text-4xl text-muted-foreground"></i>
                    <p class="text-sm text-secondary-foreground">Belum ada mahasiswa yang terdaftar.</p>
                </div>
            @else
                <div class="kt-scrollable-y-auto h-full" data-kt-scrollable="true">
                    <div class="divide-y divide-border">
                        @foreach ($course->students as $student)
                            <div class="flex items-center gap-3 px-5 py-3.5" wire:key="student-{{ $student->id }}">
                                <span
                                    class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </span>
                                <div class="min-w-0 grow">
                                    <p class="truncate text-sm font-medium text-mono" title="{{ $student->name }}">
                                        {{ $student->name }}
                                    </p>
                                    <p class="truncate text-xs text-secondary-foreground" title="{{ $student->email }}">
                                        {{ $student->email }}
                                    </p>
                                </div>
                                <span class="shrink-0 text-xs text-muted-foreground">
                                    {{ \Illuminate\Support\Carbon::parse($student->pivot->enrolled_at)->format('d M Y') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="kt-modal kt-modal-center" data-kt-modal="true" id="delete_course_modal" wire:ignore.self>
        <div class="kt-modal-content max-w-[400px] w-full">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">Hapus Kelas</h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal"
                    data-kt-modal-dismiss="#delete_course_modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="deleteCourse">
                <div class="kt-modal-body flex flex-col gap-4">
                    <div class="kt-alert kt-alert-warning flex items-start gap-2">
                        <i class="ki-filled ki-information-2 mt-0.5"></i>
                        <div class="text-sm">
                            Anda akan menghapus kelas <span class="font-semibold text-mono">{{ $course->title }}</span>
                            ({{ $course->code }}). Semua materi, tugas, nilai, dan data mahasiswa terdaftar akan ikut
                            terhapus. Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="deleteCoursePassword">
                            Masukkan password Anda untuk konfirmasi
                        </label>
                        <input id="deleteCoursePassword" type="password" class="kt-input"
                            wire:model="deleteCoursePassword" autocomplete="current-password"
                            placeholder="Password akun Anda" />
                        @error('deleteCoursePassword')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="kt-modal-footer gap-2.5">
                    <button type="button" class="kt-btn kt-btn-outline" data-kt-modal-dismiss="#delete_course_modal"
                        wire:click="resetDeleteCourseForm">
                        Batal
                    </button>
                    <button type="submit" class="kt-btn kt-btn-destructive" wire:loading.attr="disabled"
                        wire:target="deleteCourse">
                        <span wire:loading.remove wire:target="deleteCourse">Hapus Kelas</span>
                        <span wire:loading wire:target="deleteCourse">Menghapus...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
