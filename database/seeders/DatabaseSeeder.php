<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BadmintonSeeder::class,
            PhuKienSeeder::class,
            BanTinSeeder::class,
            DonHangMauSeeder::class,
            BoSungBienTheSeeder::class,
        ]);
    }
}
