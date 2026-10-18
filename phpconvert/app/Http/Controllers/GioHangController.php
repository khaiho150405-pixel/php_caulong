<?php

namespace App\Http\Controllers;

use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    public function getCartCount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $khachHang = $user->khachHang;
            if ($khachHang) {
                $count = GioHang::where('MaKhachHang', $khachHang->MaKhachHang)->sum('SoLuong');
                session(['GioHangCount' => (int)$count]);
                return response((string)(int)$count, 200)->header('Content-Type', 'text/plain');
            }
        }

        $count = session('GioHangCount', 0);
        return response((string)(int)$count, 200)->header('Content-Type', 'text/plain');
    }

    public function removeFromCart($chiTietId)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $khachHang = $user->khachHang;
            if ($khachHang) {
                GioHang::where('MaKhachHang', $khachHang->MaKhachHang)
                    ->where('MaChiTietSanPham', $chiTietId)
                    ->delete();

                $count = GioHang::where('MaKhachHang', $khachHang->MaKhachHang)->sum('SoLuong');
                session(['GioHangCount' => (int)$count]);
            }
        }

        return redirect()->route('home.viewcart');
    }
}
