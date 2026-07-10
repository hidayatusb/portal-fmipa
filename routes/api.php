<?php

use App\Http\Controllers\Api\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Dosen\AssignmentController as DosenAssignmentController;
use App\Http\Controllers\Api\Dosen\CourseController as DosenCourseController;
use App\Http\Controllers\Api\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Api\Dosen\GradeController as DosenGradeController;
use App\Http\Controllers\Api\Dosen\MaterialController as DosenMaterialController;
use App\Http\Controllers\Api\Dosen\SubmissionController as DosenSubmissionController;
use App\Http\Controllers\Api\Mahasiswa\AssignmentController as MahasiswaAssignmentController;
use App\Http\Controllers\Api\Mahasiswa\CourseController as MahasiswaCourseController;
use App\Http\Controllers\Api\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\FcmTopicController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/profile/picture', [ProfileController::class, 'picture'])->name('api.profile.picture');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead']);

    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);
    Route::get('/announcements/{announcement}/image', [AnnouncementController::class, 'image'])
        ->name('api.announcements.image');

    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);
    Route::get('/fcm/topics', [FcmTopicController::class, 'index']);

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class);
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approve']);
        Route::post('/users/{user}/reject', [AdminUserController::class, 'reject']);

        Route::get('/announcements', [AdminAnnouncementController::class, 'index']);
        Route::post('/announcements', [AdminAnnouncementController::class, 'store']);
        Route::get('/announcements/{announcement}', [AdminAnnouncementController::class, 'show']);
        Route::match(['put', 'patch', 'post'], '/announcements/{announcement}', [AdminAnnouncementController::class, 'update']);
        Route::delete('/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy']);
    });

    Route::middleware('role:dosen')->prefix('dosen')->group(function () {
        Route::get('/dashboard', DosenDashboardController::class);

        Route::get('/courses', [DosenCourseController::class, 'index']);
        Route::post('/courses', [DosenCourseController::class, 'store']);
        Route::get('/courses/{course}', [DosenCourseController::class, 'show']);
        Route::match(['put', 'patch'], '/courses/{course}', [DosenCourseController::class, 'update']);
        Route::get('/courses/{course}/grades', [DosenCourseController::class, 'grades']);

        Route::post('/courses/{course}/materials', [DosenMaterialController::class, 'store']);
        Route::delete('/courses/{course}/materials/{material}', [DosenMaterialController::class, 'destroy']);
        Route::get('/courses/{course}/materials/{material}/file', [DosenMaterialController::class, 'file']);

        Route::post('/courses/{course}/assignments', [DosenAssignmentController::class, 'store']);
        Route::get('/courses/{course}/assignments/{assignment}', [DosenAssignmentController::class, 'show']);
        Route::match(['put', 'patch'], '/courses/{course}/assignments/{assignment}', [DosenAssignmentController::class, 'update']);
        Route::delete('/courses/{course}/assignments/{assignment}', [DosenAssignmentController::class, 'destroy']);
        Route::get('/courses/{course}/assignments/{assignment}/attachment', [DosenAssignmentController::class, 'attachment']);

        Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}', [DosenSubmissionController::class, 'show']);
        Route::patch('/courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [DosenSubmissionController::class, 'grade']);
        Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}/file', [DosenSubmissionController::class, 'file']);

        Route::get('/courses/{course}/grades/settings', [DosenGradeController::class, 'show']);
        Route::put('/courses/{course}/grades/settings/weights', [DosenGradeController::class, 'updateWeights']);
        Route::put('/courses/{course}/grades/settings/students', [DosenGradeController::class, 'updateStudentScores']);
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->group(function () {
        Route::get('/dashboard', MahasiswaDashboardController::class);

        Route::get('/courses', [MahasiswaCourseController::class, 'index']);
        Route::post('/courses/join', [MahasiswaCourseController::class, 'join']);
        Route::get('/courses/{course}', [MahasiswaCourseController::class, 'show']);
        Route::get('/courses/{course}/materials/{material}/file', [MahasiswaCourseController::class, 'materialFile']);

        Route::get('/courses/{course}/assignments/{assignment}', [MahasiswaAssignmentController::class, 'show']);
        Route::post('/courses/{course}/assignments/{assignment}/submit', [MahasiswaAssignmentController::class, 'submit']);
        Route::get('/courses/{course}/assignments/{assignment}/attachment', [MahasiswaAssignmentController::class, 'attachment']);
        Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}/file', [MahasiswaAssignmentController::class, 'submissionFile']);
    });
});
