<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Laptop',
            'Komputer PC',
            'Proyektor',
            'Printer',
            'Scanner',
            'Kabel & Aksesori',
            'Perangkat Jaringan',
            'Kursi Lab',
            'Meja Lab',
            'Alat Tulis',
            'Perlengkapan Listrik',
        ];

        foreach ($data as $nama) {
            Kategori::updateOrCreate(
                ['nama' => $nama],
                ['deskripsi' => null]
            );
        }
    }
}
