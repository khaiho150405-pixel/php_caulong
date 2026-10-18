<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanLyKhuyenMaiController extends Controller
{
    public function index()
    {
        $list = KhuyenMai::orderByDesc('NgayBatDau')->get();
        return view('QuanLyKhuyenMai.Index', compact('list'));
    }

    public function create()
    {
        return view('QuanLyKhuyenMai.Create');
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'TenChuongTrinh' => 'required|string|max:100',
            'PhanTramGiam' => 'required|numeric|min:0|max:100',
            'NgayBatDau' => 'required|date',
            'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
        ]);

        KhuyenMai::create($request->all());

        return redirect()->route('quanlykhuyenmai.index')->with('message', 'Tạo khuyến mãi thành công!');
    }

    public function edit($id)
    {
        $km = KhuyenMai::findOrFail($id);
        return view('QuanLyKhuyenMai.Edit', compact('km'));
    }

    public function editPost(Request $request, $id)
    {
        $km = KhuyenMai::findOrFail($id);

        $request->validate([
            'TenChuongTrinh' => 'required|string|max:100',
            'PhanTramGiam' => 'required|numeric|min:0|max:100',
            'NgayBatDau' => 'required|date',
            'NgayKetThuc' => 'required|date|after_or_equal:NgayBatDau',
        ]);

        $km->update($request->all());

        return redirect()->route('quanlykhuyenmai.index')->with('message', 'Cập nhật khuyến mãi thành công!');
    }

    public function delete($id)
    {
        $km = KhuyenMai::findOrFail($id);
        return view('QuanLyKhuyenMai.Delete', compact('km'));
    }

    public function deleteConfirmed($id)
    {
        $km = KhuyenMai::findOrFail($id);

        DB::transaction(function () use ($id, $km) {
            // Gỡ tham chiếu khuyến mãi từ các sản phẩm đang áp dụng
            SanPham::where('MaKhuyenMai', $id)->update(['MaKhuyenMai' => null]);
            $km->delete();
        });

        return redirect()->route('quanlykhuyenmai.index')->with('message', 'Xóa chương trình khuyến mãi thành công!');
    }
}
