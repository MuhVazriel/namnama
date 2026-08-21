<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;

Route::get('/', [BarangController::class, 'index']);
Route::resource('barang', BarangController::class);
Route::resource('peminjaman', PeminjamanController::class);