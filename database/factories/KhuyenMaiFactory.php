<?php

namespace Database\Factories;

use App\Models\KhuyenMai;
use Illuminate\Database\Eloquent\Factories\Factory;

class KhuyenMaiFactory extends Factory
{
    protected $model = KhuyenMai::class;

    public function definition(): array
    {
        $batDau = fake()->dateTimeBetween('-1 month', 'now');

        return [
            'TenChuongTrinh' => 'Khuyến mãi '.fake()->unique()->words(2, true),
            'MoTa' => fake()->sentence(),
            'NgayBatDau' => $batDau->format('Y-m-d'),
            'NgayKetThuc' => fake()->dateTimeBetween('+1 week', '+3 months')->format('Y-m-d'),
            'PhanTramGiam' => fake()->randomElement([5, 10, 15, 20, 30]),
            'GiamToiDa' => fake()->randomElement([null, 100000, 200000, 500000]),
            'SoLuongSuDung' => fake()->numberBetween(10, 300),
        ];
    }

    public function hetHan(): static
    {
        return $this->state(fn () => [
            'NgayBatDau' => now()->subMonths(2)->toDateString(),
            'NgayKetThuc' => now()->subMonth()->toDateString(),
        ]);
    }
}
