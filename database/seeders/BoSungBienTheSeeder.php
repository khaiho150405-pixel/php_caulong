<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoSungBienTheSeeder extends Seeder
{
    public function run(): void
    {

        $existingRacketSpecs = [
            18 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            19 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            20 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '5U (75-79g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '670mm', 'SucCang' => '20-30 lbs'],
            21 => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            22 => ['DoCanBang' => 'Rất nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Rất cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-31 lbs'],
            23 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            24 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            25 => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '5U (75-79g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
        ];

        foreach ($existingRacketSpecs as $maChiTiet => $spec) {
            if (DB::table('ChiTietSanPham')->where('MaChiTiet', $maChiTiet)->exists()) {
                DB::table('ThongSoVot')->updateOrInsert(
                    ['MaChiTiet' => $maChiTiet],
                    $spec
                );
            }
        }

        $newVariants = [

            [
                'MaSanPham' => 14, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 3100000, 'SoLuongTon' => 50,
                'SKU' => 'LN-CAL600-DEN-3U', 'HinhAnh' => 'lining_calibar600.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],
            [
                'MaSanPham' => 14, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 3100000, 'SoLuongTon' => 80,
                'SKU' => 'LN-CAL600-DEN-4U', 'HinhAnh' => 'lining_calibar600.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            ],
            [
                'MaSanPham' => 14, 'MaMau' => 5, 'MaSize' => 2, 'GiaBan' => 3100000, 'SoLuongTon' => 45,
                'SKU' => 'LN-CAL600-BAC-4U', 'HinhAnh' => 'lining_calibar600.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            ],

            [
                'MaSanPham' => 15, 'MaMau' => 3, 'MaSize' => 1, 'GiaBan' => 4200000, 'SoLuongTon' => 50,
                'SKU' => 'LN-BLADEX800-DO-3U', 'HinhAnh' => 'lining_bladex800.jpg',
                'spec' => ['DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-31 lbs'],
            ],
            [
                'MaSanPham' => 15, 'MaMau' => 3, 'MaSize' => 2, 'GiaBan' => 4200000, 'SoLuongTon' => 70,
                'SKU' => 'LN-BLADEX800-DO-4U', 'HinhAnh' => 'lining_bladex800.jpg',
                'spec' => ['DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],
            [
                'MaSanPham' => 15, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 4200000, 'SoLuongTon' => 60,
                'SKU' => 'LN-BLADEX800-XANH-4U', 'HinhAnh' => 'lining_bladex800_xanh.jpg',
                'spec' => ['DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],

            [
                'MaSanPham' => 16, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 3500000, 'SoLuongTon' => 40,
                'SKU' => 'LN-TEC9-DEN-3U', 'HinhAnh' => 'lining_tectonic9.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-32 lbs'],
            ],
            [
                'MaSanPham' => 16, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 3500000, 'SoLuongTon' => 75,
                'SKU' => 'LN-TEC9-DEN-4U', 'HinhAnh' => 'lining_tectonic9.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-31 lbs'],
            ],
            [
                'MaSanPham' => 16, 'MaMau' => 1, 'MaSize' => 3, 'GiaBan' => 3500000, 'SoLuongTon' => 35,
                'SKU' => 'LN-TEC9-DEN-5U', 'HinhAnh' => 'lining_tectonic9.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '5U (75-79g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],

            [
                'MaSanPham' => 17, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4100000, 'SoLuongTon' => 40,
                'SKU' => 'VT-AS100X-DEN-3U', 'HinhAnh' => 'victor_as100x_ultra.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            ],
            [
                'MaSanPham' => 17, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 4100000, 'SoLuongTon' => 60,
                'SKU' => 'VT-AS100X-DEN-4U', 'HinhAnh' => 'victor_as100x_ultra.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ],

            [
                'MaSanPham' => 18, 'MaMau' => 3, 'MaSize' => 1, 'GiaBan' => 3500000, 'SoLuongTon' => 45,
                'SKU' => 'VT-RYUGA-DO-3U', 'HinhAnh' => 'victor_ryuga_metallic.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '22-31 lbs'],
            ],
            [
                'MaSanPham' => 18, 'MaMau' => 3, 'MaSize' => 2, 'GiaBan' => 3500000, 'SoLuongTon' => 85,
                'SKU' => 'VT-RYUGA-DO-4U', 'HinhAnh' => 'victor_ryuga_metallic.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],

            [
                'MaSanPham' => 19, 'MaMau' => 2, 'MaSize' => 1, 'GiaBan' => 3200000, 'SoLuongTon' => 50,
                'SKU' => 'VT-BRS12-XANH-3U', 'HinhAnh' => 'victor_brs12_pro.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ],
            [
                'MaSanPham' => 19, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 3200000, 'SoLuongTon' => 90,
                'SKU' => 'VT-BRS12-XANH-4U', 'HinhAnh' => 'victor_brs12_pro.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '19-27 lbs'],
            ],

            [
                'MaSanPham' => 20, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 2850000, 'SoLuongTon' => 40,
                'SKU' => 'VT-JS10-DEN-3U', 'HinhAnh' => 'victor_js10.jpg',
                'spec' => ['DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ],
            [
                'MaSanPham' => 20, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 2850000, 'SoLuongTon' => 70,
                'SKU' => 'VT-JS10-DEN-4U', 'HinhAnh' => 'victor_js10.jpg',
                'spec' => ['DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '19-27 lbs'],
            ],

            [
                'MaSanPham' => 21, 'MaMau' => 2, 'MaSize' => 1, 'GiaBan' => 3800000, 'SoLuongTon' => 50,
                'SKU' => 'VT-DX12-XANH-3U', 'HinhAnh' => 'victor_dx12.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-29 lbs'],
            ],
            [
                'MaSanPham' => 21, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 3800000, 'SoLuongTon' => 60,
                'SKU' => 'VT-DX12-XANH-4U', 'HinhAnh' => 'victor_dx12.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ],

            [
                'MaSanPham' => 22, 'MaMau' => 6, 'MaSize' => 2, 'GiaBan' => 2150000, 'SoLuongTon' => 50,
                'SKU' => 'VT-TFCLAW-TRANG-4U', 'HinhAnh' => 'victor_tfclaw.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-30 lbs'],
            ],
            [
                'MaSanPham' => 22, 'MaMau' => 6, 'MaSize' => 3, 'GiaBan' => 2150000, 'SoLuongTon' => 50,
                'SKU' => 'VT-TFCLAW-TRANG-5U', 'HinhAnh' => 'victor_tfclaw.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '5U (75-79g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '19-29 lbs'],
            ],

            [
                'MaSanPham' => 23, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4800000, 'SoLuongTon' => 30,
                'SKU' => 'MZ-F10POWER-DEN-3U', 'HinhAnh' => 'mizuno_f10power.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '23-29 lbs'],
            ],
            [
                'MaSanPham' => 23, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 4800000, 'SoLuongTon' => 50,
                'SKU' => 'MZ-F10POWER-DEN-4U', 'HinhAnh' => 'mizuno_f10power.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '22-28 lbs'],
            ],

            [
                'MaSanPham' => 24, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4800000, 'SoLuongTon' => 30,
                'SKU' => 'MZ-F10QUICK-DEN-3U', 'HinhAnh' => 'mizuno_f10quick.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '23-29 lbs'],
            ],
            [
                'MaSanPham' => 24, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 4800000, 'SoLuongTon' => 50,
                'SKU' => 'MZ-F10QUICK-DEN-4U', 'HinhAnh' => 'mizuno_f10quick.jpg',
                'spec' => ['DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '22-28 lbs'],
            ],

            [
                'MaSanPham' => 25, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4990000, 'SoLuongTon' => 30,
                'SKU' => 'MZ-JPXRES-DEN-3U', 'HinhAnh' => 'mizuno_jpx_reserve.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '22-30 lbs'],
            ],
            [
                'MaSanPham' => 25, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 4990000, 'SoLuongTon' => 40,
                'SKU' => 'MZ-JPXRES-DEN-4U', 'HinhAnh' => 'mizuno_jpx_reserve.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ],

            [
                'MaSanPham' => 26, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 3599000, 'SoLuongTon' => 45,
                'SKU' => 'MZ-FORTIUS70-DEN-4U', 'HinhAnh' => 'mizuno_fortius70.jpg',
                'spec' => ['DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '22-28 lbs'],
            ],
            [
                'MaSanPham' => 26, 'MaMau' => 1, 'MaSize' => 3, 'GiaBan' => 3599000, 'SoLuongTon' => 45,
                'SKU' => 'MZ-FORTIUS70-DEN-5U', 'HinhAnh' => 'mizuno_fortius70.jpg',
                'spec' => ['DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '5U (75-79g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-27 lbs'],
            ],

            [
                'MaSanPham' => 27, 'MaMau' => 6, 'MaSize' => 1, 'GiaBan' => 4500000, 'SoLuongTon' => 30,
                'SKU' => 'MZ-ALTIUS01-TRANG-3U', 'HinhAnh' => 'mizuno_altius01speedlimited.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '22-28 lbs'],
            ],
            [
                'MaSanPham' => 27, 'MaMau' => 6, 'MaSize' => 2, 'GiaBan' => 4500000, 'SoLuongTon' => 50,
                'SKU' => 'MZ-ALTIUS01-TRANG-4U', 'HinhAnh' => 'mizuno_altius01speedlimited.jpg',
                'spec' => ['DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '21-27 lbs'],
            ],

            [
                'MaSanPham' => 31, 'MaMau' => 1, 'MaSize' => 19, 'GiaBan' => 3000000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS012-DEN-40', 'HinhAnh' => 'lining_ayas012_den.jpg',
            ],
            [
                'MaSanPham' => 31, 'MaMau' => 1, 'MaSize' => 20, 'GiaBan' => 3000000, 'SoLuongTon' => 25,
                'SKU' => 'LN-AYAS012-DEN-41', 'HinhAnh' => 'lining_ayas012_den.jpg',
            ],
            [
                'MaSanPham' => 31, 'MaMau' => 1, 'MaSize' => 21, 'GiaBan' => 3000000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS012-DEN-42', 'HinhAnh' => 'lining_ayas012_den.jpg',
            ],
            [
                'MaSanPham' => 31, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 3000000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS012-TRANG-41', 'HinhAnh' => 'lining_ayas012_trang.jpg',
            ],
            [
                'MaSanPham' => 31, 'MaMau' => 6, 'MaSize' => 21, 'GiaBan' => 3000000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS012-TRANG-42', 'HinhAnh' => 'lining_ayas012_trang.jpg',
            ],

            [
                'MaSanPham' => 32, 'MaMau' => 6, 'MaSize' => 19, 'GiaBan' => 2160000, 'SoLuongTon' => 25,
                'SKU' => 'LN-AYZU017-TRANG-40', 'HinhAnh' => 'lining_ayzu017.jpg',
            ],
            [
                'MaSanPham' => 32, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 2160000, 'SoLuongTon' => 30,
                'SKU' => 'LN-AYZU017-TRANG-41', 'HinhAnh' => 'lining_ayzu017.jpg',
            ],
            [
                'MaSanPham' => 32, 'MaMau' => 6, 'MaSize' => 21, 'GiaBan' => 2160000, 'SoLuongTon' => 25,
                'SKU' => 'LN-AYZU017-TRANG-42', 'HinhAnh' => 'lining_ayzu017.jpg',
            ],

            [
                'MaSanPham' => 33, 'MaMau' => 2, 'MaSize' => 19, 'GiaBan' => 2450000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS018-XANH-40', 'HinhAnh' => 'lining_ayas018.jpg',
            ],
            [
                'MaSanPham' => 33, 'MaMau' => 2, 'MaSize' => 20, 'GiaBan' => 2450000, 'SoLuongTon' => 30,
                'SKU' => 'LN-AYAS018-XANH-41', 'HinhAnh' => 'lining_ayas018.jpg',
            ],
            [
                'MaSanPham' => 33, 'MaMau' => 2, 'MaSize' => 21, 'GiaBan' => 2450000, 'SoLuongTon' => 20,
                'SKU' => 'LN-AYAS018-XANH-42', 'HinhAnh' => 'lining_ayas018.jpg',
            ],

            [
                'MaSanPham' => 34, 'MaMau' => 1, 'MaSize' => 19, 'GiaBan' => 2190000, 'SoLuongTon' => 20,
                'SKU' => 'VT-A970ACE-DEN-40', 'HinhAnh' => 'victor_a970.jpg',
            ],
            [
                'MaSanPham' => 34, 'MaMau' => 1, 'MaSize' => 20, 'GiaBan' => 2190000, 'SoLuongTon' => 35,
                'SKU' => 'VT-A970ACE-DEN-41', 'HinhAnh' => 'victor_a970.jpg',
            ],
            [
                'MaSanPham' => 34, 'MaMau' => 1, 'MaSize' => 21, 'GiaBan' => 2190000, 'SoLuongTon' => 25,
                'SKU' => 'VT-A970ACE-DEN-42', 'HinhAnh' => 'victor_a970.jpg',
            ],

            [
                'MaSanPham' => 35, 'MaMau' => 6, 'MaSize' => 19, 'GiaBan' => 2140000, 'SoLuongTon' => 20,
                'SKU' => 'VT-P9200III-TRANG-40', 'HinhAnh' => 'victor_p9200iii.jpg',
            ],
            [
                'MaSanPham' => 35, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 2140000, 'SoLuongTon' => 30,
                'SKU' => 'VT-P9200III-TRANG-41', 'HinhAnh' => 'victor_p9200iii.jpg',
            ],
            [
                'MaSanPham' => 35, 'MaMau' => 6, 'MaSize' => 21, 'GiaBan' => 2140000, 'SoLuongTon' => 25,
                'SKU' => 'VT-P9200III-TRANG-42', 'HinhAnh' => 'victor_p9200iii.jpg',
            ],

            [
                'MaSanPham' => 36, 'MaMau' => 2, 'MaSize' => 19, 'GiaBan' => 2750000, 'SoLuongTon' => 20,
                'SKU' => 'MZ-WCLAW2-XANH-40', 'HinhAnh' => 'mizuno_waveclaw_neo2_xanh.jpg',
            ],
            [
                'MaSanPham' => 36, 'MaMau' => 2, 'MaSize' => 20, 'GiaBan' => 2750000, 'SoLuongTon' => 30,
                'SKU' => 'MZ-WCLAW2-XANH-41', 'HinhAnh' => 'mizuno_waveclaw_neo2_xanh.jpg',
            ],
            [
                'MaSanPham' => 36, 'MaMau' => 2, 'MaSize' => 21, 'GiaBan' => 2750000, 'SoLuongTon' => 20,
                'SKU' => 'MZ-WCLAW2-XANH-42', 'HinhAnh' => 'mizuno_waveclaw_neo2_xanh.jpg',
            ],
            [
                'MaSanPham' => 36, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 2750000, 'SoLuongTon' => 20,
                'SKU' => 'MZ-WCLAW2-TRANG-41', 'HinhAnh' => 'mizuno_waveclaw_neo2_trang.jpg',
            ],

            [
                'MaSanPham' => 37, 'MaMau' => 6, 'MaSize' => 19, 'GiaBan' => 3900000, 'SoLuongTon' => 15,
                'SKU' => 'MZ-WFANGPRO-TRANG-40', 'HinhAnh' => 'mizuno_fangpro.jpg',
            ],
            [
                'MaSanPham' => 37, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 3900000, 'SoLuongTon' => 25,
                'SKU' => 'MZ-WFANGPRO-TRANG-41', 'HinhAnh' => 'mizuno_fangpro.jpg',
            ],
            [
                'MaSanPham' => 37, 'MaMau' => 6, 'MaSize' => 21, 'GiaBan' => 3900000, 'SoLuongTon' => 20,
                'SKU' => 'MZ-WFANGPRO-TRANG-42', 'HinhAnh' => 'mizuno_fangpro.jpg',
            ],

            [
                'MaSanPham' => 41, 'MaMau' => 2, 'MaSize' => 5, 'GiaBan' => 130000, 'SoLuongTon' => 30,
                'SKU' => 'LN-A421-XANH-M', 'HinhAnh' => 'lining_a421.jpg',
            ],
            [
                'MaSanPham' => 41, 'MaMau' => 2, 'MaSize' => 6, 'GiaBan' => 130000, 'SoLuongTon' => 40,
                'SKU' => 'LN-A421-XANH-L', 'HinhAnh' => 'lining_a421.jpg',
            ],
            [
                'MaSanPham' => 41, 'MaMau' => 2, 'MaSize' => 7, 'GiaBan' => 130000, 'SoLuongTon' => 30,
                'SKU' => 'LN-A421-XANH-XL', 'HinhAnh' => 'lining_a421.jpg',
            ],

            [
                'MaSanPham' => 42, 'MaMau' => 3, 'MaSize' => 5, 'GiaBan' => 160000, 'SoLuongTon' => 30,
                'SKU' => 'LN-3715-DO-M', 'HinhAnh' => 'lining_3715.jpg',
            ],
            [
                'MaSanPham' => 42, 'MaMau' => 3, 'MaSize' => 6, 'GiaBan' => 160000, 'SoLuongTon' => 40,
                'SKU' => 'LN-3715-DO-L', 'HinhAnh' => 'lining_3715.jpg',
            ],
            [
                'MaSanPham' => 42, 'MaMau' => 3, 'MaSize' => 7, 'GiaBan' => 160000, 'SoLuongTon' => 30,
                'SKU' => 'LN-3715-DO-XL', 'HinhAnh' => 'lining_3715.jpg',
            ],

            [
                'MaSanPham' => 43, 'MaMau' => 1, 'MaSize' => 5, 'GiaBan' => 110000, 'SoLuongTon' => 35,
                'SKU' => 'LN-Q21-DEN-M', 'HinhAnh' => 'lining_q21.jpg',
            ],
            [
                'MaSanPham' => 43, 'MaMau' => 1, 'MaSize' => 6, 'GiaBan' => 110000, 'SoLuongTon' => 45,
                'SKU' => 'LN-Q21-DEN-L', 'HinhAnh' => 'lining_q21.jpg',
            ],
            [
                'MaSanPham' => 43, 'MaMau' => 1, 'MaSize' => 7, 'GiaBan' => 110000, 'SoLuongTon' => 35,
                'SKU' => 'LN-Q21-DEN-XL', 'HinhAnh' => 'lining_q21.jpg',
            ],
        ];

        foreach ($newVariants as $item) {
            $spec = $item['spec'] ?? null;
            unset($item['spec']);

            if (! DB::table('SanPham')->where('MaSanPham', $item['MaSanPham'])->exists()) {
                continue;
            }

            DB::table('ChiTietSanPham')->updateOrInsert(
                ['SKU' => $item['SKU']],
                $item
            );

            if ($spec) {
                $chiTiet = DB::table('ChiTietSanPham')->where('SKU', $item['SKU'])->first();
                if ($chiTiet) {
                    DB::table('ThongSoVot')->updateOrInsert(
                        ['MaChiTiet' => $chiTiet->MaChiTiet],
                        $spec
                    );
                }
            }
        }
    }
}
