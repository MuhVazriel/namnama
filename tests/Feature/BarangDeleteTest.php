<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BarangDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_dapat_dihapus_dengan_memasukkan_id(): void
    {
        $barang = $this->buatBarang();

        $this->get(route('barang.delete.form'))
            ->assertOk()
            ->assertSee((string) $barang->id)
            ->assertSee('Hapus Barang');

        $this->post(route('barang.delete'), ['barang_id' => $barang->id])
            ->assertRedirect(route('barang.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('barangs', ['id' => $barang->id]);
    }

    public function test_barang_yang_masih_dipinjam_tidak_dapat_dihapus(): void
    {
        $barang = $this->buatBarang();
        DB::table('peminjamen')->insert([
            'nama_peminjam' => 'Budi',
            'barang_id' => $barang->id,
            'barang' => $barang->nama_barang,
            'jumlah' => 1,
            'tanggal_pinjam' => '2026-10-09',
            'status' => 'dipinjam',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('barang.delete.form'))
            ->post(route('barang.delete'), ['barang_id' => $barang->id])
            ->assertRedirect(route('barang.delete.form'))
            ->assertSessionHasErrors('barang_id');

        $this->assertDatabaseHas('barangs', ['id' => $barang->id]);
    }

    private function buatBarang(): Barang
    {
        $kategori = Kategori::query()->firstOrCreate(['nama_kategori' => 'Elektronik']);

        return Barang::create([
            'kode_barang' => 'BRG-DEL-01',
            'nama_barang' => 'Barang Uji Hapus',
            'kategori_id' => $kategori->id,
            'stok' => 1,
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);
    }
}
