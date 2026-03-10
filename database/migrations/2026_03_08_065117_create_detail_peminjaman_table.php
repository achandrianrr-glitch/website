<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjaman_id')
                ->constrained('peminjaman')
                ->cascadeOnDelete();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->foreignId('unit_barang_id')
                ->nullable()
                ->constrained('unit_barang')
                ->nullOnDelete();

            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->enum('status_item', ['dipinjam', 'dikembalikan'])->default('dipinjam');
            $table->dateTime('waktu_kembali')->nullable();
            $table->unsignedTinyInteger('kondisi_kembali')->nullable();
            $table->text('catatan_kembali')->nullable();
            $table->timestamps();

            $table->index('peminjaman_id');
            $table->index('barang_id');
            $table->index('status_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
