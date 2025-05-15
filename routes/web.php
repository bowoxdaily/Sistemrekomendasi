<?php

use App\Http\Controllers\BE\AuthController as BEAuthController;
use App\Http\Controllers\BE\ForgotPasswordController;
use App\Http\Controllers\BE\JobRecommendataionController;
use App\Http\Controllers\BE\KuisionerController;
use App\Http\Controllers\BE\QuestionnaireControllerOpe;
use App\Http\Controllers\BE\QuestionnaireControllerSiswa;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Http\Controllers\FE\AuthController;
use App\Http\Controllers\FE\DashboardControllerFE;
use App\Http\Controllers\FE\JurusanControllerFE;
use App\Http\Controllers\FE\OperatorControllerFE;
use App\Http\Controllers\FE\SiswaController;
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


Route::middleware(['role:siswa,guru,operator', 'check.student.profile'])->group(function () {

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardControllerFE::class, 'index'])->name('dashboard');
    });
    Route::group(['prefix' => 'operator'], function () {
        Route::get('/dashboard', [DashboardControllerFE::class, 'index'])->name('dashboard');
        Route::get('/profile', [OperatorControllerFE::class, 'profile'])->name('operator.profile');
        Route::get('/tracerstudi', [OperatorControllerFE::class, 'tracer'])->name('tracer');
        Route::get('/data/siswa', [OperatorControllerFE::class, 'viewsiswa'])->name('view.siswa');
        Route::get('/data/jurusan', [JurusanControllerFE::class, 'index'])->name('view.jurusan');
        Route::get('/jobs', [JobRecommendataionController::class, 'index'])->name('operator.jobs.index');
        Route::post('/jobs', [JobRecommendataionController::class, 'store'])->name('operator.jobs.store');
        Route::put('/jobs/{job}', [JobRecommendataionController::class, 'update'])->name('operator.jobs.update');
        Route::delete('/jobs/{job}', [JobRecommendataionController::class, 'destroy'])->name('operator.jobs.destroy');
    });
    Route::group(['prefix' => 'operator/questionnaires', 'middleware' => ['auth', 'role:operator']], function () {
        Route::get('/dashboard', [DashboardControllerFE::class, 'index'])->name('dashboard');
        Route::get('/', [QuestionnaireControllerOpe::class, 'index'])->name('operator.questionnaires.index');
        Route::post('/', [QuestionnaireControllerOpe::class, 'store'])->name('operator.questionnaires.store');
        Route::get('/{questionnaire}/edit', [QuestionnaireControllerOpe::class, 'edit'])->name('operator.questionnaires.edit');
        Route::put('/{questionnaire}', [QuestionnaireControllerOpe::class, 'update'])->name('operator.questionnaires.update');
        Route::delete('/{questionnaire}', [QuestionnaireControllerOpe::class, 'destroy'])->name('operator.questionnaires.destroy');
        Route::post('/{questionnaire}/questions', [QuestionnaireControllerOpe::class, 'addQuestion'])->name('operator.questionnaires.questions.add');
        Route::delete('/questions/{question}', [QuestionnaireControllerOpe::class, 'removeQuestion'])->name('operator.questionnaires.questions.remove');
    });

    Route::group(['prefix' => 'siswa'], function () {
        Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
        Route::get('/dashboard', [DashboardControllerFE::class, 'index'])->name('dashboard');
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
