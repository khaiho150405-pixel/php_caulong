<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Chạy: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->call([
            BadmintonSeeder::class,   // Danh mục, hãng, sản phẩm, biến thể, tài khoản mẫu...
            PhuKienSeeder::class,
            BanTinSeeder::class,      // Bảng tin: kết quả trận đấu và bài viết có nguồn,     // Phụ kiện cầu lông (cầu, quấn cán...) với ảnh thật
            DonHangMauSeeder::class,  // Đơn hàng mẫu cho Dashboard thống kê
        ]);
    }
}
