<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\GioHang;
use App\Services\OrderService;
use App\Services\Payment\MomoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class DonHangController extends Controller
{
    public function checkout()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $cartItems = GioHang::with(['chiTietSanPham.sanPham.khuyenMai', 'chiTietSanPham.mauSac', 'chiTietSanPham.size'])
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home.viewcart')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->chiTietSanPham->effective_price * $item->SoLuong;
        }

        return view('DonHang.Checkout', compact('khachHang', 'cartItems', 'total'));
    }

    public function checkoutPost(Request $request, OrderService $orderService)
    {
        $request->validate([
            'HoTenNhan' => 'required|string|max:100',
            'SoDienThoaiNhan' => 'required|string|max:15',
            'DiaChiGiao' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $cartItems = GioHang::with(['chiTietSanPham.sanPham.khuyenMai'])
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home.viewcart')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            $orderData = [
                'DiaChiGiaoHang' => $request->input('DiaChiGiao'),
                'SoDienThoaiNhanHang' => $request->input('SoDienThoaiNhan'),
                'GhiChu' => $request->input('GhiChu', ''),
            ];

            $order = $orderService->createOrder($khachHang->MaKhachHang, $orderData, $cartItems, 'Đang xử lý');
            session(['GioHangCount' => 0]);

            return redirect()->route('donhang.index')->with('message', "Đặt hàng thành công! Đơn hàng #{$order->MaDonHang}");
        } catch (Exception $e) {
            return redirect()->route('home.viewcart')->with('error', 'Lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }

    public function paymentMoMo(Request $request, OrderService $orderService, MomoPaymentService $momoService)
    {
        $request->validate([
            'HoTenNhan' => 'required|string|max:100',
            'SoDienThoaiNhan' => 'required|string|max:15',
            'DiaChiGiao' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $cartItems = GioHang::with(['chiTietSanPham.sanPham.khuyenMai'])
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('home.viewcart')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        try {
            $orderData = [
                'DiaChiGiaoHang' => $request->input('DiaChiGiao'),
                'SoDienThoaiNhanHang' => $request->input('SoDienThoaiNhan'),
                'GhiChu' => $request->input('GhiChu', '') . ' (Thanh toán qua MoMo)',
            ];

            // Tạo đơn hàng tạm thời ở trạng thái Chờ thanh toán MoMo
            $order = $orderService->createOrder($khachHang->MaKhachHang, $orderData, $cartItems, 'Chờ thanh toán MoMo');

            // Tạo link thanh toán MoMo
            $payUrl = $momoService->createPayment($order->MaDonHang, (float)$order->TongTien, "Thanh toan don hang #{$order->MaDonHang}");

            return redirect($payUrl);
        } catch (Exception $e) {
            return redirect()->route('donhang.checkout')->with('error', 'Lỗi cổng MoMo: ' . $e->getMessage());
        }
    }

    public function thanhToanMomoSuccess(Request $request)
    {
        $errorCode = $request->query('errorCode');
        $orderId = (int)$request->query('orderId');
        $message = $request->query('message', '');

        $order = DonHang::find($orderId);
        $isSuccess = false;

        if ($errorCode === '0' || $errorCode === 0) {
            if ($order) {
                $order->TrangThai = 'Đã thanh toán bằng MoMo';
                $order->save();
                if ($order->MaKhachHang) {
                    GioHang::where('MaKhachHang', $order->MaKhachHang)->delete();
                }
                session(['GioHangCount' => 0]);
                $isSuccess = true;
            }
        } else {
            if ($order) {
                $order->TrangThai = 'Đã hủy (Thanh toán thất bại)';
                $order->save();
            }
        }

        return view('DonHang.ThanhToanMomoSuccess', [
            'isSuccess' => $isSuccess,
            'maDonHang' => $orderId,
            'message' => $isSuccess ? 'Giao dịch MoMo thành công!' : 'Giao dịch MoMo thất bại hoặc đã bị hủy.',
            'description' => $message,
        ]);
    }

    public function savePayment(Request $request)
    {
        // IPN Webhook callback từ MoMo
        $orderId = (int)$request->input('orderId');
        $resultCode = $request->input('resultCode');

        if ($orderId && ($resultCode == 0 || $resultCode == '0')) {
            $order = DonHang::find($orderId);
            if ($order) {
                $order->TrangThai = 'Đã thanh toán bằng MoMo';
                $order->save();
            }
        }

        return response()->json(['status' => 'success'], 204);
    }

    public function index()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $orders = DonHang::where('MaKhachHang', $khachHang->MaKhachHang)
            ->with(['chiTietDonHangs.chiTietSanPham.sanPham'])
            ->orderByDesc('NgayDat')
            ->get();

        return view('DonHang.Index', compact('orders'));
    }

    public function details($id)
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $order = DonHang::where('MaDonHang', $id)
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->with([
                'chiTietDonHangs.chiTietSanPham.sanPham',
                'chiTietDonHangs.chiTietSanPham.mauSac',
                'chiTietDonHangs.chiTietSanPham.size',
            ])
            ->firstOrFail();

        return view('DonHang.Details', compact('order'));
    }

    public function huyDonHang($id, OrderService $orderService)
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('taikhoan.dangnhap');
        }

        try {
            $orderService->cancelOrder((int)$id, $khachHang->MaKhachHang, false);
            return back()->with('message', "Đã hủy đơn hàng #{$id}");
        } catch (Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
