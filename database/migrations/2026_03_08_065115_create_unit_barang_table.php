<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->string('nomor_unit', 50);
            $table->string('serial_number', 100)->nullable();
            $table->unsignedTinyInteger('kondisi')->default(100);
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'keluar'])->default('tersedia');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['barang_id', 'nomor_unit']);
            $table->index('status');
            $table->index('barang_id');
            $table->index('kondisi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_barang');
    }
};
