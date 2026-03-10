<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->restrictOnDelete();

            $table->foreignId('merek_id')
                ->nullable()
                ->constrained('merek')
                ->nullOnDelete();

            $table->string('merek_manual', 100)->nullable();

            $table->foreignId('lokasi_id')
                ->nullable()
                ->constrained('lokasi')
                ->nullOnDelete();

            $table->string('lokasi_manual', 100)->nullable();

            $table->enum('tipe', ['aset', 'stok'])->default('aset');
            $table->text('spesifikasi')->nullable();
            $table->year('tahun_pengadaan')->nullable();

            $table->unsignedSmallInteger('qty_total')->nullable()->default(0);
            $table->unsignedSmallInteger('qty_tersedia')->nullable()->default(0);
            $table->unsignedSmallInteger('qty_dipinjam')->nullable()->default(0);
            $table->unsignedSmallInteger('qty_rusak')->nullable()->default(0);
            $table->unsignedSmallInteger('qty_keluar')->nullable()->default(0);
            $table->unsignedTinyInteger('kondisi_stok')->nullable()->default(100);

            $table->boolean('aktif')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('kategori_id');
            $table->index('tipe');
            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
