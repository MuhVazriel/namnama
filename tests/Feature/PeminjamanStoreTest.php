<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_peminjaman_memilih_barang_yang_tersedia_menyimpan_data_dan_mengurangi_stok(): void
    {
        $barang = $this->buatBarang();

        $this->get(route('peminjaman.index'))
            ->assertOk()
            ->assertSee('Laptop Lab');

        $response = $this->post(route('peminjaman.store'), [
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'jumlah' => 2,
            'tanggal_pinjam' => '2026-10-09',
        ]);

        $response
            ->assertRedirect(route('peminjaman.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamen', [
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'barang' => 'Laptop Lab',
            'jumlah' => 2,
            'status' => 'dipinjam',
        ]);
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok' => 0,
            'status' => 'Dipinjam',
        ]);

        $this->get(route('peminjaman.index'))
            ->assertOk()
            ->assertSee('<td>2</td>', false);
        $this->get(route('barang.index'))
            ->assertOk()
            ->assertSee('<td>2</td>', false);
    }

    public function test_barang_tidak_bisa_dipinjam_jika_stok_habis(): void
    {
        $barang = $this->buatBarang(stok: 0, status: 'Dipinjam');

        $this->post(route('peminjaman.store'), [
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-10-09',
        ])->assertSessionHasErrors('barang_id');

        $this->assertDatabaseCount('peminjamen', 0);
    }

    public function test_jumlah_peminjaman_tidak_boleh_melebihi_stok_tersedia(): void
    {
        $barang = $this->buatBarang(stok: 2);

        $this->post(route('peminjaman.store'), [
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'jumlah' => 3,
            'tanggal_pinjam' => '2026-10-09',
        ])->assertSessionHasErrors('jumlah');

        $this->assertDatabaseCount('peminjamen', 0);
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok' => 2,
        ]);
    }

    public function test_pengembalian_mengubah_status_dan_memulihkan_stok(): void
    {
        $barang = $this->buatBarang(stok: 0, status: 'Dipinjam');
        $peminjamanId = \Illuminate\Support\Facades\DB::table('peminjamen')->insertGetId([
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'barang' => $barang->nama_barang,
            'jumlah' => 2,
            'tanggal_pinjam' => '2026-10-09',
            'status' => 'dipinjam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('peminjaman.return', $peminjamanId))
            ->assertRedirect(route('peminjaman.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('peminjamen', [
            'id' => $peminjamanId,
            'status' => 'dikembalikan',
        ]);
        $this->assertSame(
            now()->toDateString(),
            Peminjaman::query()->findOrFail($peminjamanId)->tanggal_kembali->toDateString(),
        );
        $this->assertDatabaseHas('barangs', [
            'id' => $barang->id,
            'stok' => 2,
            'status' => 'Tersedia',
        ]);
    }

    private function buatBarang(int $stok = 2, string $status = 'Tersedia'): Barang
    {
        $kategori = Kategori::query()->firstOrCreate(['nama_kategori' => 'Elektronik']);

        return Barang::create([
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop Lab',
            'kategori_id' => $kategori->id,
            'stok' => $stok,
            'kondisi' => 'Baik',
            'status' => $status,
        ]);
    }
}
