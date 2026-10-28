<?php

namespace Database\Seeders;

use App\Models\ChiTietSanPham;
use App\Models\KhachHang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Tạo một số đơn hàng mẫu rải đều trong năm hiện tại để Dashboard thống kê có dữ liệu.
 * (Không trừ tồn kho vì đây là dữ liệu lịch sử minh họa.)
 */
class DonHangMauSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026);

        $khachHangIds = KhachHang::whereHas('taiKhoan', fn ($q) => $q->where('MaQuyen', 3))->pluck('MaKhachHang')->all();
        $bienThes = ChiTietSanPham::with('sanPham.khuyenMai')->get();
        if (empty($khachHangIds) || $bienThes->isEmpty()) {
            return;
        }

        $trangThais = ['Đã hoàn thành', 'Đã hoàn thành', 'Đã hoàn thành', 'Đang giao hàng', 'Đang xử lý', 'Đã hủy'];
        $nam = (int) date('Y');
        $thangHienTai = (int) date('n');

        for ($i = 0; $i < 24; $i++) {
            $thang = mt_rand(1, $thangHienTai);
            $ngay = min(mt_rand(1, 28), $thang === $thangHienTai ? (int) date('j') : 28);
            $ngayDat = sprintf('%04d-%02d-%02d %02d:%02d:00', $nam, $thang, $ngay, mt_rand(8, 21), mt_rand(0, 59));

            $chiTiet = [];
            $tongTien = 0;
            foreach ($bienThes->random(mt_rand(1, 3)) as $bt) {
                $soLuong = mt_rand(1, 2);
                $donGia = (float) $bt->effective_price;
                $thanhTien = $donGia * $soLuong;
                $tongTien += $thanhTien;
                $chiTiet[] = [
                    'MaChiTietSanPham' => $bt->MaChiTiet,
                    'SoLuong' => $soLuong,
                    'DonGia' => $donGia,
                    'ThanhTien' => $thanhTien,
                ];
            }

            $maDon = DB::table('DonHang')->insertGetId([
                'NgayDat' => $ngayDat,
                'TrangThai' => $trangThais[array_rand($trangThais)],
                'TongTien' => $tongTien,
                'TongTienSauGiam' => $tongTien,
                'TienGiam' => 0,
                'MaKhachHang' => $khachHangIds[array_rand($khachHangIds)],
                'DiaChiGiaoHang' => '140 Lê Trọng Tấn, Tây Thạnh, Tân Phú, TP.HCM',
                'SoDienThoaiNhanHang' => '0901234567',
                'GhiChu' => 'Đơn hàng mẫu',
            ], 'MaDonHang');

            foreach ($chiTiet as $ct) {
                DB::table('ChiTietDonHang')->insert($ct + ['MaDonHang' => $maDon]);
            }
        }
    }
}
