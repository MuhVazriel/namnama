<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Barang</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #1e293b; }
        label { display: block; margin-top: 14px; }
        input, select { box-sizing: border-box; max-width: 400px; padding: 8px; width: 100%; }
        .error { color: #b91c1c; display: block; margin-top: 4px; }
        button { margin-top: 18px; padding: 8px 14px; }
    </style>
</head>
<body>
    <h1>Peminjaman Barang</h1>
    <a href="{{ route('barang.index') }}">Kembali ke daftar barang</a>
    <form action="{{ route('barang.store') }}" method="POST">
        @csrf
        <label for="kode_barang">Kode Barang:</label>
        <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}">
        @error('kode_barang')
            <span class="error">{{ $message }}</span>
        @enderror

        <label for="nama_barang">Nama Barang:</label>
        <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}">
        @error('nama_barang')
            <span class="error">{{ $message }}</span>
        @enderror

        <label for="kategori_id">Kategori:</label>
        <select name="kategori_id" id="kategori_id">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('kategori_id')
            <span class="error">{{ $message }}</span>
        @enderror

        <label for="stok">Stok:</label>
        <input type="number" name="stok" id="stok" value="{{ old('stok') }}">
        @error('stok')
            <span class="error">{{ $message }}</span>
        @enderror

        <label for="kondisi">Kondisi:</label>
        <select name="kondisi" id="kondisi">
            <option value="">-- Pilih Kondisi --</option>
            <option value="Baik" {{ old('kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
            <option value="Rusak Ringan" {{ old('kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
            <option value="Rusak Berat" {{ old('kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
        </select>
        @error('kondisi')
            <span class="error">{{ $message }}</span>
        @enderror

        <label for
