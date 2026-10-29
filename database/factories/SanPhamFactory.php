<?php

namespace Database\Factories;

use App\Models\Hang;
use App\Models\LoaiSanPham;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory tạo dữ liệu mẫu cho bảng SanPham (dùng trong Seeder và Test).
 *
 * @extends Factory<SanPham>
 */
class SanPhamFactory extends Factory
{
    protected $model = SanPham::class;

    public function definition(): array
    {
        $dong = ['Astrox', 'Nanoflare', 'Arcsaber', 'Thruster', 'Auraspeed', 'Axforce', 'Tectonic', 'Fortius'];

        return [
            'TenSanPham' => 'Vợt '.fake()->randomElement($dong).' '.fake()->unique()->numberBetween(10, 9999),
            'MoTa' => fake()->sentence(12),
            'GiaGoc' => fake()->numberBetween(5, 60) * 100000,
            'HinhAnhDaiDien' => 'default-product.jpg',
            'MaLoai' => LoaiSanPham::query()->inRandomOrder()->value('MaLoai'),
            'MaNhaCungCap' => NhaCungCap::query()->inRandomOrder()->value('MaNhaCungCap'),
            'MaHang' => Hang::query()->inRandomOrder()->value('MaHang'),
            'MaKhuyenMai' => null,
            'CoSize' => true,
            'CoMau' => true,
            'NgayTao' => fake()->dateTimeBetween('-1 year'),
        ];
    }
}
