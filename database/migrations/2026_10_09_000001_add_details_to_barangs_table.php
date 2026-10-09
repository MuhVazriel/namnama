<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('kode_barang', 20)->nullable()->unique()->after('id');
            $table->foreignId('kategori_id')->nullable()->after('nama_barang')->constrained('kategoris');
            $table->string('kondisi')->nullable()->after('stok');
            $table->string('status')->nullable()->after('kondisi');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropUnique(['kode_barang']);
            $table->dropColumn(['kode_barang', 'kategori_id', 'kondisi', 'status']);
        });
    }
};
