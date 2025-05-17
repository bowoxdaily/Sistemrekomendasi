<?php

use App\Http\Controllers\BE\AuthController;
use App\Http\Controllers\BE\DataSiswaController;
use App\Http\Controllers\BE\JobRecommendataionController;
use App\Http\Controllers\BE\JurusanController;
use App\Http\Controllers\BE\OperatorControllerBE;
use App\Http\Controllers\BE\QuestionnaireControllerOpe;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Student Profile Routes

});

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/student/profile/check', [SiswaControllerBE::class, 'checkProfile'])
        ->name('api.student.profile.check');
    Route::get('/student/profile', [SiswaControllerBE::class, 'getProfile'])
        ->name('api.student.profile.get');
    Route::post('/student/profile/update', [SiswaControllerBE::class, 'updateProfile'])
        ->name('api.student.profile.update');
    Route::post('/student/change-password', [AuthController::class, 'changePassword'])
        ->name('api.student.change-password');

    Route::group(['prefix' => 'profile-operator'], function () {
        Route::post('/', [OperatorControllerBE::class, 'updateProfile']);
        // Route Siswa
        Route::get('/get/siswa', [OperatorControllerBE::class, 'getSiswaData']);
        Route::get('/get/siswa/{id}', [OperatorControllerBE::class, 'getSiswaById']);
        Route::post('/create/siswa', [OperatorControllerBE::class, 'tambahAkunSiswa']);
        Route::get('/download-template/siswa', [OperatorControllerBE::class, 'downloadTemplateAkunSiswa']);
        Route::post('/import/siswa', [OperatorControllerBE::class, 'importAkunSiswa']);
        Route::delete('/delete/siswa/{id}', [OperatorControllerBE::class, 'destroy']);
        // RouteJurusan 
        Route::get('/get/jurusan', [JurusanController::class, 'index']);
        Route::post('/create/jurusan', [JurusanController::class, 'store']);
        Route::get('get/jurusan', [JurusanController::class, 'getAllJurusan']);
        Route::get('get/jurusan/{id}', [JurusanController::class, 'getJurusanById']);
        Route::put('/jurusan/{id}', [JurusanController::class, 'update']);
        Route::delete('/delete/jurusan/{id}', [JurusanController::class, 'destroy']);

        // Route Job
        Route::get('/get/job', [JobRecommendataionController::class, 'getdata']);
        Route::get('/get/job/{id}', [JobRecommendataionController::class, 'getById']);
        Route::put('/update/job/{id}', [JobRecommendataionController::class, 'update']);
        Route::post('/create/job', [JobRecommendataionController::class, 'store']);
        Route::delete('/delete/job/{id}', [JobRecommendataionController::class, 'destroy']);

        // Update Route Questionnaire prefix
        Route::group(['prefix' => 'kuisioner'], function () {
            Route::get('/get', [QuestionnaireControllerOpe::class, 'getQuestionnaires']);
            Route::post('/create', [QuestionnaireControllerOpe::class, 'store']);
            Route::get('/get/{id}', [QuestionnaireControllerOpe::class, 'getById']);
            Route::put('/update/{questionnaire}', [QuestionnaireControllerOpe::class, 'update']); // Ubah ini
            Route::delete('/delete/{id}', [QuestionnaireControllerOpe::class, 'destroy']);
            Route::post('/{questionnaire}/questions', [QuestionnaireControllerOpe::class, 'addQuestion']);
            Route::delete('/questions/{question}', [QuestionnaireControllerOpe::class, 'removeQuestion']);
            Route::get('/{questionnaire}/questions', [QuestionnaireControllerOpe::class, 'getQuestions']);
        });
    });
    Route::group(['prefix' => 'siswa'], function () {
        Route::post('/insert/data/graduation', [DataSiswaController::class, 'insertData']);
    });

    // Statistics Routes
    Route::get('/stats/students', [SiswaControllerBE::class, 'getCount'])
        ->name('api.stats.students');
});
