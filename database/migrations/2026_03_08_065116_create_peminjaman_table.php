<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam', 20)->unique();
            $table->string('nama_peminjam', 150);

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->restrictOnDelete();

            $table->foreignId('jurusan_id')
                ->constrained('jurusan')
                ->restrictOnDelete();

            $table->string('no_hp', 20)->nullable();
            $table->string('mata_pelajaran', 100)->nullable();
            $table->date('tanggal_pinjam');
            $table->time('waktu_pinjam');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->text('catatan')->nullable();

            $table->foreignId('pengguna_id')
                ->nullable()
                ->constrained('pengguna')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_pinjam');
            $table->index('kode_pinjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
