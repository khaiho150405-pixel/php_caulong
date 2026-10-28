<?php

namespace Database\Factories;

use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Factory tạo tài khoản mẫu. Mặc định là Khách hàng và tự tạo bản ghi KhachHang đi kèm.
 *
 * @extends Factory<TaiKhoan>
 */
class TaiKhoanFactory extends Factory
{
    protected $model = TaiKhoan::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'TenDangNhap' => fake()->unique()->userName(),
            'MatKhau' => static::$password ??= Hash::make('123456'),
            'Email' => fake()->unique()->safeEmail(),
            'MaQuyen' => TaiKhoan::QUYEN_KHACHHANG,
            'NgayTao' => now(),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['MaQuyen' => TaiKhoan::QUYEN_ADMIN]);
    }

    public function nhanVien(): static
    {
        return $this->state(fn () => ['MaQuyen' => TaiKhoan::QUYEN_NHANVIEN]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (TaiKhoan $taiKhoan) {
            KhachHang::create([
                'HoTen' => fake()->name(),
                'Email' => $taiKhoan->Email,
                'SoDienThoai' => '09' . fake()->numerify('########'),
                'DiaChi' => fake()->address(),
                'MaTaiKhoan' => $taiKhoan->MaTaiKhoan,
                'NgayTao' => now(),
            ]);
        });
    }
}
