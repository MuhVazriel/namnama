<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #1e293b; }
        label { display: block; margin-top: 14px; }
        input, select { box-sizing: border-box; max-width: 400px; padding: 8px; width: 100%; }
        .error { color: #b91c1c; display: block; margin-top: 4px; }
        button { margin-top: 18px; padding: 8px 14px; }
    </style>
</head>
<body>
    <h1>Tambah Barang</h1>
    <a href="{{ route('barang.index') }}">Kembali ke daftar barang</a>

    <form action="{{ route('barang.store') }}" method="POST">
        @csrf

        <label for="kode_barang">Kode Barang</label>
        <input id="kode_barang" type="text" name="kode_barang" maxlength="20" value="{{ old('kode_barang') }}" required>
        @error('kode_barang') <small class="error">{{ $message }}</small> @enderror

        <label for="nama_barang">Nama Barang</label>
        <input id="nama_barang" type="text" name="nama_barang" minlength="3" maxlength="150" value="{{ old('nama_barang') }}" required>
        @error('nama_barang') <small class="error">{{ $message }}</small> @enderror

        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required>
            <option value="">Pilih kategori</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" @selected(old('kategori_id') == $kategori->id)>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id') <small class="error">{{ $message }}</small> @enderror

        <label for="stok">Stok</label>
        <input id="stok" type="number" name="stok" min="0" value="{{ old('stok') }}" required>
        @error('stok') <small class="error">{{ $message }}</small> @enderror

        <label for="kondisi">Kondisi</label>
        <select id="kondisi" name="kondisi" required>
            <option value="">Pilih kondisi</option>
            @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisi)
                <option value="{{ $kondisi }}" @selected(old('kondisi') === $kondisi)>{{ $kondisi }}</option>
            @endforeach
        </select>
        @error('kondisi') <small class="error">{{ $message }}</small> @enderror

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="">Pilih status</option>
            @foreach (['Tersedia', 'Dipinjam', 'Maintenance'] as $status)
                <option value="{{ $status }}" @selected(old('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        @error('status') <small class="error">{{ $message }}</small> @enderror

        <button type="submit">Simpan</button>
    </form>
</body>
</html>