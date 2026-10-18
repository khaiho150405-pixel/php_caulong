<?php

namespace App\Http\Controllers;

use App\Models\PhanHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemPhanHoiSPController extends Controller
{
    public function reviewForm($maSP)
    {
        if (!Auth::check()) {
            return redirect()->route('home.chitietsanpham', ['id' => $maSP])
                ->with('error', 'Bạn cần đăng nhập để gửi phản hồi!');
        }

        return view('ThemPhanHoiSP.ReviewForm', ['maSP' => $maSP]);
    }

    public function submitReview(Request $request)
    {
        if (!Auth::check()) {
            $maSP = $request->input('maSP');
            return redirect()->route('home.chitietsanpham', ['id' => $maSP])
                ->with('error', 'Bạn cần đăng nhập để gửi phản hồi!');
        }

        $user = Auth::user();
        $khachHang = $user->khachHang;
        $maSP = (int)$request->input('maSP');

        if (!$khachHang) {
            return redirect()->route('home.chitietsanpham', ['id' => $maSP])
                ->with('error', 'Tài khoản của bạn không phải là khách hàng và không thể gửi đánh giá.');
        }

        $danhGia = min(5, max(1, (int)$request->input('danhGia', 5)));
        $noiDung = $request->input('noiDung');

        PhanHoi::create([
            'MaKhachHang' => $khachHang->MaKhachHang,
            'MaSanPham' => $maSP,
            'DanhGia' => $danhGia,
            'NoiDung' => $noiDung,
            'NgayPhanHoi' => now(),
        ]);

        return redirect()->route('home.chitietsanpham', ['id' => $maSP])
            ->with('message', 'Gửi đánh giá thành công!');
    }
}
