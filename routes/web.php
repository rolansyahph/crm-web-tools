<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MitraController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test-db', [UserController::class, 'testConnection']);

Route::get('/users', [UserController::class, 'index']);

// Middleware Guest
Route::group(['middleware' => 'guest'], function () {
    // Menampilkan form login
        Route::view('/', 'login');

    // Proses login & Logout
        Route::post('/login', [UserController::class, 'login'])->name('login');
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    });

// Middleware Group
Route::group(['middleware' => 'check.session'], function () {

    //Dasboard
    Route::view('/Dasboard-CRM', 'dashboard.index');

    //M_Users
    Route::view('/Master-Users', 'master_data.m_users.index');
    Route::prefix('master-data')->group(function () {
        Route::get('m-users', [UserController::class, 'index'])->name('m_users.index');
        Route::get('m-users/data', [UserController::class, 'getData'])->name('m_users.data');
        Route::post('m-users/store', [UserController::class, 'store'])->name('m_users.store');
        Route::post('m-users/update', [UserController::class, 'update'])->name('m_users.update');
        Route::post('m-users/delete', [UserController::class, 'destroy'])->name('m_users.destroy');
    });

    // M_Mitra
    Route::view('/Master-Mitra', 'master_data.m_mitra.index');
    Route::prefix('master-data')->group(function () {
        Route::get('m-mitra', [MitraController::class, 'index'])->name('m_mitra.index');
        Route::get('m-mitra/data', [MitraController::class, 'getData'])->name('m_mitra.data');
        Route::post('m-mitra/store', [MitraController::class, 'store'])->name('m_mitra.store');
        Route::post('m-mitra/update', [MitraController::class, 'update'])->name('m_mitra.update');
        Route::post('m-mitra/delete', [MitraController::class, 'destroy'])->name('m_mitra.destroy');
    });

    // M_Unit
    Route::view('/Master-Unit', 'master_data.m_unit.index');
    Route::prefix('master-data')->group(function () {
        Route::get('m-unit', [App\Http\Controllers\UnitController::class, 'index'])->name('m_unit.index');
        Route::get('m-unit/data', [App\Http\Controllers\UnitController::class, 'getData'])->name('m_unit.data');
        Route::post('m-unit/store', [App\Http\Controllers\UnitController::class, 'store'])->name('m_unit.store');
        Route::post('m-unit/update', [App\Http\Controllers\UnitController::class, 'update'])->name('m_unit.update');
        Route::post('m-unit/delete', [App\Http\Controllers\UnitController::class, 'destroy'])->name('m_unit.destroy');
    });

    // M_Bank
    Route::view('/Master-Bank', 'master_data.m_bank.index');
    Route::prefix('master-data')->group(function () {
        Route::get('m-bank', [App\Http\Controllers\BankController::class, 'index'])->name('m_bank.index');
        Route::get('m-bank/data', [App\Http\Controllers\BankController::class, 'getData'])->name('m_bank.data');
        Route::post('m-bank/store', [App\Http\Controllers\BankController::class, 'store'])->name('m_bank.store');
        Route::post('m-bank/update', [App\Http\Controllers\BankController::class, 'update'])->name('m_bank.update');
        Route::post('m-bank/delete', [App\Http\Controllers\BankController::class, 'destroy'])->name('m_bank.destroy');
    });

    // Transaksi data
    Route::view('/Transaksi-Cek-Log', 'transaksi.log.index');
    Route::prefix('transaksi-data')->group(function () {
        Route::get('/Transaksi-Cek-Log', [App\Http\Controllers\LogController::class, 'index'])->name('transaksi.log.index');
        Route::get('/Cek-Log/data', [App\Http\Controllers\LogController::class, 'getData'])->name('log.data');
    });

});
