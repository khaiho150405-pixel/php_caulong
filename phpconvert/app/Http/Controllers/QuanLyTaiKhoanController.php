<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\KhachHang;
use App\Models\PhanQuyen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class QuanLyTaiKhoanController extends Controller
{
    public function index()
    {
        $danhSach = TaiKhoan::with(['phanQuyen', 'khachHang'])
            ->orderBy('MaQuyen')
            ->get();

        return view('QuanLyTaiKhoan.Index', compact('danhSach'));
    }

    public function create()
    {
        $phanQuyens = PhanQuyen::all();
        return view('QuanLyTaiKhoan.Create', compact('phanQuyens'));
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string|max:50|unique:TaiKhoan,TenDangNhap',
            'MatKhau' => 'required|string|min:4',
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email|unique:KhachHang,Email',
            'MaQuyen' => 'required|integer',
            'HoTen' => 'required|string|max:100',
        ]);

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
            return back()->withInput()->withErrors(['error' => 'Lỗi khi tạo tài khoản: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $taiKhoan = TaiKhoan::with('khachHang')->findOrFail($id);
        $phanQuyens = PhanQuyen::all();
        return view('QuanLyTaiKhoan.Edit', compact('taiKhoan', 'phanQuyens'));
    }

    public function editPost(Request $request, $id)
    {
        $taiKhoan = TaiKhoan::with('khachHang')->findOrFail($id);

        $request->validate([
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email,' . $id . ',MaTaiKhoan',
            'MaQuyen' => 'required|integer',
            'HoTen' => 'required|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($request, $taiKhoan) {
                $taiKhoan->Email = $request->input('Email');
                $taiKhoan->MaQuyen = $request->input('MaQuyen');
                if ($request->filled('MatKhau')) {
                    $taiKhoan->MatKhau = Hash::make($request->input('MatKhau'));
                }
                $taiKhoan->save();

                if ($taiKhoan->khachHang) {
                    $taiKhoan->khachHang->update([
                        'HoTen' => $request->input('HoTen'),
                        'Email' => $request->input('Email'),
                        'SoDienThoai' => $request->input('SoDienThoai', ''),
                        'DiaChi' => $request->input('DiaChi', ''),
                    ]);
                }
            });

            return redirect()->route('quanlytaikhoan.index')->with('message', 'Cập nhật tài khoản thành công!');
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Lỗi khi cập nhật tài khoản: ' . $e->getMessage()]);
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
        $taiKhoan->delete();
        return redirect()->route('quanlytaikhoan.index')->with('message', 'Xóa tài khoản thành công!');
    }
}
