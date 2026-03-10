<?php

namespace Database\Seeders;

use App\Models\Merek;
use Illuminate\Database\Seeder;

class MerekSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Asus',
            'Acer',
            'Dell',
            'HP',
            'Lenovo',
            'Apple',
            'MSI',
            'Canon',
            'Epson',
            'Brother',
            'Samsung',
            'LG',
            'TP-Link',
            'D-Link',
            'Mikrotik',
            'Logitech',
            'Philips',
            'Sony',
            'Toshiba',
        ];

        foreach ($data as $nama) {
            Merek::updateOrCreate(['nama' => $nama]);
        }
    }
}
