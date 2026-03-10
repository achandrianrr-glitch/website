<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KelasSeeder::class,
            JurusanSeeder::class,
            KategoriSeeder::class,
            MerekSeeder::class,
            LokasiSeeder::class,
            PenggunaSeeder::class,
        ]);
    }
}
