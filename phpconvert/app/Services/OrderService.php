<?php

namespace App\Services;

use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietSanPham;
use App\Models\GioHang;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Tạo đơn hàng mới từ giỏ hàng (chống Deadlock và tranh chấp kho bằng Pessimistic Lock)
     */
    public function createOrder(int $maKhachHang, array $orderData, $cartItems, string $initialStatus = 'Đang xử lý'): DonHang
    {
        return DB::transaction(function () use ($maKhachHang, $orderData, $cartItems, $initialStatus) {
            $tongTien = 0;
            $orderDetails = [];

            // 1. Kiểm tra tồn kho và khóa hàng với lockForUpdate()
            foreach ($cartItems as $item) {
                $ctsp = ChiTietSanPham::where('MaChiTiet', $item->MaChiTietSanPham)
                    ->lockForUpdate()
                    ->first();

                if (!$ctsp) {
                    throw new Exception("Sản phẩm biến thể ID: {$item->MaChiTietSanPham} không tồn tại.");
                }

                if (($ctsp->SoLuongTon ?? 0) < $item->SoLuong) {
                    $tenSp = $ctsp->sanPham ? $ctsp->sanPham->TenSanPham : 'Sản phẩm';
                    throw new Exception("Sản phẩm '{$tenSp}' vừa hết hàng hoặc không đủ số lượng tồn kho.");
                }

                // Trừ tồn kho
                $ctsp->SoLuongTon = max(0, $ctsp->SoLuongTon - $item->SoLuong);
                $ctsp->save();

                // Tính giá bán thực tế (có trừ khuyến mãi)
                $donGia = $ctsp->effective_price;
                $thanhTien = $donGia * $item->SoLuong;
                $tongTien += $thanhTien;

                $orderDetails[] = [
                    'MaChiTietSanPham' => $ctsp->MaChiTiet,
                    'SoLuong' => $item->SoLuong,
                    'DonGia' => $donGia,
                    'ThanhTien' => $thanhTien,
                ];
            }

            // 2. Tạo bản ghi đơn hàng
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

            // 3. Lưu chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                $detail['MaDonHang'] = $donHang->MaDonHang;
                ChiTietDonHang::create($detail);
            }

            // 4. Xóa giỏ hàng
            GioHang::where('MaKhachHang', $maKhachHang)->delete();

            return $donHang;
        }, 3); // Tự động retry tối đa 3 lần nếu gặp deadlock (mã lỗi 1205)
    }

    /**
     * Hủy đơn hàng và hoàn trả số lượng vào kho
     */
    public function cancelOrder(int $maDonHang, ?int $maKhachHang = null, bool $isAdmin = false): bool
    {
        return DB::transaction(function () use ($maDonHang, $maKhachHang, $isAdmin) {
            $query = DonHang::with('chiTietDonHangs')->where('MaDonHang', $maDonHang);
            if (!$isAdmin && $maKhachHang !== null) {
                $query->where('MaKhachHang', $maKhachHang);
            }

            $order = $query->first();
            if (!$order) {
                throw new Exception("Không tìm thấy đơn hàng.");
            }

            if (!$isAdmin && !in_array($order->TrangThai, ['Chờ xác nhận', 'Chờ thanh toán MoMo', 'Đang xử lý'])) {
                throw new Exception("Đơn hàng đang giao hoặc đã hoàn tất, không thể hủy.");
            }

            // Hoàn lại số lượng tồn kho
            foreach ($order->chiTietDonHangs as $item) {
                $ctsp = ChiTietSanPham::find($item->MaChiTietSanPham);
                if ($ctsp) {
                    $ctsp->increment('SoLuongTon', $item->SoLuong);
                }
            }

            $order->TrangThai = 'Đã hủy';
            $order->save();

            return true;
        });
    }

    /**
     * Cập nhật trạng thái đơn hàng từ trang Quản trị Admin
     */
    public function setTrangThai(int $maDonHang, string $trangThaiMoi): bool
    {
        return DB::transaction(function () use ($maDonHang, $trangThaiMoi) {
            $order = DonHang::with('chiTietDonHangs')->find($maDonHang);
            if (!$order) {
                throw new Exception("Không tìm thấy đơn hàng.");
            }

            $trangThaiCu = $order->TrangThai;

            // CASE 1: Chờ xác nhận -> Đang xử lý (Trừ kho)
            if ($trangThaiCu == "Chờ xác nhận" && $trangThaiMoi == "Đang xử lý") {
                foreach ($order->chiTietDonHangs as $item) {
                    $ctsp = ChiTietSanPham::find($item->MaChiTietSanPham);
                    if (!$ctsp || ($ctsp->SoLuongTon ?? 0) < ($item->SoLuong ?? 0)) {
                        throw new Exception("Không đủ tồn kho cho biến thể ID: {$item->MaChiTietSanPham}.");
                    }
                }
                foreach ($order->chiTietDonHangs as $item) {
                    $ctsp = ChiTietSanPham::find($item->MaChiTietSanPham);
                    if ($ctsp) {
                        $ctsp->SoLuongTon = max(0, ($ctsp->SoLuongTon ?? 0) - ($item->SoLuong ?? 0));
                        $ctsp->save();
                    }
                }
            }
            // CASE 2: Đang xử lý / Đang giao hàng -> Đã hủy (Hoàn trả kho)
            else if (in_array($trangThaiCu, ["Đang xử lý", "Đang giao hàng"]) && $trangThaiMoi == "Đã hủy") {
                foreach ($order->chiTietDonHangs as $item) {
                    $ctsp = ChiTietSanPham::find($item->MaChiTietSanPham);
                    if ($ctsp) {
                        $ctsp->increment('SoLuongTon', $item->SoLuong ?? 0);
                    }
                }
            }

            $order->TrangThai = $trangThaiMoi;
            $order->save();

            return true;
        });
    }
}
