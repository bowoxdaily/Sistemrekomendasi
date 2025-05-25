<?php

use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\BE\AuthController as BEAuthController;
use App\Http\Controllers\BE\ForgotPasswordController;
use App\Http\Controllers\BE\JobRecommendataionController;
use App\Http\Controllers\BE\KuisionerController;
use App\Http\Controllers\BE\QuestionnaireControllerOpe;
use App\Http\Controllers\BE\QuestionnaireControllerSiswa;
use App\Http\Controllers\BE\SettingsController;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Http\Controllers\FE\AuthController;
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


Route::middleware(['role:siswa,guru,operator,superadmin', 'check.student.profile'])->group(function () {

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardControllerFE::class, 'index'])->name('dashboard');
    });
    Route::group(['prefix' => 'superadmin'], function () {
        Route::get('/operator', [SuperadminControllerFE::class, 'operator'])->name('superadmin.operator');
        // Route::get('/operator', [DashboardControllerFE::class, 'index'])->name('superadmin.profile');
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
    Route::get('/dashboard/teacher', [DashboardControllerFE::class, 'teacherDashboard'])->name('teacher.dashboard');
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
