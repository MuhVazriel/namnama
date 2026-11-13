<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Barang</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #1e293b; }
        label { display: block; margin-top: 14px; }
        input { box-sizing: border-box; max-width: 400px; padding: 8px; width: 100%; }
        .error { color: #b91c1c; display: block; margin-top: 4px; }
        button { margin-top: 18px; padding: 8px 14px; }
        table { border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h1>Hapus Barang</h1>
    <p><a href="{{ route('barang.index') }}">Kembali ke daftar barang</a></p>
    <p>Masukkan ID barang yang ingin dihapus. Barang yang masih dipinjam tidak dapat dihapus.</p>

    <form action="{{ route('barang.delete') }}" method="POST">
        @csrf
        <label for="barang_id">ID Barang</label>
        <input id="barang_id" type="number" name="barang_id" min="1" value="{{ old('barang_id') }}" required>
        @error('barang_id') <span class="error">{{ $message }}</span> @enderror
        <button type="submit" onclick="return confirm('Yakin ingin menghapus barang dengan ID yang dimasukkan?')">Hapus Barang</button>
    </form>

    <h2>ID Barang yang Tersedia</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barangs as $barang)
                <tr>
                    <td>{{ $barang->id }}</td>
                    <td>{{ $barang->kode_barang ?? '-' }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
