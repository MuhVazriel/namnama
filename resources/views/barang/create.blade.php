<h1>Tambah Barang</h1>

<form action="/barang" method="POST">
    @csrf

    <label>Nama Barang:</label><br>
    <input type="text" name="nama_barang"><br><br>

    <label>Stok:</label><br>
    <input type="number" name="stok"><br><br>

    <button type="submit">Simpan</button>
</form>