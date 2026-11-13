<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $missingColumns = collect([
            'nama_peminjam' => fn (Blueprint $table) => $table->string('nama_peminjam')->nullable(),
            'barang' => fn (Blueprint $table) => $table->string('barang')->nullable(),
            'tanggal_pinjam' => fn (Blueprint $table) => $table->date('tanggal_pinjam')->nullable(),
            'tanggal_kembali' => fn (Blueprint $table) => $table->date('tanggal_kembali')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status')->default('dipinjam'),
        ])->reject(fn ($definition, $column) => Schema::hasColumn('peminjamen', $column));

        if ($missingColumns->isNotEmpty()) {
            Schema::table('peminjamen', function (Blueprint $table) use ($missingColumns) {
                foreach ($missingColumns as $definition) {
                    $definition($table);
                }
            });
        }
    }

    public function down(): void
    {
        // Keep repaired columns to avoid removing columns from databases that already had them.
    }
};
