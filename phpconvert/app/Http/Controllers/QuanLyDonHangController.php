<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Exception;

class QuanLyDonHangController extends Controller
{
    public function index()
    {
        $orders = DonHang::with('khachHang')
            ->orderByDesc('NgayDat')
            ->get();

        return view('QuanLyDonHang.Index', compact('orders'));
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
        $trangThaiMoi = $request->input('trangThaiMoi');

        try {
            $orderService->setTrangThai((int)$id, $trangThaiMoi);
            return redirect()->route('quanlydonhang.details', ['id' => $id])
                ->with('message', "Cập nhật trạng thái đơn hàng sang '{$trangThaiMoi}' thành công!");
        } catch (Exception $e) {
            return redirect()->route('quanlydonhang.details', ['id' => $id])
                ->with('error', 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }
}
