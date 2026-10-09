<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BarangStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_dapat_disimpan_dan_mengalihkan_ke_daftar(): void
    {
        $kategori = Kategori::query()->firstOrCreate(['nama_kategori' => 'Elektronik']);

        $response = $this->post(route('barang.store'), [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop Lab',
            'kategori_id' => $kategori->id,
            'stok' => 3,
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response
            ->assertRedirect(route('barang.index'))
            ->assertSessionHas('success', 'Data barang berhasil disimpan!');

        $this->assertDatabaseHas('barangs', [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop Lab',
            'kategori_id' => $kategori->id,
            'stok' => 3,
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);
    }

    public function test_validasi_gagal_mengembalikan_error_dan_input_lama(): void
    {
        $response = $this->from(route('barang.create'))->post(route('barang.store'), [
            'kode_barang' => 'KODE-TERLALU-PANJANG-UNTUK-VALIDASI',
            'nama_barang' => 'AB',
            'kategori_id' => 99999,
            'stok' => -1,
            'kondisi' => 'Tidak diketahui',
            'status' => 'Tidak diketahui',
        ]);

        $response
            ->assertRedirect(route('barang.create'))
            ->assertSessionHasErrors([
                'kode_barang',
                'nama_barang',
                'kategori_id',
                'stok',
                'kondisi',
                'status',
            ])
            ->assertSessionHas('_old_input.kode_barang', 'KODE-TERLALU-PANJANG-UNTUK-VALIDASI');

        $this->assertSame(0, Barang::query()->count());
    }

    public function test_kode_barang_harus_unik(): void
    {
        $kategori = Kategori::query()->firstOrCreate(['nama_kategori' => 'Elektronik']);
        Barang::create([
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop Lab',
            'kategori_id' => $kategori->id,
            'stok' => 3,
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response = $this->post(route('barang.store'), [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop Cadangan',
            'kategori_id' => $kategori->id,
            'stok' => 1,
            'kondisi' => 'Baik',
            'status' => 'Tersedia',
        ]);

        $response->assertSessionHasErrors('kode_barang');
        $this->assertSame(1, Barang::query()->count());
    }

    public function test_form_memuat_kategori_dan_daftar_tetap_bisa_menampilkan_barang_lama(): void
    {
        $this->get(route('barang.create'))
            ->assertOk()
            ->assertSee('Elektronik');

        DB::table('barangs')->insert([
            'nama_barang' => 'Barang lama',
            'stok' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('barang.index'))
            ->assertOk()
            ->assertSee('Barang lama');
    }
}
