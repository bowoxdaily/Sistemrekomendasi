<?php

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BE\AuthController;
use App\Http\Controllers\BE\BlogControllerBE;
use App\Http\Controllers\BE\JurusanController;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Http\Controllers\BE\SettingsController;
use App\Http\Controllers\BE\DataSiswaController;
use App\Http\Controllers\BE\OperatorControllerBE;
use App\Http\Controllers\BE\SuperAdminController;
use App\Http\Controllers\BE\QuestionnaireControllerOpe;
use App\Http\Controllers\BE\JobRecommendataionController;
use App\Http\Controllers\Api\StatsController;

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

    Route::group(['prefix' => 'superadmin'], function () {
        Route::get('/operator', [SuperAdminController::class, 'getOperators']);
        Route::get('/operator/{id}', [SuperAdminController::class, 'getOperator']);
        Route::post('/operator', [SuperAdminController::class, 'storeOperator']);
        Route::put('/operator/{id}', [SuperAdminController::class, 'updateOperator']);
        Route::delete('/operator/{id}', [SuperAdminController::class, 'deleteOperator']);
    });

     Route::group(['prefix' => 'blog'], function () {
        Route::get('/', [BlogControllerBE::class, 'get']);
        Route::get('/{id}', [BlogControllerBE::class, 'getById']);
        Route::post('/', [BlogControllerBE::class, 'store']);
        Route::put('/{id}', [BlogControllerBE::class, 'update']);
        Route::delete('/{id}', [BlogControllerBE::class, 'destroy']);   
    });


    Route::group(['prefix' => 'profile-operator'], function () {
        Route::post('/', [OperatorControllerBE::class, 'updateProfile']);
        // Route Siswa
        Route::get('/get/siswa', [OperatorControllerBE::class, 'getSiswaData']);
        Route::get('/get/siswa/{id}', [OperatorControllerBE::class, 'getSiswaById']);
        Route::post('/create/siswa', [OperatorControllerBE::class, 'tambahAkunSiswa']);
        Route::get('/download-template/siswa', [OperatorControllerBE::class, 'downloadTemplateAkunSiswa']);
        Route::post('/import/siswa', [OperatorControllerBE::class, 'importAkunSiswa']);
        Route::delete('/delete/siswa/{id}', [OperatorControllerBE::class, 'destroy']);
        Route::post('/update/siswa/{id}', [OperatorControllerBE::class, 'updateSiswa']);
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
            Route::get('/get', [QuestionnaireControllerOpe::class, 'getQue/stionnaires']);
            Route::post('/create', [QuestionnaireControllerOpe::class, 'store']);
            Route::get('/get/{id}', [QuestionnaireControllerOpe::class, 'getById']);
            Route::put('/update/{questionnaire}', [QuestionnaireControllerOpe::class, 'update']); // Ubah ini
            Route::delete('/delete/{id}', [QuestionnaireControllerOpe::class, 'destroy']);
            Route::post('/{questionnaire}/questions', [QuestionnaireControllerOpe::class, 'addQuestion']);
            Route::delete('/questions/{question}', [QuestionnaireControllerOpe::class, 'removeQuestion']);
            Route::get('/{questionnaire}/questions', [QuestionnaireControllerOpe::class, 'getQuestions']);
        });
    });
    Route::group(['prefix' => 'settings'], function () {
        Route::put('/general', [SettingsController::class, 'updateGeneral']);
        // Logo settings
        Route::post('/logo', [SettingsController::class, 'updateLogo']);
        // School information
        Route::put('/school', [SettingsController::class, 'updateSchool']);
        // Backup & Restore operations
        Route::post('/backup/generate', [SettingsController::class, 'generateBackup']);
        Route::post('/backup/restore', [SettingsController::class, 'restoreBackup']);
        Route::post('/backup/delete', [SettingsController::class, 'deleteBackup']);
        Route::post('/backup/schedule', [SettingsController::class, 'updateBackupSchedule']);
    });

    Route::group(['prefix' => 'siswa'], function () {
        Route::post('/insert/data/graduation', [DataSiswaController::class, 'insertData']);
        Route::post('/update/profile', [SiswaControllerBE::class, 'updatesiswaProfile']);
    });
    // Statistics Routes
    Route::get('/stats/students', [SiswaControllerBE::class, 'getCount'])
        ->name('api.stats.students');
    Route::get('/stats/tracer', [StatsController::class, 'getTracerStats']);
    Route::get('/stats/dashboard', [StatsController::class, 'getDashboardStats']);
    
    // Blog categories
    Route::get('/blog-categories', [BlogControllerBE::class, 'getCategories']);
});
