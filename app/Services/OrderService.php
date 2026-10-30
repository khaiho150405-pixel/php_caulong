<?php

namespace App\Services;

use App\Models\ChiTietDonHang;
use App\Models\ChiTietSanPham;
use App\Models\DonHang;
use App\Models\GioHang;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(int $maKhachHang, array $orderData, $cartItems, string $initialStatus = 'Đang xử lý', bool $xoaGioHang = true): DonHang
    {
        return DB::transaction(function () use ($maKhachHang, $orderData, $cartItems, $initialStatus, $xoaGioHang) {
            $tongTien = 0;
            $orderDetails = [];

            foreach ($cartItems as $item) {
                $ctsp = ChiTietSanPham::with(['sanPham.khuyenMai'])
                    ->where('MaChiTiet', $item->MaChiTietSanPham)
                    ->lockForUpdate()
                    ->first();

                if (! $ctsp) {
                    throw new Exception("Sản phẩm biến thể ID: {$item->MaChiTietSanPham} không tồn tại.");
                }

                if (($ctsp->SoLuongTon ?? 0) < $item->SoLuong) {
                    $tenSp = $ctsp->sanPham ? $ctsp->sanPham->TenSanPham : 'Sản phẩm';
                    throw new Exception("Sản phẩm '{$tenSp}' vừa hết hàng hoặc không đủ số lượng tồn kho.");
                }

                $ctsp->SoLuongTon = max(0, $ctsp->SoLuongTon - $item->SoLuong);
                $ctsp->save();

                $donGia = (float) $ctsp->effective_price;
                $thanhTien = $donGia * $item->SoLuong;
                $tongTien += $thanhTien;

                $orderDetails[] = [
                    'MaChiTietSanPham' => $ctsp->MaChiTiet,
                    'SoLuong' => $item->SoLuong,
                    'DonGia' => $donGia,
                    'ThanhTien' => $thanhTien,
                ];
            }

            $donHang = DonHang::create([
                'NgayDat' => now(),
                'TrangThai' => $initialStatus,
                'TongTien' => $tongTien,
                'TongTienSauGiam' => $tongTien,
                'MaKhachHang' => $maKhachHang,
                'TienGiam' => 0,
                'DiaChiGiaoHang' => $orderData['DiaChiGiaoHang'] ?? 'Tại cửa hàng',
                'SoDienThoaiNhanHang' => $orderData['SoDienThoaiNhanHang'] ?? '0000000000',
                'GhiChu' => $orderData['GhiChu'] ?? '',
            ]);

            foreach ($orderDetails as $detail) {
                $detail['MaDonHang'] = $donHang->MaDonHang;
                ChiTietDonHang::create($detail);
            }

            if ($xoaGioHang) {
                GioHang::where('MaKhachHang', $maKhachHang)->delete();
            }

            return $donHang;
        }, 3);
    }

    public const CHUYEN_TRANG_THAI = [
        'Chờ xác nhận' => ['Đang xử lý', 'Đã hủy'],
        'Chờ thanh toán MoMo' => ['Đang xử lý', 'Đã hủy'],
        'Đã thanh toán bằng MoMo' => ['Đang giao hàng', 'Đã hủy'],
        'Đang xử lý' => ['Đang giao hàng', 'Đã hủy'],
        'Đang giao hàng' => ['Đã hoàn thành', 'Đã hủy'],
    ];

    public const KHACH_DUOC_HUY = ['Chờ xác nhận', 'Chờ thanh toán MoMo', 'Đang xử lý'];

    public function cancelOrder(int $maDonHang, ?int $maKhachHang = null, bool $isAdmin = false): bool
    {
        return DB::transaction(function () use ($maDonHang, $maKhachHang, $isAdmin) {
            $query = DonHang::with('chiTietDonHangs')->where('MaDonHang', $maDonHang)->lockForUpdate();
            if (! $isAdmin && $maKhachHang !== null) {
                $query->where('MaKhachHang', $maKhachHang);
            }

            $order = $query->first();
            if (! $order) {
                throw new Exception('Không tìm thấy đơn hàng.');
            }

            $choPhep = $isAdmin ? array_keys(self::CHUYEN_TRANG_THAI) : self::KHACH_DUOC_HUY;
            if (! in_array($order->TrangThai, $choPhep, true)) {
                throw new Exception('Đơn hàng đang giao, đã hoàn tất hoặc đã hủy, không thể hủy.');
            }

            $this->hoanKho($order);
            $order->TrangThai = 'Đã hủy';
            $order->save();

            return true;
        });
    }

    public function huyDoThanhToanThatBai(int $maDonHang): void
    {
        DB::transaction(function () use ($maDonHang) {
            $order = DonHang::with('chiTietDonHangs')->lockForUpdate()->find($maDonHang);
            if (! $order || $order->TrangThai !== 'Chờ thanh toán MoMo') {
                return;
            }

            $this->hoanKho($order);
            $order->TrangThai = 'Đã hủy (Thanh toán thất bại)';
            $order->save();
        });
    }

    public function setTrangThai(int $maDonHang, string $trangThaiMoi): bool
    {
        return DB::transaction(function () use ($maDonHang, $trangThaiMoi) {
            $order = DonHang::with('chiTietDonHangs')->lockForUpdate()->find($maDonHang);
            if (! $order) {
                throw new Exception('Không tìm thấy đơn hàng.');
            }

            $trangThaiCu = $order->TrangThai;
            $hopLe = self::CHUYEN_TRANG_THAI[$trangThaiCu] ?? [];

            if (! in_array($trangThaiMoi, $hopLe, true)) {
                throw new Exception("Không thể chuyển đơn hàng từ '{$trangThaiCu}' sang '{$trangThaiMoi}'.");
            }

            if ($trangThaiMoi === 'Đã hủy') {
                $this->hoanKho($order);
            }

            $order->TrangThai = $trangThaiMoi;
            $order->save();

            return true;
        });
    }

    private function hoanKho(DonHang $order): void
    {
        foreach ($order->chiTietDonHangs as $item) {
            if ($item->MaChiTietSanPham) {
                ChiTietSanPham::where('MaChiTiet', $item->MaChiTietSanPham)
                    ->increment('SoLuongTon', (int) ($item->SoLuong ?? 0));
            }
        }
    }
}
