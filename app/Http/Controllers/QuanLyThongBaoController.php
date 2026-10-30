<?php

namespace App\Http\Controllers;

use App\Models\PhanHoi;

class QuanLyThongBaoController extends Controller
{
    public function thongBao()
    {

        $data = PhanHoi::with(['khachHang', 'sanPham'])
            ->where('DanhGia', '<=', 2)
            ->orderByDesc('NgayPhanHoi')
            ->get();

        return view('QuanLyThongBao.ThongBao', compact('data'));
    }

    public function soThongBao()
    {
        $soThongBao = PhanHoi::where('DanhGia', '<=', 2)->count();

        return response((string) $soThongBao, 200)->header('Content-Type', 'text/plain');
    }
}
