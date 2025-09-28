<?php

use App\Http\Controllers\Admin\NonTeachingStaffController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Guru\ProfileController as GuruProfileController;

// Public routes (no authentication required)
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/search/guru', [PublicController::class, 'searchGuru'])->name('public.search-guru');
Route::get('/search/siswa', [PublicController::class, 'searchSiswa'])->name('public.search-siswa');
Route::get('/search/non-teaching-staff', [PublicController::class, 'searchNonTeachingStaff'])->name('public.search-non-teaching-staff');
Route::get('/search/sekolah', [PublicController::class, 'searchSekolah'])->name('public.search-sekolah');
Route::get('/detail-guru/{id}', [PublicController::class, 'detailGuru'])->name('public.detail-guru');
Route::get('/detail-siswa/{id}', [PublicController::class, 'detailSiswa'])->name('public.detail-siswa');
Route::get('/detail-sekolah/{id}', [PublicController::class, 'detailSekolah'])->name('public.detail-sekolah');
Route::get('/non-teaching-staff/{id}', [PublicController::class, 'detailNonTeachingStaff'])->name('public.detail-non-teaching-staff');
Route::get('/statistik', [PublicController::class, 'statistik'])->name('statistik');
Route::get('/statistik/{type}', [PublicController::class, 'statistikDetail'])->name('statistik.detail');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Main guru routes
    Route::get('/profile', [App\Http\Controllers\Guru\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/students', [TeacherController::class, 'students'])->name('students');
    Route::get('/reports', [TeacherController::class, 'reports'])->name('reports');
    Route::get('/report/{report}', [TeacherController::class, 'reportDetail'])->name('report.detail');
    Route::get('/documents', [App\Http\Controllers\Guru\DocumentController::class, 'index'])->name('documents');

    // Profile management routes
    Route::get('profile/edit', [GuruProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [GuruProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [GuruProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('profile/photo', [GuruProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::get('profile/print', [GuruProfileController::class, 'print'])->name('profile.print');

    // Documents management routes
    Route::get('documents/{document}', [App\Http\Controllers\Guru\DocumentController::class, 'show'])->name('documents.show');
    Route::post('documents', [App\Http\Controllers\Guru\DocumentController::class, 'store'])->name('documents.store');
    Route::delete('documents/{document}', [App\Http\Controllers\Guru\DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('documents/{document}/download', [App\Http\Controllers\Guru\DocumentController::class, 'download'])->name('documents.download');
});

// Public Routes (tanpa authentication) - CLEANED UP
// Removed duplicate routes: /cari-guru, /cari-siswa (use /search/* instead)
// Removed: /detail-siswa/{id} (use /siswa/{id} instead for consistency)

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Logika untuk menampilkan dashboard berdasarkan role
        if (auth()->user()->hasRole('admin_dinas')) {
            // Logika dashboard admin
            $stats = [
                'total_sekolah' => \App\Models\School::count(),
                'total_guru' => \App\Models\Teacher::count(),
                'total_siswa_aktif' => \App\Models\Student::where('status', 'Aktif')->count(),
                'total_siswa_tamat' => \App\Models\Student::where('status', 'Tamat')->count(),
            ];
            return view('public.index', compact('stats'));
        }
        return view('public.index');
    })->name('dashboard');

    // Admin Routes
    Route::middleware('role:admin_dinas')->prefix('dinas')->name('dinas.')->group(function () {
        // Route download template harus di atas resource!
        Route::get('schools/template-excel', [SchoolController::class, 'downloadTemplateSekolah'])
            ->name('schools.template');
        Route::get('students/template-excel', [App\Http\Controllers\Admin\StudentController::class, 'downloadTemplateSiswa'])->name('students.template');
        Route::post('students/import', [App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
        // Import and template routes for schools
        Route::post('schools/import', [SchoolController::class, 'import'])->name('schools.import');
        Route::get('students/template-excel', [StudentController::class, 'downloadTemplateSiswa'])->name('students.template');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('teachers/template-excel', [TeacherController::class, 'downloadTemplateGuru'])->name('teachers.template');
        Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
        Route::get('non-teaching-staff/template-excel', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'downloadTemplateStaff'])->name('non-teaching-staff.template');
        Route::post('non-teaching-staff/import', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'import'])->name('non-teaching-staff.import');

        // Export routes harus di atas resource routes!
        Route::get('/reports/teachers/export', [App\Http\Controllers\Admin\ReportController::class, 'exportTeachers'])->name('reports.teachers.export');
        Route::get('/reports/students/export', [App\Http\Controllers\Admin\ReportController::class, 'exportStudents'])->name('reports.students.export');
        Route::get('/reports/graduation/export', [App\Http\Controllers\Admin\ReportController::class, 'exportGraduation'])->name('reports.graduation.export');
        Route::get('/reports/non-teaching-staff/export', [App\Http\Controllers\Admin\ReportController::class, 'exportNonTeachingStaff'])->name('reports.non-teaching-staff.export');
        Route::get('/reports/schools/export', [App\Http\Controllers\Admin\ReportController::class, 'exportSchools'])->name('reports.schools.export');

        // Report routes
        Route::get('/reports/schools', [App\Http\Controllers\Admin\ReportController::class, 'schoolsReport'])->name('reports.schools');
        Route::get('/reports/teachers', [App\Http\Controllers\Admin\ReportController::class, 'teachersReport'])->name('reports.teachers');
        Route::get('/reports/students', [App\Http\Controllers\Admin\ReportController::class, 'studentsReport'])->name('reports.students');
        Route::get('/reports/graduation', [App\Http\Controllers\Admin\ReportController::class, 'graduationReport'])->name('reports.graduation');
        Route::get('/reports/non-teaching-staff', [App\Http\Controllers\Admin\ReportController::class, 'nonTeachingStaffReport'])->name('reports.non-teaching-staff');

        Route::resource('schools', App\Http\Controllers\Admin\SchoolController::class);
        Route::get('schools/{school}/print', [App\Http\Controllers\Admin\SchoolController::class, 'print'])->name('schools.print');
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
        Route::get('students/{student}/print', [App\Http\Controllers\Admin\StudentController::class, 'print'])->name('students.print');
        Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
        Route::get('teachers/{teacher}/print', [App\Http\Controllers\Admin\TeacherController::class, 'print'])->name('teachers.print');
        Route::resource('non-teaching-staff', App\Http\Controllers\Admin\NonTeachingStaffController::class);
        Route::get('non-teaching-staff/{nonTeachingStaff}/print', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'print'])->name('non-teaching-staff.print');
        Route::resource('user-management', App\Http\Controllers\Admin\UserManagementController::class)->parameters([
            'user-management' => 'user'
        ]);

        // Reports routes
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        // Student Certificate Routes (tambahkan untuk admin dinas)
        Route::get('students/{student}/certificate/upload', [StudentController::class, 'createCertificate'])->name('students.certificate.create');
        Route::post('students/{student}/certificate', [StudentController::class, 'storeCertificate'])->name('students.certificate.store');
        Route::get('students/{student}/certificate', [StudentController::class, 'showCertificate'])->name('students.certificate.show');
        Route::delete('students/{student}/certificate/{certificate}', [StudentController::class, 'deleteCertificate'])->name('students.certificate.delete');
        // Student Report (Raport) Routes
        Route::get('students/{student}/reports/create', [\App\Http\Controllers\Admin\StudentReportController::class, 'create'])->name('students.reports.create');
        Route::post('students/{student}/reports', [\App\Http\Controllers\Admin\StudentReportController::class, 'store'])->name('students.reports.store');
        Route::delete('students/{student}/reports/{report}', [\App\Http\Controllers\Admin\StudentReportController::class, 'destroy'])->name('students.reports.destroy');
    });

    // Admin Sekolah Routes
    Route::middleware('role:admin_sekolah')->prefix('sekolah')->name('sekolah.')->group(function () {
        Route::get('teachers/template-excel', [App\Http\Controllers\Admin\TeacherController::class, 'downloadTemplateGuru'])->name('teachers.template');
        Route::post('teachers/import', [App\Http\Controllers\Admin\TeacherController::class, 'import'])->name('teachers.import');
        Route::get('non-teaching-staff/template-excel', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'downloadTemplateStaff'])->name('non-teaching-staff.template');
        Route::post('non-teaching-staff/import', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'import'])->name('non-teaching-staff.import');
        Route::get('students/template-excel', [App\Http\Controllers\Admin\StudentController::class, 'downloadTemplateSiswa'])->name('students.template');
        Route::post('students/import', [App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
        Route::resource('students', App\Http\Controllers\Admin\StudentController::class);
        Route::get('students/{student}/print', [App\Http\Controllers\Admin\StudentController::class, 'print'])->name('students.print');
        Route::resource('teachers', App\Http\Controllers\Admin\TeacherController::class);
        Route::get('teachers/{teacher}/print', [App\Http\Controllers\Admin\TeacherController::class, 'print'])->name('teachers.print');
        Route::resource('non-teaching-staff', App\Http\Controllers\Admin\NonTeachingStaffController::class);
        Route::get('non-teaching-staff/{nonTeachingStaff}/print', [App\Http\Controllers\Admin\NonTeachingStaffController::class, 'print'])->name('non-teaching-staff.print');
        Route::resource('user-management', App\Http\Controllers\Admin\UserManagementController::class)->parameters([
            'user-management' => 'user'
        ]);

        // Reports routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
            Route::get('/students', [App\Http\Controllers\Admin\ReportController::class, 'schoolStudentsReport'])->name('students');
            Route::get('/teachers', [App\Http\Controllers\Admin\ReportController::class, 'schoolTeachersReport'])->name('teachers');
            Route::get('/raport', [App\Http\Controllers\Admin\ReportController::class, 'schoolReportsReport'])->name('raport');
        });

        // Student Certificate Routes
        Route::get('students/{student}/certificate/upload', [StudentController::class, 'createCertificate'])->name('students.certificate.create');
        Route::post('students/{student}/certificate', [StudentController::class, 'storeCertificate'])->name('students.certificate.store');
        Route::get('students/{student}/certificate', [StudentController::class, 'showCertificate'])->name('students.certificate.show');
        Route::delete('students/{student}/certificate/{certificate}', [StudentController::class, 'deleteCertificate'])->name('students.certificate.delete');

        // Student Report (Raport) Routes
        Route::get('students/{student}/reports/create', [\App\Http\Controllers\Admin\StudentReportController::class, 'create'])->name('students.reports.create');
        Route::post('students/{student}/reports', [\App\Http\Controllers\Admin\StudentReportController::class, 'store'])->name('students.reports.store');
        Route::delete('students/{student}/reports/{report}', [\App\Http\Controllers\Admin\StudentReportController::class, 'destroy'])->name('students.reports.destroy');
    });
});

// Route test tanpa middleware untuk debug download template
Route::get('/test-template', [App\Http\Controllers\Admin\SchoolController::class, 'downloadTemplateSekolah']);
