<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\TaiKhoanRequest;
use App\Models\KhachHang;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QuanLyTaiKhoanController extends Controller
{
    /**
     * Danh sách tài khoản: tìm theo tên đăng nhập / email / họ tên, lọc theo quyền, phân trang.
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $maQuyen = $request->query('maQuyen');

        $danhSach = TaiKhoan::with(['phanQuyen', 'khachHang'])
            ->when($keyword !== '', function ($q) use ($keyword) {
                $q->where(function ($q) use ($keyword) {
                    $q->where('TenDangNhap', 'LIKE', "%{$keyword}%")
                        ->orWhere('Email', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('khachHang', fn ($kh) => $kh->where('HoTen', 'LIKE', "%{$keyword}%"));
                });
            })
            ->when($maQuyen, fn ($q) => $q->where('MaQuyen', $maQuyen))
            ->orderBy('MaQuyen')
            ->orderBy('MaTaiKhoan')
            ->paginate(10)
            ->withQueryString();

        $phanQuyens = PhanQuyen::all();

        return view('QuanLyTaiKhoan.Index', compact('danhSach', 'keyword', 'maQuyen', 'phanQuyens'));
    }

    public function create()
    {
        $phanQuyens = PhanQuyen::all();

        return view('QuanLyTaiKhoan.Create', compact('phanQuyens'));
    }

    public function createPost(TaiKhoanRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $newTk = TaiKhoan::create([
                    'TenDangNhap' => $request->input('TenDangNhap'),
                    'MatKhau' => Hash::make($request->input('MatKhau')),
                    'Email' => $request->input('Email'),
                    'MaQuyen' => $request->input('MaQuyen'),
                    'NgayTao' => now(),
                ]);

                KhachHang::create([
                    'HoTen' => $request->input('HoTen'),
                    'Email' => $request->input('Email'),
                    'SoDienThoai' => $request->input('SoDienThoai', ''),
                    'DiaChi' => $request->input('DiaChi', ''),
                    'MaTaiKhoan' => $newTk->MaTaiKhoan,
                    'NgayTao' => now(),
                ]);
            });

            return redirect()->route('quanlytaikhoan.index')->with('message', 'Tạo tài khoản thành công!');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Lỗi khi tạo tài khoản: '.$e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $taiKhoan = TaiKhoan::with('khachHang')->findOrFail($id);
        $phanQuyens = PhanQuyen::all();

        return view('QuanLyTaiKhoan.Edit', compact('taiKhoan', 'phanQuyens'));
    }

    public function editPost(TaiKhoanRequest $request, $id)
    {
        $taiKhoan = TaiKhoan::with('khachHang')->findOrFail($id);

        // Không cho Admin tự hạ quyền của chính mình (tránh mất quyền quản trị)
        if ((int) $taiKhoan->MaTaiKhoan === (int) Auth::id() && (int) $request->input('MaQuyen') !== TaiKhoan::QUYEN_ADMIN) {
            return back()->withInput()->with('error', 'Bạn không thể tự thay đổi quyền Quản trị của chính mình.');
        }

        try {
            DB::transaction(function () use ($request, $taiKhoan) {
                $taiKhoan->Email = $request->input('Email');
                $taiKhoan->MaQuyen = $request->input('MaQuyen');
                if ($request->filled('MatKhau')) {
                    $taiKhoan->MatKhau = Hash::make($request->input('MatKhau'));
                }
                $taiKhoan->save();

                $taiKhoan->khachHang()->updateOrCreate(
                    ['MaTaiKhoan' => $taiKhoan->MaTaiKhoan],
                    [
                        'HoTen' => $request->input('HoTen'),
                        'Email' => $request->input('Email'),
                        'SoDienThoai' => $request->input('SoDienThoai', ''),
                        'DiaChi' => $request->input('DiaChi', ''),
                    ]
                );
            });

            return redirect()->route('quanlytaikhoan.index')->with('message', 'Cập nhật tài khoản thành công!');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Lỗi khi cập nhật tài khoản: '.$e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $taiKhoan = TaiKhoan::with(['phanQuyen', 'khachHang'])->findOrFail($id);

        return view('QuanLyTaiKhoan.Delete', compact('taiKhoan'));
    }

    public function deleteConfirmed($id)
    {
        $taiKhoan = TaiKhoan::findOrFail($id);

        if ((int) $taiKhoan->MaTaiKhoan === (int) Auth::id()) {
            return redirect()->route('quanlytaikhoan.index')->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập.');
        }

        $taiKhoan->delete();

        return redirect()->route('quanlytaikhoan.index')->with('message', 'Xóa tài khoản thành công!');
    }
}
