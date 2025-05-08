<?php

use App\Http\Controllers\BE\AuthController;
use App\Http\Controllers\BE\DataSiswaController;
use App\Http\Controllers\BE\OperatorControllerBE;
use App\Http\Controllers\BE\SiswaControllerBE;
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
        Route::get('/get/siswa', [OperatorControllerBE::class, 'getSiswaData']);
        Route::post('/create/siswa', [OperatorControllerBE::class, 'tambahAkunSiswa']);
        Route::get('/download-template/siswa', [OperatorControllerBE::class, 'downloadTemplateAkunSiswa']);
        Route::post('/import/siswa', [OperatorControllerBE::class, 'importAkunSiswa']);
    });
    Route::group(['prefix' => 'siswa'], function () {
        Route::post('/insert/data/graduation', [DataSiswaController::class, 'insertData']);
    });

    // Statistics Routes
    Route::get('/stats/students', [SiswaControllerBE::class, 'getCount'])
        ->name('api.stats.students');
});
