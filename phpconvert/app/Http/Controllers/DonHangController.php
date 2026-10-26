<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Services\OrderService;
use App\Services\Payment\MomoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

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

    public function checkoutPost(CheckoutRequest $request, OrderService $orderService)
    {

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

    public function paymentMoMo(CheckoutRequest $request, OrderService $orderService, MomoPaymentService $momoService)
    {

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
            // Giỏ hàng chỉ bị xóa khi MoMo báo thanh toán thành công
            $order = $orderService->createOrder($khachHang->MaKhachHang, $orderData, $cartItems, 'Chờ thanh toán MoMo', false);

            // Tạo link thanh toán MoMo
            $payUrl = $momoService->createPayment($order->MaDonHang, (float)$order->TongTien, "Thanh toan don hang #{$order->MaDonHang}");

            return redirect()->away($payUrl);
        } catch (Exception $e) {
            if (isset($order)) {
                // Không tạo được link thanh toán -> hủy đơn tạm và hoàn kho
                $orderService->huyDoThanhToanThatBai($order->MaDonHang);
            }
            return redirect()->route('donhang.checkout')->with('error', 'Lỗi cổng MoMo: ' . $e->getMessage());
        }
    }

    /**
     * MoMo chuyển hướng người dùng về (redirectUrl). Chuẩn MoMo v2 trả về "resultCode" và "signature".
     * Chỉ cập nhật đơn khi chữ ký hợp lệ để tránh giả mạo URL.
     */
    public function thanhToanMomoSuccess(Request $request, MomoPaymentService $momoService, OrderService $orderService)
    {
        $data = $request->query();
        $orderId = (int) $request->query('orderId');
        $resultCode = (string) $request->query('resultCode', $request->query('errorCode', '-1'));
        $order = DonHang::find($orderId);

        if (!$order || !$this->chuKyMomoHopLe($momoService, $data)) {
            return view('DonHang.ThanhToanMomoSuccess', [
                'isSuccess' => false,
                'maDonHang' => $orderId,
                'message' => 'Không xác thực được kết quả thanh toán từ MoMo.',
                'description' => 'Chữ ký giao dịch không hợp lệ hoặc đơn hàng không tồn tại.',
            ]);
        }

        $isSuccess = $resultCode === '0';
        $this->capNhatKetQuaMomo($order, $isSuccess, $orderService);

        return view('DonHang.ThanhToanMomoSuccess', [
            'isSuccess' => $isSuccess,
            'maDonHang' => $orderId,
            'message' => $isSuccess ? 'Giao dịch MoMo thành công!' : 'Giao dịch MoMo thất bại hoặc đã bị hủy.',
            'description' => (string) $request->query('message', ''),
        ]);
    }

    /**
     * IPN (MoMo gọi server-to-server). Phản hồi HTTP 204 theo tài liệu MoMo.
     */
    public function savePayment(Request $request, MomoPaymentService $momoService, OrderService $orderService)
    {
        $data = $request->all();
        $order = DonHang::find((int) $request->input('orderId'));

        if ($order && $this->chuKyMomoHopLe($momoService, $data)) {
            $this->capNhatKetQuaMomo($order, (string) $request->input('resultCode') === '0', $orderService);
        } else {
            Log::warning('MoMo IPN không hợp lệ', ['orderId' => $request->input('orderId')]);
        }

        return response()->noContent();
    }

    private function chuKyMomoHopLe(MomoPaymentService $momoService, array $data): bool
    {
        $batBuoc = ['amount', 'extraData', 'message', 'orderId', 'orderInfo', 'orderType', 'partnerCode',
            'payType', 'requestId', 'responseTime', 'resultCode', 'transId', 'signature'];
        foreach ($batBuoc as $key) {
            if (!array_key_exists($key, $data)) {
                return false;
            }
        }

        return $momoService->verifySignature($data);
    }

    private function capNhatKetQuaMomo(DonHang $order, bool $isSuccess, OrderService $orderService): void
    {
        // Chỉ xử lý đơn đang chờ thanh toán (tránh cập nhật lặp khi cả Return URL và IPN cùng gọi)
        if ($order->TrangThai !== 'Chờ thanh toán MoMo') {
            return;
        }

        if ($isSuccess) {
            $order->TrangThai = 'Đã thanh toán bằng MoMo';
            $order->save();
            if ($order->MaKhachHang) {
                GioHang::where('MaKhachHang', $order->MaKhachHang)->delete();
                if (Auth::check()) {
                    session(['GioHangCount' => 0]);
                }
            }
        } else {
            $orderService->huyDoThanhToanThatBai($order->MaDonHang);
        }
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
