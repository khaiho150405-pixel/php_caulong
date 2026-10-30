<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhuKienSeeder extends Seeder
{
    public function run(): void
    {
        $now = now()->toDateTimeString();

        $items = [
            [
                'ten' => 'Cầu Lông Lông Vịt Thi Đấu (Ống 12 Quả)',
                'mo_ta' => 'Cầu lông lông vịt chọn lọc, đầu cork bọc da, quỹ đạo bay ổn định. Phù hợp thi đấu và tập luyện thường xuyên.',
                'gia' => 340000, 'anh' => 'pk_cau_thi_dau.jpg', 'km' => 2, 'ton' => 120, 'sku' => 'PK-CAU-THIDAU-12',
            ],
            [
                'ten' => 'Cầu Lông Lông Ngỗng Cao Cấp (Ống 12 Quả)',
                'mo_ta' => 'Lông ngỗng cao cấp, độ bền cao, giữ nhịp bay đều khi đánh mạnh. Dành cho người chơi yêu cầu chất lượng cầu tốt.',
                'gia' => 520000, 'anh' => 'pk_cau_cao_cap.jpg', 'km' => null, 'ton' => 80, 'sku' => 'PK-CAU-CAOCAP-12',
            ],
            [
                'ten' => 'Cầu Lông Lông Vịt Tập Luyện (Ống 12 Quả)',
                'mo_ta' => 'Cầu tập luyện giá tốt, độ bền khá, phù hợp câu lạc bộ và người chơi phong trào.',
                'gia' => 240000, 'anh' => 'pk_cau_tap_luyen.jpg', 'km' => 3, 'ton' => 150, 'sku' => 'PK-CAU-TAPLUYEN-12',
            ],
            [
                'ten' => 'Dây Cuốn Cán Vợt Cầu Lông (Cuộn Đơn)',
                'mo_ta' => 'Quấn cán mỏng, bám tay, thấm mồ hôi tốt. Dễ quấn, giúp cầm vợt chắc và không trơn khi chơi lâu.',
                'gia' => 35000, 'anh' => 'pk_day_cuon_can.jpg', 'km' => null, 'ton' => 300, 'sku' => 'PK-QUANCAN-1',
            ],
            [
                'ten' => 'Dây Cuốn Cán Vợt Cầu Lông (Combo 3 Cuộn)',
                'mo_ta' => 'Combo 3 cuộn quấn cán tiết kiệm, đủ để thay mới định kỳ cho cây vợt của bạn.',
                'gia' => 90000, 'anh' => 'pk_day_cuon_can.jpg', 'km' => 2, 'ton' => 200, 'sku' => 'PK-QUANCAN-3',
            ],
        ];

        foreach ($items as $it) {
            DB::table('SanPham')->updateOrInsert(
                ['TenSanPham' => $it['ten']],
                [
                    'MoTa' => $it['mo_ta'],
                    'GiaGoc' => $it['gia'],
                    'HinhAnhDaiDien' => $it['anh'],
                    'MaLoai' => 4,
                    'MaNhaCungCap' => 5,
                    'MaHang' => null,
                    'MaKhuyenMai' => $it['km'],
                    'CoSize' => 0,
                    'CoMau' => 0,
                    'NgayTao' => $now,
                    'deleted_at' => null,
                ]
            );

            $maSp = DB::table('SanPham')->where('TenSanPham', $it['ten'])->value('MaSanPham');

            DB::table('ChiTietSanPham')->updateOrInsert(
                ['SKU' => $it['sku']],
                [
                    'MaSanPham' => $maSp,
                    'MaMau' => null,
                    'MaSize' => null,
                    'GiaBan' => $it['gia'],
                    'SoLuongTon' => $it['ton'],
                    'HinhAnh' => $it['anh'],
                ]
            );
        }
    }
}
