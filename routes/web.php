<?php

use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\BE\AuthController as BEAuthController;
use App\Http\Controllers\BE\ForgotPasswordController;
use App\Http\Controllers\BE\GuruController;
use App\Http\Controllers\BE\KepalaSekolahController;
use App\Http\Controllers\BE\SuperAdminController;
use App\Http\Controllers\BE\JobRecommendataionController;
use App\Http\Controllers\BE\KuisionerController;
use App\Http\Controllers\BE\QuestionnaireControllerOpe;
use App\Http\Controllers\BE\QuestionnaireControllerSiswa;
use App\Http\Controllers\BE\SettingsController;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Http\Controllers\BE\TracerReportController;
use App\Http\Controllers\BE\VisualizationController;
use App\Http\Controllers\FE\AuthController;
use App\Http\Controllers\FE\BlogsControllerFE;
use App\Http\Controllers\FE\DashboardControllerFE;
use App\Http\Controllers\FE\JurusanControllerFE;
use App\Http\Controllers\FE\OperatorControllerFE;
use App\Http\Controllers\FE\SiswaController;
use App\Http\Controllers\FE\SuperadminControllerFE;
use App\Http\Controllers\KuisionerControllerBE;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return view('auth.login'); // Mengembalikan view 'hello.blade.php'
});


Route::get('/', [AuthController::class, 'viewlogin'])->name('login');
Route::get('/register', [AuthController::class, 'viewregister'])->name('register');
Route::post('/dologin', [BEAuthController::class, 'login'])->name('dologin');
Route::post('/logout', [BEAuthController::class, 'logout'])->name('logout');
// Forgot Password Routes
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');


Route::middleware(['role:siswa,guru,operator,superadmin,kepalasekolah', 'check.student.profile'])->group(function () {

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardControllerFE::class, 'index'])->name('dashboard');
    });
    Route::group(['prefix' => 'superadmin'], function () {
        Route::get('/operator', [SuperadminControllerFE::class, 'operator'])->name('superadmin.operator');
        Route::get('/profile', [SuperAdminController::class, 'profile'])->name('superadmin.profile');
        Route::put('/profile', [SuperAdminController::class, 'updateProfile'])->name('superadmin.profile.update');
        Route::put('/password', [SuperAdminController::class, 'changePassword'])->name('superadmin.password.update');
    });

    Route::group(['prefix' => 'operator'], function () {
        Route::get('/profile', [OperatorControllerFE::class, 'profile'])->name('operator.profile');
        Route::get('/tracerstudi', [OperatorControllerFE::class, 'tracer'])->name('tracer');
        Route::get('/data/siswa', [OperatorControllerFE::class, 'viewsiswa'])->name('view.siswa');
        Route::get('/data/jurusan', [JurusanControllerFE::class, 'index'])->name('view.jurusan');
        Route::get('/jobs', [JobRecommendataionController::class, 'index'])->name('operator.jobs.index');

        // Fix questionnaire routes with correct prefix
        Route::get('/kuisioner', [QuestionnaireControllerOpe::class, 'index'])->name('operator.questionnaires.index');
        Route::get('/kuisioner/{questionnaire}/edit', [QuestionnaireControllerOpe::class, 'edit'])->name('operator.questionnaires.edit');
    });


    Route::group(['prefix' => 'siswa'], function () {
        Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
        Route::get('/edit', [SiswaControllerBE::class, 'edit'])->name('student.profile.edit');
        // Rekomndasi
        Route::get('/questionnaire', [QuestionnaireControllerSiswa::class, 'showQuestionnaire'])->name('student.kuis');
        Route::get('/rekomendasi', [QuestionnaireControllerSiswa::class, 'showRecommendation'])
            ->name('student.recommendation.show');
        Route::post('/questionnaire/submit', [QuestionnaireControllerSiswa::class, 'submitQuestionnaire'])
            ->name('student.questionnaire.submit');
        Route::post('/questionnaire/submit/rekomendasi', [QuestionnaireControllerSiswa::class, 'submitRecommendation'])
            ->name('student.questionnaire.submit.rekomendasi');
    });

    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', [BlogsControllerFE::class, 'index'])->name('operator.blog.index');
        Route::get('/create', [BlogsControllerFE::class, 'create'])->name('operator.blog.create');
        Route::get('/edit/{id}', [BlogsControllerFE::class, 'edit'])->name('operator.blog.edit');
    });
});

// Add these routes to your web.php file

// Stats API routes
Route::prefix('api')->group(function () {
    Route::get('/stats/students', [StatsController::class, 'getStudentStats'])
        ->name('api.stats.students');
    Route::get('/stats/tracer', [StatsController::class, 'getTracerStats'])
        ->name('api.stats.tracer')->middleware('auth');
});

// Role-specific dashboard routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/operator', [DashboardControllerFE::class, 'operatorDashboard'])->name('operator.dashboard');
    Route::get('/dashboard/student', [DashboardControllerFE::class, 'studentDashboard'])->name('student.dashboard');
    Route::get('/dashboard/guru', [DashboardControllerFE::class, 'teacherDashboard'])->name('guru.dashboard');
    Route::get('/dashboard/kepalasekolah', [DashboardControllerFE::class, 'KepalaSekolahDashboard'])->name('guru.dashboard');
});

// Guru Profile Routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/profile', [GuruController::class, 'profile'])->name('guru.profile');
    Route::put('/profile', [GuruController::class, 'updateProfile'])->name('guru.profile.update');
    Route::put('/password', [GuruController::class, 'changePassword'])->name('guru.password.update');
});

// Kepala Sekolah Profile Routes
Route::middleware(['auth', 'role:kepalasekolah'])->prefix('kepalasekolah')->group(function () {
    Route::get('/profile', [KepalaSekolahController::class, 'profile'])->name('kepalasekolah.profile');
    Route::put('/profile', [KepalaSekolahController::class, 'updateProfile'])->name('kepalasekolah.profile.update');
    Route::put('/password', [KepalaSekolahController::class, 'updatePassword'])->name('kepalasekolah.password.update');
});

// Operator Settings Routes
Route::middleware(['auth', 'role:operator'])->prefix('operator/settings')->group(function () {
    // View routes only
    Route::get('/general', [SettingsController::class, 'general'])->name('operator.settings.general');
    Route::get('/logo', [SettingsController::class, 'logo'])->name('operator.settings.logo');
    Route::get('/school', [SettingsController::class, 'school'])->name('operator.settings.school');

    // Backup & Restore - views only
    Route::get('/backup', [SettingsController::class, 'backup'])->name('operator.settings.backup');
    Route::get('/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('operator.settings.backup.download');
});

// Clean up duplicate routes and ensure delete method is properly defined
Route::middleware(['auth', 'role:operator'])->prefix('operator/settings')->name('operator.settings.')->group(function () {
    // General settings
    Route::get('/general', [SettingsController::class, 'general'])->name('general');
    // Logo settings
    Route::get('/logo', [SettingsController::class, 'logo'])->name('logo');
    // School information
    Route::get('/school', [SettingsController::class, 'school'])->name('school');
    // Backup & Restore
    Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
    Route::get('/backup/download/{filename}', [SettingsController::class, 'downloadBackup'])->name('backup.download');
});

// Add or update the blog routes
Route::prefix('operator/blog')->middleware(['auth', 'role:operator,guru,superadmin,kepalasekolah'])->group(function () {
    Route::get('/', 'App\Http\Controllers\BlogController@index')->name('operator.blog.index');
    Route::get('/create', 'App\Http\Controllers\BlogController@create')->name('operator.blog.create');
    Route::get('/edit/{id}', 'App\Http\Controllers\BlogController@edit')->name('operator.blog.edit');
});

// Public blog routes (accessible to all users)
Route::get('/blog', 'App\Http\Controllers\BlogController@list')->name('blog.index');
Route::get('/blog/{slug}', 'App\Http\Controllers\BlogController@show')->name('blog.show');

// Operator Routes
Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    // Reports Routes
    Route::get('/reports', [TracerReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [TracerReportController::class, 'generateReport'])->name('reports.generate');
    Route::get('/reports/export', [TracerReportController::class, 'exportRawData'])->name('reports.export');
    Route::get('/reports/data', [TracerReportController::class, 'getReportData'])->name('reports.data');
});

// Add routes for the superadmin visualization feature
Route::middleware(['auth', 'role:operator,superadmin,guru,kepalasekolah'])->prefix('report')->name('superadmin.')->group(function () {
    // Visualization Routes
    Route::get('/tracerstudi', [VisualizationController::class, 'index'])->name('visualizations.index');
    Route::get('/tracerstudi/data', [VisualizationController::class, 'getData'])->name('visualizations.data');
    Route::get('/tracerstudi/export/pdf', [VisualizationController::class, 'exportPdf'])->name('visualizations.export.pdf');
    Route::get('/tracerstudi/export/excel', [VisualizationController::class, 'exportExcel'])->name('visualizations.export.excel');
    Route::get('/tracerstudi/export/specific', [VisualizationController::class, 'exportSpecific'])->name('visualizations.export.specific');
});

// Siswa Profile Routes
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->group(function () {
    Route::put('/profile', [SiswaControllerBE::class, 'updateProfile'])->name('siswa.profile.update');
    Route::put('/password', [SiswaControllerBE::class, 'changePassword'])->name('siswa.password.update');
});
