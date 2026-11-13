<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Barang</title>
</head>
<body>
    <h1>Daftar Inventaris</h1>
    @if (session('success'))
        <p role="status">{{ session('success') }}</p>
    @endif
    <p><a href="{{ route('barang.create') }}">+ Tambah Barang</a></p>

    <p><a href="{{ route('peminjaman.index') }}">+ Peminjaman Barang</a></p>
    <p><a href="{{ route('barang.delete.form') }}">- Hapus Barang</a></p>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>ID Barang</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Stok Dipinjam</th>
                <th>Kondisi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barangs as $barang)
                <tr>
                <td>{{ $barang->id }}</td>
                <td>{{ $barang->kode_barang ?? '-' }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td>{{ $barang->kategori?->nama_kategori ?? '-' }}</td>
                    <td>{{ $barang->stok }}</td>
                    <td>{{ $barang->stok_dipinjam ?? 0 }}</td>
                    <td>{{ $barang->kondisi ?? '-' }}</td>
                    <td>{{ $barang->status ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Data barang masih kosong.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
