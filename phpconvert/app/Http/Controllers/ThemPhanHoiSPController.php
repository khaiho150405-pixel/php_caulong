<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhanHoiRequest;
use App\Models\PhanHoi;
use App\Models\SanPham;
use Illuminate\Support\Facades\Auth;

class ThemPhanHoiSPController extends Controller
{
    public function reviewForm($maSP)
    {
        SanPham::findOrFail($maSP);

        return view('ThemPhanHoiSP.ReviewForm', ['maSP' => $maSP]);
    }

    public function submitReview(PhanHoiRequest $request)
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;
        $maSP = (int)$request->input('maSP');

        if (!$khachHang) {
            return redirect()->route('home.chitietsanpham', ['id' => $maSP])
                ->with('error', 'Tài khoản của bạn không phải là khách hàng và không thể gửi đánh giá.');
        }

        $danhGia = (int) $request->validated('danhGia');
        $noiDung = $request->validated('noiDung');

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
