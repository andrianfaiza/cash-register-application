<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasController;

Route::get('/', function () {
    return view('login');
});
Route::get('login', function () {
    return view('login');
});
Route::post('login', [KasController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [KasController::class, 'dashboard'])->name('dashboard');

    Route::get('transaksi', [KasController::class, 'transactions'])->name('transaksi');
    Route::post('transaksi', [KasController::class, 'storeTransaction'])->name('transaksi.store');
    Route::put('transaksi/{transaction}', [KasController::class, 'updateTransaction'])->name('transaksi.update');
    Route::delete('transaksi', [KasController::class, 'destroyTransactions'])->name('transaksi.destroy');
    Route::get('transaksi/create', [KasController::class, 'createTransaction'])->name('transaksi.create');

    Route::get('proyek', [KasController::class, 'projects'])->name('proyek');
    Route::post('proyek', [KasController::class, 'storeProject'])->name('proyek.store');
    Route::put('proyek/{project}', [KasController::class, 'updateProject'])->name('proyek.update');
    Route::delete('proyek', [KasController::class, 'destroyProjects'])->name('proyek.destroy');

    Route::get('laporan', [KasController::class, 'reports'])->name('laporan');
    Route::get('laporan/export-excel', [KasController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('laporan/export-pdf', [KasController::class, 'exportPdf'])->name('laporan.export-pdf');

    Route::get('search', [KasController::class, 'search'])->name('search');

    Route::get('settings', [KasController::class, 'settings'])->name('settings');
    Route::put('settings', [KasController::class, 'updateSettings'])->name('settings.update');

    Route::get('profile', [KasController::class, 'profile'])->name('profile');
    Route::put('profile', [KasController::class, 'updateProfile'])->name('profile.update');
    Route::post('logout', [KasController::class, 'logout'])->name('logout');
});