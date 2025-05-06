<?php

use App\Http\Controllers\BE\AuthController as BEAuthController;
use App\Http\Controllers\BE\ForgotPasswordController;
use App\Http\Controllers\BE\SiswaControllerBE;
use App\Http\Controllers\FE\AuthController;
use App\Http\Controllers\FE\DashboardControllerFE;
use App\Http\Controllers\FE\OperatorControllerFE;
use App\Http\Controllers\FE\SiswaController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Constraint\Operator;

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
    });

    Route::group(['prefix' => 'siswa'], function () {
        Route::get('/profile', [SiswaController::class, 'profile'])->name('siswa.profile');
        Route::get('/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');
        Route::get('/edit', [SiswaControllerBE::class, 'edit'])->name('student.profile.edit');
    });
});

Route::middleware(['role:operator'])->group(function () {
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/tracerstudi', [OperatorControllerFE::class, 'tracer'])->name('tracer');
        Route::get('/Data/siswa', [OperatorControllerFE::class, 'viewsiswa'])->name('view.siswa');
    });



    // Tambahkan rute user lainnya

});
