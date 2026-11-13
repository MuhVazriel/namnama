<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PeminjamanController;

Route::redirect('/', '/barang');
Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::get('/barang/hapus', [BarangController::class, 'deleteForm'])->name('barang.delete.form');
Route::post('/barang/hapus', [BarangController::class, 'destroyById'])->name('barang.delete');
Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
Route::post('/peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'return'])->name('peminjaman.return');
Route::get('/table', function () {
    $daftarBarang = [
        ['nama' => 'Laptop Lab RPL', 'kategori' => 'Elektronik', 'status' => 'Tersedia'],
        ['nama' => 'Proyektor Epson EB-X400', 'kategori' => 'Elektronik', 'status' => 'Dipinjam'],
        ['nama' => 'Router TP-Link', 'kategori' => 'Jaringan', 'status' => 'Tersedia'],
    ];
    return view('table', compact('daftarBarang'));
});