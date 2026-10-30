<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\KhuyenMaiRequest;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuanLyKhuyenMaiController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));

        $list = KhuyenMai::withCount('sanPhams')
            ->when($keyword !== '', fn ($q) => $q->where('TenChuongTrinh', 'LIKE', "%{$keyword}%"))
            ->orderByDesc('NgayBatDau')
            ->paginate(10)
            ->withQueryString();

        return view('QuanLyKhuyenMai.Index', compact('list', 'keyword'));
    }

    public function create()
    {
        return view('QuanLyKhuyenMai.Create');
    }

    public function createPost(KhuyenMaiRequest $request)
    {
        KhuyenMai::create($request->validated());

        return redirect()->route('quanlykhuyenmai.index')->with('message', 'Tạo khuyến mãi thành công!');
    }

    public function edit($id)
    {
        $km = KhuyenMai::findOrFail($id);

        return view('QuanLyKhuyenMai.Edit', compact('km'));
    }

    public function editPost(KhuyenMaiRequest $request, $id)
    {
        $km = KhuyenMai::findOrFail($id);
        $km->update($request->validated());

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

            SanPham::where('MaKhuyenMai', $id)->update(['MaKhuyenMai' => null]);
            $km->delete();
        });

        return redirect()->route('quanlykhuyenmai.index')->with('message', 'Xóa chương trình khuyến mãi thành công!');
    }
}
