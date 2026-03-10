<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Lab RPS 1',
            'Lab RPS 2',
            'Lab RPS 3',
            'Kantor RPS',
            'Gudang RPS',
        ];

        foreach ($data as $nama) {
            Lokasi::updateOrCreate(['nama' => $nama]);
        }
    }
}
