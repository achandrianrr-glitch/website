<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['masuk', 'keluar']);

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->foreignId('unit_barang_id')
                ->nullable()
                ->constrained('unit_barang')
                ->nullOnDelete();

            $table->unsignedSmallInteger('jumlah')->default(1);
            $table->enum('alasan_keluar', ['pindah_lokasi', 'dibuang', 'hibah', 'lainnya'])->nullable();

            $table->foreignId('lokasi_tujuan_id')
                ->nullable()
                ->constrained('lokasi')
                ->nullOnDelete();

            $table->string('lokasi_tujuan_manual', 100)->nullable();
            $table->string('sumber_tujuan', 200)->nullable();
            $table->date('tanggal_transaksi');
            $table->unsignedTinyInteger('kondisi_saat_itu')->nullable();
            $table->text('catatan')->nullable();

            $table->foreignId('pengguna_id')
                ->constrained('pengguna')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('jenis');
            $table->index('tanggal_transaksi');
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
