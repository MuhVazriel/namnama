<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;

Route::redirect('/', '/barang');
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::get('/table', function () {
    $daftarBarang = [
        ['nama' => 'Laptop Lab RPL', 'kategori' => 'Elektronik', 'status' => 'Tersedia'],
        ['nama' => 'Proyektor Epson EB-X400', 'kategori' => 'Elektronik', 'status' => 'Dipinjam'],
        ['nama' => 'Router TP-Link', 'kategori' => 'Jaringan', 'status' => 'Tersedia'],
    ];
    return view('table', compact('daftarBarang'));
});