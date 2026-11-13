<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = Barang::query()
            ->with('kategori')
            ->withSum([
                'peminjamans as stok_dipinjam' => fn ($query) => $query->where('status', 'dipinjam'),
            ], 'jumlah')
            ->get();

        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('barang.create', compact('kategoris'));
    }

    public function deleteForm()
    {
        $barangs = Barang::query()->orderBy('id')->get(['id', 'nama_barang', 'kode_barang']);

        return view('barang.delete', compact('barangs'));
    }

    public function destroyById(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $barang = Barang::query()
                ->lockForUpdate()
                ->findOrFail($validated['barang_id']);

            if ($barang->peminjamans()->where('status', 'dipinjam')->exists()) {
                throw ValidationException::withMessages([
                    'barang_id' => 'Barang ini masih dipinjam dan belum bisa dihapus.',
                ]);
            }

            $barang->delete();
        });

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:20', 'unique:barangs,kode_barang'],
            'nama_barang' => ['required', 'string', 'min:3', 'max:150'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:Baik,Rusak Ringan,Rusak Berat'],
            'status' => ['required', 'in:Tersedia,Dipinjam,Maintenance'],
        ]);

        Barang::create($validated);

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil disimpan!');
    }
}
