<?php

use App\Http\Controllers\Dosen\AssignmentAttachmentController as DosenAssignmentAttachmentController;
use App\Http\Controllers\Dosen\CourseGradesExportController;
use App\Http\Controllers\Dosen\MaterialFileController as DosenMaterialFileController;
use App\Http\Controllers\Mahasiswa\AssignmentAttachmentController as MahasiswaAssignmentAttachmentController;
use App\Http\Controllers\Mahasiswa\MaterialFileController as MahasiswaMaterialFileController;
use App\Http\Controllers\Dosen\SubmissionController as DosenSubmissionController;
use App\Http\Controllers\Dosen\SubmissionFileController as DosenSubmissionFileController;
use App\Http\Controllers\Mahasiswa\SubmissionFileController as MahasiswaSubmissionFileController;
use App\Http\Controllers\AnnouncementImageController;
use App\Http\Controllers\ProfilePictureController;
use App\Livewire\Admin\Pengumuman\Create as AdminPengumumanCreate;
use App\Livewire\Admin\Pengumuman\Edit as AdminPengumumanEdit;
use App\Livewire\Admin\Pengumuman\Index as AdminPengumumanIndex;
use App\Livewire\Admin\UserApprovals\Index as AdminUserApprovalsIndex;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Dosen\Elearning\CreateAssignment as DosenCreateAssignment;
use App\Livewire\Dosen\Elearning\CreateMaterial as DosenCreateMaterial;
use App\Livewire\Dosen\Elearning\GradeSettings as DosenGradeSettings;
use App\Livewire\Dosen\Elearning\Index as DosenElearningIndex;
use App\Livewire\Dosen\Elearning\Show as DosenElearningShow;
use App\Livewire\Dosen\Elearning\ShowAssignment as DosenShowAssignment;
use App\Livewire\Mahasiswa\Elearning\Index as MahasiswaElearningIndex;
use App\Livewire\Mahasiswa\Elearning\Show as MahasiswaElearningShow;
use App\Livewire\Mahasiswa\Elearning\ShowAssignment as MahasiswaShowAssignment;
use App\Livewire\Profile\Edit as ProfileEdit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

Route::middleware(['auth', 'approved'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard.index');
    Route::get('/profil', ProfileEdit::class)->name('profile.edit');
    Route::get('/profil/foto', [ProfilePictureController::class, 'show'])->name('profile.picture');
    Route::get('/pengumuman/{announcement}/gambar', [AnnouncementImageController::class, 'show'])
        ->name('announcements.image');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/review-akun', AdminUserApprovalsIndex::class)->name('user-approvals.index');
        Route::get('/pengumuman', AdminPengumumanIndex::class)->name('pengumuman.index');
        Route::get('/pengumuman/tambah', AdminPengumumanCreate::class)->name('pengumuman.create');
        Route::get('/pengumuman/{announcement}/edit', AdminPengumumanEdit::class)->name('pengumuman.edit');
    });

    Route::middleware(['role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/elearning', DosenElearningIndex::class)->name('elearning.index');
        Route::get('/elearning/{course}', DosenElearningShow::class)->name('elearning.show');
        Route::get('/elearning/{course}/nilai/pengaturan', DosenGradeSettings::class)->name('elearning.grades.settings');
        Route::get('/elearning/{course}/nilai/export/excel', [CourseGradesExportController::class, 'excel'])
            ->name('elearning.grades.export.excel');
        Route::get('/elearning/{course}/nilai/export/pdf', [CourseGradesExportController::class, 'pdf'])
            ->name('elearning.grades.export.pdf');
        Route::get('/elearning/{course}/materi/tambah', DosenCreateMaterial::class)->name('elearning.materials.create');
        Route::get('/elearning/{course}/materi/{material}/file', [DosenMaterialFileController::class, 'show'])
            ->name('elearning.materials.file.show');
        Route::get('/elearning/{course}/materi/{material}/file/unduh', [DosenMaterialFileController::class, 'download'])
            ->name('elearning.materials.file.download');
        Route::get('/elearning/{course}/tugas/tambah', DosenCreateAssignment::class)->name('elearning.assignments.create');
        Route::get('/elearning/{course}/tugas/{assignment}', DosenShowAssignment::class)->name('elearning.assignments.show');
        Route::get('/elearning/{course}/tugas/{assignment}/lampiran', [DosenAssignmentAttachmentController::class, 'show'])
            ->name('elearning.assignments.attachment.show');
        Route::get('/elearning/{course}/tugas/{assignment}/lampiran/unduh', [DosenAssignmentAttachmentController::class, 'download'])
            ->name('elearning.assignments.attachment.download');
        Route::get('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}', [DosenSubmissionController::class, 'show'])
            ->name('elearning.submissions.show');
        Route::patch('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}/penilaian', [DosenSubmissionController::class, 'updateGrade'])
            ->name('elearning.submissions.grade');
        Route::get('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}/file', [DosenSubmissionFileController::class, 'show'])
            ->name('elearning.submissions.file.show');
        Route::get('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}/file/unduh', [DosenSubmissionFileController::class, 'download'])
            ->name('elearning.submissions.file.download');
    });

    Route::middleware(['role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/elearning', MahasiswaElearningIndex::class)->name('elearning.index');
        Route::get('/elearning/{course}', MahasiswaElearningShow::class)->name('elearning.show');
        Route::get('/elearning/{course}/tugas/{assignment}', MahasiswaShowAssignment::class)->name('elearning.assignments.show');
        Route::get('/elearning/{course}/materi/{material}/file', [MahasiswaMaterialFileController::class, 'show'])
            ->name('elearning.materials.file.show');
        Route::get('/elearning/{course}/materi/{material}/file/unduh', [MahasiswaMaterialFileController::class, 'download'])
            ->name('elearning.materials.file.download');
        Route::get('/elearning/{course}/tugas/{assignment}/lampiran', [MahasiswaAssignmentAttachmentController::class, 'show'])
            ->name('elearning.assignments.attachment.show');
        Route::get('/elearning/{course}/tugas/{assignment}/lampiran/unduh', [MahasiswaAssignmentAttachmentController::class, 'download'])
            ->name('elearning.assignments.attachment.download');
        Route::get('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}/file', [MahasiswaSubmissionFileController::class, 'show'])
            ->name('elearning.submissions.file.show');
        Route::get('/elearning/{course}/tugas/{assignment}/pengumpulan/{submission}/file/unduh', [MahasiswaSubmissionFileController::class, 'download'])
            ->name('elearning.submissions.file.download');
    });
});

Route::livewire('/login', 'pages::login.index')->name('login');
Route::livewire('/register', 'pages::register.index')->name('register');
