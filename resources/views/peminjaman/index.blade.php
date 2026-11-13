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
        .success { color: #15803d; }
        button { padding: 8px 14px; }
        table { border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #e2e8f0; }
    </style>
</head>
<body>
    <h1>Peminjaman Barang</h1>
    <p><a href="{{ route('barang.index') }}">Kembali ke daftar barang</a></p>

    @if (session('success'))
        <p class="success" role="status">{{ session('success') }}</p>
    @endif
    @error('peminjaman')
        <p class="error" role="alert">{{ $message }}</p>
    @enderror

    <h2>Form Peminjaman</h2>
    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf

        <label for="nama_peminjam">Nama Peminjam</label>
        <input id="nama_peminjam" type="text" name="nama_peminjam" value="{{ old('nama_peminjam') }}" maxlength="255" required>
        @error('nama_peminjam') <span class="error">{{ $message }}</span> @enderror

        <label for="barang_id">Barang</label>
        <select id="barang_id" name="barang_id" required>
            <option value="">Pilih barang</option>
            @foreach ($barangs as $barang)
                <option value="{{ $barang->id }}" data-stok="{{ $barang->stok }}" @selected(old('barang_id') == $barang->id)>
                    {{ $barang->kode_barang ? $barang->kode_barang . ' - ' : '' }}{{ $barang->nama_barang }} (stok: {{ $barang->stok }})
                </option>
            @endforeach
        </select>
        @error('barang_id') <span class="error">{{ $message }}</span> @enderror

        <label for="jumlah">Jumlah yang Dipinjam</label>
        <input id="jumlah" type="number" name="jumlah" min="1" value="{{ old('jumlah', 1) }}" required>
        <small id="batas-stok">Pilih barang untuk melihat stok yang tersedia.</small>
        @error('jumlah') <span class="error">{{ $message }}</span> @enderror
        @if ($barangs->isEmpty())
            <p>Tidak ada barang yang tersedia untuk dipinjam.</p>
        @endif

        <label for="tanggal_pinjam">Tanggal Pinjam</label>
        <input id="tanggal_pinjam" type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', now()->toDateString()) }}" required>
        @error('tanggal_pinjam') <span class="error">{{ $message }}</span> @enderror

        <button type="submit" @disabled($barangs->isEmpty())>Simpan Peminjaman</button>
    </form>

    <h2>Daftar Peminjaman</h2>
    <table>
        <thead>
            <tr>
                <th>Nama Peminjam</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjamans as $peminjaman)
                <tr>
                    <td>{{ $peminjaman->nama_peminjam }}</td>
                    <td>{{ $peminjaman->barang?->nama_barang ?? $peminjaman->barang }}</td>
                    <td>{{ $peminjaman->jumlah }}</td>
                    <td>{{ $peminjaman->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ $peminjaman->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ ucfirst($peminjaman->status) }}</td>
                    <td>
                        @if ($peminjaman->status === 'dipinjam' && $peminjaman->barang_id !== null)
                            <form action="{{ route('peminjaman.return', $peminjaman) }}" method="POST">
                                @csrf
                                <button type="submit">Kembalikan</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <script>
        const pilihanBarang = document.getElementById('barang_id');
        const inputJumlah = document.getElementById('jumlah');
        const batasStok = document.getElementById('batas-stok');

        function perbaruiBatasStok() {
            const stok = Number(pilihanBarang.selectedOptions[0]?.dataset.stok || 0);
            inputJumlah.max = stok || 1;
            inputJumlah.disabled = stok === 0;
            batasStok.textContent = stok > 0
                ? `Stok tersedia: ${stok} barang.`
                : 'Pilih barang untuk melihat stok yang tersedia.';

            if (Number(inputJumlah.value) > stok) {
                inputJumlah.value = stok || 1;
            }
        }

        pilihanBarang.addEventListener('change', perbaruiBatasStok);
        perbaruiBatasStok();
    </script>
</body>
</html>
