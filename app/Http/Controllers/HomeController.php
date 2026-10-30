<?php

namespace App\Http\Controllers;

use App\Models\ChiTietSanPham;
use App\Models\GioHang;
use App\Models\PhanHoi;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        $sanPhams = SanPham::with(['loaiSanPham', 'hang', 'khuyenMai'])
            ->orderByDesc('NgayTao')
            ->get();

        return view('Home.Index', compact('sanPhams'));
    }

    public function contact()
    {
        return view('Home.Contact');
    }

    public function chiTietSanPham($id)
    {
        $sanPham = SanPham::with(['hang', 'khuyenMai', 'loaiSanPham'])
            ->where('MaSanPham', $id)
            ->firstOrFail();

        $variants = ChiTietSanPham::where('MaSanPham', $id)
            ->with(['mauSac', 'size', 'thongSoVots'])
            ->get();

        $reviews = PhanHoi::where('MaSanPham', $id)
            ->with('khachHang')
            ->orderByDesc('NgayPhanHoi')
            ->get();

        $availableColors = $variants->whereNotNull('mauSac')->map->mauSac->unique('MaMau')->values();
        $availableSizes = $variants->whereNotNull('size')->map->size->unique('MaSize')->values();

        $thongSoVot = $variants->flatMap->thongSoVots->first();

        $averageRating = $sanPham->trung_binh_danh_gia;
        $reviewCount = $reviews->count();
        $giaBanThucTe = $sanPham->gia_sau_khuyen_mai;

        return view('Home.ChiTietSanPham', compact(
            'sanPham',
            'variants',
            'reviews',
            'availableColors',
            'availableSizes',
            'thongSoVot',
            'averageRating',
            'reviewCount',
            'giaBanThucTe'
        ));
    }

    public function addToCart(Request $request)
    {
        if (! Auth::check()) {
            return redirect()->route('taikhoan.dangnhap', ['returnUrl' => url()->previous()]);
        }

        $validator = Validator::make($request->all(), [
            'chiTietId' => ['required', 'integer', 'exists:ChiTietSanPham,MaChiTiet'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'chiTietId.required' => 'Vui lòng chọn phiên bản (màu/size) sản phẩm.',
            'chiTietId.exists' => 'Sản phẩm biến thể không tồn tại.',
            'quantity.min' => 'Số lượng phải lớn hơn 0.',
            'quantity.max' => 'Mỗi lần chỉ được thêm tối đa 100 sản phẩm.',
        ]);
        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first());
        }

        $chiTietId = (int) $request->input('chiTietId');
        $quantity = (int) $request->input('quantity', 1);

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (! $khachHang) {
            return back()->with('error', 'Không tìm thấy thông tin khách hàng của tài khoản.');
        }

        $productVariant = ChiTietSanPham::with('sanPham')->find($chiTietId);
        if (! $productVariant || ! $productVariant->sanPham || $productVariant->sanPham->trashed()) {
            return back()->with('error', 'Sản phẩm không còn kinh doanh.');
        }

        $existingCartItem = GioHang::where('MaKhachHang', $khachHang->MaKhachHang)
            ->where('MaChiTietSanPham', $chiTietId)
            ->first();

        $soLuongMoi = $existingCartItem ? ($existingCartItem->SoLuong + $quantity) : $quantity;

        if (($productVariant->SoLuongTon ?? 0) < $soLuongMoi) {
            return back()->with('error', 'Số lượng sản phẩm trong giỏ vượt quá tồn kho!');
        }

        if ($existingCartItem) {
            $existingCartItem->SoLuong = $soLuongMoi;
            $existingCartItem->NgayThem = now();
            $existingCartItem->save();
        } else {
            GioHang::create([
                'MaKhachHang' => $khachHang->MaKhachHang,
                'MaChiTietSanPham' => $chiTietId,
                'SoLuong' => $soLuongMoi,
                'NgayThem' => now(),
            ]);
        }

        $this->updateCartCountInSession($khachHang->MaKhachHang);

        return redirect()->route('home.viewcart')->with('message', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
    }

    public function viewCart()
    {
        if (! Auth::check()) {
            return redirect()->route('taikhoan.dangnhap', ['returnUrl' => route('home.viewcart')]);
        }

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (! $khachHang) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $cartItems = GioHang::with(['chiTietSanPham.sanPham.khuyenMai', 'chiTietSanPham.mauSac', 'chiTietSanPham.size'])
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->orderByDesc('NgayThem')
            ->get();

        return view('Home.ViewCart', compact('cartItems'));
    }

    public function updateCartQuantity(Request $request, $maGioHang)
    {
        if (! Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập.'], 401);
            }

            return redirect()->route('taikhoan.dangnhap', ['returnUrl' => route('home.viewcart')]);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng sản phẩm trong giỏ hàng phải lớn hơn 0.',
            'quantity.max' => 'Mỗi sản phẩm chỉ được đặt tối đa 100.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }

            return back()->with('error', $validator->errors()->first());
        }

        $quantity = (int) $request->input('quantity');
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (! $khachHang) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin khách hàng.'], 404);
            }

            return back()->with('error', 'Không tìm thấy thông tin khách hàng.');
        }

        $cartItem = GioHang::with(['chiTietSanPham.sanPham'])
            ->where('MaGioHang', $maGioHang)
            ->where('MaKhachHang', $khachHang->MaKhachHang)
            ->first();

        if (! $cartItem) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng.'], 404);
            }

            return back()->with('error', 'Sản phẩm không có trong giỏ hàng.');
        }

        $productVariant = $cartItem->chiTietSanPham;
        if (! $productVariant || ! $productVariant->sanPham || $productVariant->sanPham->trashed()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không còn kinh doanh.'], 400);
            }

            return back()->with('error', 'Sản phẩm không còn kinh doanh.');
        }

        $tonKho = (int) ($productVariant->SoLuongTon ?? 0);
        if ($quantity > $tonKho) {
            $msg = "Số lượng trong kho chỉ còn {$tonKho} sản phẩm.";
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg, 'maxStock' => $tonKho], 422);
            }

            return back()->with('error', $msg);
        }

        $cartItem->SoLuong = $quantity;
        $cartItem->save();

        $this->updateCartCountInSession($khachHang->MaKhachHang);

        if ($request->expectsJson() || $request->ajax()) {
            $allCartItems = GioHang::with(['chiTietSanPham.sanPham'])
                ->where('MaKhachHang', $khachHang->MaKhachHang)
                ->get();

            $giaBan = (float) ($productVariant->GiaBan ?? $productVariant->sanPham->GiaGoc ?? 0);
            $itemTotal = $giaBan * $quantity;

            $cartTotal = $allCartItems->sum(function ($it) {
                $p = (float) ($it->chiTietSanPham->GiaBan ?? $it->chiTietSanPham->sanPham->GiaGoc ?? 0);

                return $p * (int) $it->SoLuong;
            });

            $cartCount = (int) $allCartItems->sum('SoLuong');

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật số lượng.',
                'quantity' => $quantity,
                'itemTotal' => $itemTotal,
                'itemTotalFormatted' => number_format($itemTotal, 0, ',', '.').' đ',
                'cartTotal' => $cartTotal,
                'cartTotalFormatted' => number_format($cartTotal, 0, ',', '.').' đ',
                'cartCount' => $cartCount,
            ]);
        }

        return redirect()->route('home.viewcart')->with('message', 'Đã cập nhật số lượng sản phẩm.');
    }

    public function removeFromCart($maGioHang)
    {
        if (! Auth::check()) {
            return redirect()->route('taikhoan.dangnhap');
        }

        $user = Auth::user();
        $khachHang = $user->khachHang;

        if ($khachHang) {
            $cartItem = GioHang::where('MaGioHang', $maGioHang)
                ->where('MaKhachHang', $khachHang->MaKhachHang)
                ->first();

            if ($cartItem) {
                $cartItem->delete();
                $this->updateCartCountInSession($khachHang->MaKhachHang);
            }
        }

        return redirect()->route('home.viewcart')->with('message', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    private function updateCartCountInSession(int $maKhachHang)
    {
        $totalItems = GioHang::where('MaKhachHang', $maKhachHang)->sum('SoLuong');
        session(['GioHangCount' => (int) $totalItems]);
    }
}
