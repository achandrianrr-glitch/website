<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['X', 'XI', 'XII'];

        foreach ($data as $nama) {
            Kelas::updateOrCreate(['nama' => $nama]);
        }
    }
}
