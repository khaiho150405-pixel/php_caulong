<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\Request;

class QuanLyDonHangController extends Controller
{
    /**
     * Danh sách đơn hàng: tìm theo mã đơn / tên khách / SĐT, lọc trạng thái, phân trang.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $trangThai = $request->query('trangThai');

        $orders = DonHang::with('khachHang')
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $maDon = ltrim($keyword, '#');
                    if (ctype_digit($maDon)) {
                        $q->orWhere('MaDonHang', (int) $maDon);
                    }
                    $q->orWhere('SoDienThoaiNhanHang', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('khachHang', fn ($kh) => $kh->where('HoTen', 'LIKE', "%{$keyword}%"));
                });
            })
            ->when($trangThai, fn ($q) => $q->where('TrangThai', $trangThai))
            ->orderByDesc('NgayDat')
            ->orderByDesc('MaDonHang')
            ->paginate(10)
            ->withQueryString();

        $dsTrangThai = DonHang::query()->distinct()->orderBy('TrangThai')->pluck('TrangThai');

        return view('QuanLyDonHang.Index', compact('orders', 'keyword', 'trangThai', 'dsTrangThai'));
    }

    public function details($id)
    {
        $order = DonHang::with([
            'khachHang',
            'chiTietDonHangs.chiTietSanPham.sanPham',
            'chiTietDonHangs.chiTietSanPham.mauSac',
            'chiTietDonHangs.chiTietSanPham.size',
        ])->findOrFail($id);

        return view('QuanLyDonHang.Details', compact('order'));
    }

    public function setTrangThai(Request $request, $id, OrderService $orderService)
    {
        $request->validate([
            'trangThaiMoi' => ['required', 'string', 'in:Đang xử lý,Đang giao hàng,Đã hoàn thành,Đã hủy'],
        ], [
            'trangThaiMoi.in' => 'Trạng thái đơn hàng không hợp lệ.',
        ]);

        $trangThaiMoi = $request->input('trangThaiMoi');

        try {
            $orderService->setTrangThai((int) $id, $trangThaiMoi);

            return redirect()->route('quanlydonhang.details', ['id' => $id])
                ->with('message', "Cập nhật trạng thái đơn hàng sang '{$trangThaiMoi}' thành công!");
        } catch (Exception $e) {
            return redirect()->route('quanlydonhang.details', ['id' => $id])
                ->with('error', 'Lỗi khi cập nhật trạng thái: '.$e->getMessage());
        }
    }
}
