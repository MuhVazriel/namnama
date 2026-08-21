<h1>Data Barang</h1>

<a href="/barang/create">+ Tambah Barang</a>

<table border="1" cellpadding="10">
    <tr>
        <th>Nama Barang</th>
        <th>Stok</th>
    </tr>

    @foreach($barangs as $b)
    <tr>
        <td>{{ $b->nama_barang }}</td>
        <td>{{ $b->stok }}</td>
    </tr>
    @endforeach
</table>