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
    //End Dasboard

    // Master Data
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

    // End Master Data

    // Transaksi
        // Topup
        Route::view('/Transaksi-Topup', 'transaksi.topup.index');
        Route::prefix('transaksi-data')->group(function () {
            Route::get('/Transaksi-Topup', [App\Http\Controllers\TopupController::class, 'index'])->name('transaksi.topup.index');
            Route::get('/Topup_transaksi/data', [App\Http\Controllers\TopupController::class, 'getData'])->name('topup_transaksi.data');
        });

        // Mutasi
        Route::view('/Transaksi-Mutasi', 'transaksi.mutasi.index');
        Route::prefix('transaksi-data')->group(function () {
            Route::get('/Transaksi-Mutasi', [App\Http\Controllers\MutasiController::class, 'index'])->name('transaksi.mutasi.index');
            Route::get('/Mutasi_transaksi/data', [App\Http\Controllers\MutasiController::class, 'getData'])->name('mutasi_transaksi.data');
        });

        // Ticket
        Route::view('/Transaksi-Ticket', 'transaksi.ticket.index');
        Route::prefix('transaksi-data')->group(function () {
            Route::get('/Transaksi-Ticket', [App\Http\Controllers\TicketController::class, 'index'])->name('transaksi.ticket.index');
            Route::get('/Ticket_transaksi/data', [App\Http\Controllers\TicketController::class, 'getData'])->name('ticket_transaksi.data');
        });

        // Inbox
        Route::view('/Transaksi-Inbox', 'transaksi.inbox.index');
        Route::prefix('transaksi-data')->group(function () {
            Route::get('/Transaksi-Inbox', [App\Http\Controllers\InboxController::class, 'index'])->name('transaksi.inbox.index');
            Route::get('/Inbox_transaksi/data', [App\Http\Controllers\InboxController::class, 'getData'])->name('inbox_transaksi.data');
        });

        // Outbox
        Route::view('/Transaksi-Outbox', 'transaksi.outbox.index');
        Route::prefix('transaksi-data')->group(function () {
            Route::get('/Transaksi-Outbox', [App\Http\Controllers\OutboxController::class, 'index'])->name('transaksi.outbox.index');
            Route::get('/Outbox_transaksi/data', [App\Http\Controllers\OutboxController::class, 'getData'])->name('outbox_transaksi.data');
        });

    //End Transaksi

    // Tools
        // Tool Log Transaksi
        Route::view('/Tool-Log-Transaksi', 'tools.log_transaksi.index');
        Route::prefix('tools-data')->group(function () {
            Route::get('/Tool-Log-Transaksi', [App\Http\Controllers\LogTransaksiController::class, 'index'])->name('tools.log_transaksi.index');
            Route::get('/Log-Transaksi/data', [App\Http\Controllers\LogTransaksiController::class, 'getData'])->name('log_transaksi.data');
        });

        // Tool Log CRM
        Route::view('/Tool-Log-CRM', 'tools.log_crm.index');
        Route::prefix('tools-data')->group(function () {
            Route::get('/Tool-Log-CRM', [App\Http\Controllers\LogCRMController::class, 'index'])->name('tools.log_crm.index');
            Route::get('/Log-CRM/data', [App\Http\Controllers\LogCRMController::class, 'getData'])->name('log_crm.data');
        });
    // End Tools

});
