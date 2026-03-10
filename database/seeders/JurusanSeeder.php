<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'RPL 1',
            'RPL 2',
            'TKJ 1',
            'TKJ 2',
            'TEI',
            'ANIMASI 1',
            'ANIMASI 2',
            'TSM 1',
            'TSM 2',
            'TSM 3',
        ];

        foreach ($data as $nama) {
            Jurusan::updateOrCreate(['nama' => $nama]);
        }
    }
}
