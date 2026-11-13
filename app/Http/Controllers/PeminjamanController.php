<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index()
    {
        $barangs = Barang::query()
            ->where('stok', '>', 0)
            ->where(function ($query) {
                $query->where('status', 'Tersedia')
                    ->orWhereNull('status');
            })
            ->orderBy('nama_barang')
            ->get();
        $peminjamans = Peminjaman::query()
            ->with('barang')
            ->latest()
            ->get();

        return view('peminjaman.index', compact('barangs', 'peminjamans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peminjam' => ['required', 'string', 'max:255'],
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'tanggal_pinjam' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail($validated['barang_id']);

            if ($barang->stok < 1 || ($barang->status !== null && $barang->status !== 'Tersedia')) {
                throw ValidationException::withMessages([
                    'barang_id' => 'Barang ini sedang tidak tersedia untuk dipinjam.',
                ]);
            }

            if ($validated['jumlah'] > $barang->stok) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah yang dipinjam melebihi stok barang yang tersedia.',
                ]);
            }

            $stokTersisa = $barang->stok - $validated['jumlah'];
            $barang->update([
                'stok' => $stokTersisa,
                'status' => $stokTersisa === 0 ? 'Dipinjam' : 'Tersedia',
            ]);

            Peminjaman::create([
                'nama_peminjam' => $validated['nama_peminjam'],
                'barang_id' => $barang->id,
                'barang' => $barang->nama_barang,
                'jumlah' => $validated['jumlah'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'status' => 'dipinjam',
            ]);
        });

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Peminjaman barang berhasil disimpan.');
    }

    public function return(Peminjaman $peminjaman)
    {
        DB::transaction(function () use ($peminjaman) {
            $peminjaman = Peminjaman::query()
                ->lockForUpdate()
                ->findOrFail($peminjaman->id);

            if ($peminjaman->status !== 'dipinjam' || $peminjaman->barang_id === null) {
                throw ValidationException::withMessages([
                    'peminjaman' => 'Peminjaman ini tidak dapat dikembalikan.',
                ]);
            }

            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail($peminjaman->barang_id);

            $barang->increment('stok', $peminjaman->jumlah);
            if ($barang->status === 'Dipinjam') {
                $barang->update(['status' => 'Tersedia']);
            }

            $peminjaman->update([
                'tanggal_kembali' => now()->toDateString(),
                'status' => 'dikembalikan',
            ]);
        });

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Barang berhasil dikembalikan.');
    }
}
