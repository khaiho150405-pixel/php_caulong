<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TinTucRequest;
use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Quản trị bảng tin: CRUD bài đăng (xóa mềm), tìm kiếm + lọc loại + phân trang giữ từ khóa.
 */
class QuanLyBangTinController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $loai = array_key_exists((string) $request->query('loai'), TinTuc::LOAI) ? $request->query('loai') : null;

        $list = TinTuc::with(['sanPham', 'tranDau', 'tacGia'])
            ->loai($loai)
            ->timKiem($keyword)
            ->orderByDesc('NgayDang')
            ->orderByDesc('MaTin')
            ->paginate(10)
            ->withQueryString();

        return view('QuanLyBangTin.Index', compact('list', 'keyword', 'loai'));
    }

    public function create()
    {
        return view('QuanLyBangTin.Create', $this->duLieuForm());
    }

    public function createPost(TinTucRequest $request)
    {
        $data = $request->validated();
        $data['NgayDang'] = $data['NgayDang'] ?? now();
        $data['MaTaiKhoan'] = Auth::id();

        TinTuc::create($data);

        return redirect()->route('quanlybantin.index')->with('message', 'Đăng bài lên bảng tin thành công!');
    }

    public function edit($id)
    {
        $tin = TinTuc::findOrFail($id);

        return view('QuanLyBangTin.Edit', ['tin' => $tin] + $this->duLieuForm());
    }

    public function editPost(TinTucRequest $request, $id)
    {
        $tin = TinTuc::findOrFail($id);
        $data = $request->validated();
        $data['NgayDang'] = $data['NgayDang'] ?? $tin->NgayDang;

        $tin->update($data);

        return redirect()->route('quanlybantin.index')->with('message', 'Cập nhật bài viết thành công!');
    }

    public function delete($id)
    {
        $tin = TinTuc::with(['sanPham', 'tranDau'])->findOrFail($id);

        return view('QuanLyBangTin.Delete', compact('tin'));
    }

    public function deleteConfirmed($id)
    {
        TinTuc::findOrFail($id)->delete(); // xóa mềm

        return redirect()->route('quanlybantin.index')->with('message', 'Đã xóa bài viết khỏi bảng tin.');
    }

    private function duLieuForm(): array
    {
        return [
            'sanPhams' => SanPham::orderBy('TenSanPham')->get(['MaSanPham', 'TenSanPham']),
            'tranDaus' => TranDau::orderByDesc('NgayThiDau')->orderByDesc('MaTranDau')->take(200)->get(),
        ];
    }
}
