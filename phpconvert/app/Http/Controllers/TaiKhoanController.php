<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\KhachHang;
use App\Models\GioHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Exception;

class TaiKhoanController extends Controller
{
    public function dangNhap(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        $returnUrl = $request->query('returnUrl');
        return view('TaiKhoan.DangNhap', compact('returnUrl'));
    }

    public function dangNhapPost(Request $request)
    {
        $request->validate([
            'TenDangNhap' => 'required|string',
            'MatKhau' => 'required|string',
        ], [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $username = trim($request->input('TenDangNhap'));
        $password = $request->input('MatKhau');
        $returnUrl = $request->input('returnUrl');

        $user = TaiKhoan::with('khachHang')->where('TenDangNhap', $username)->first();

        if ($user) {
            $isPasswordValid = false;

            // 1. Kiểm tra bằng Bcrypt
            if (Hash::check($password, $user->MatKhau)) {
                $isPasswordValid = true;
            }
            // 2. Hỗ trợ tương thích ngược mật khẩu cũ chưa băm
            elseif ($user->MatKhau === $password) {
                $isPasswordValid = true;
                // Nâng cấp tự động sang Bcrypt
                $user->MatKhau = Hash::make($password);
                $user->save();
            }

            if ($isPasswordValid) {
                Auth::login($user);

                // Đồng bộ session giống C# để View tương thích 100%
                session([
                    'MaTaiKhoan' => $user->MaTaiKhoan,
                    'TenDangNhap' => $user->TenDangNhap,
                    'MaQuyen' => (int)$user->MaQuyen,
                    'HoTen' => $user->khachHang ? $user->khachHang->HoTen : $user->TenDangNhap,
                ]);

                // Đếm số lượng giỏ hàng
                $cartCount = 0;
                if ($user->khachHang) {
                    $cartCount = GioHang::where('MaKhachHang', $user->khachHang->MaKhachHang)->sum('SoLuong');
                }
                session(['GioHangCount' => (int)$cartCount]);

                if (!empty($returnUrl)) {
                    if (str_starts_with($returnUrl, '/') || str_starts_with($returnUrl, url('/'))) {
                        return redirect($returnUrl);
                    }
                }

                if ($user->hasAdminAccess()) {
                    return redirect()->route('quanlysanpham.index');
                }

                return redirect()->route('home');
            }
        }

        return back()->withInput()->withErrors([
            'login' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ]);
    }

    public function dangKy()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('TaiKhoan.DangKy');
    }

    public function dangKyPost(Request $request)
    {
        $request->validate([
            'HoTen' => 'required|string|max:100',
            'TenDangNhap' => 'required|string|max:50|unique:TaiKhoan,TenDangNhap',
            'Email' => 'required|email|max:100|unique:TaiKhoan,Email|unique:KhachHang,Email',
            'MatKhau' => 'required|string|min:4',
            'XacNhanMatKhau' => 'required|same:MatKhau',
        ], [
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'Email.unique' => 'Email đã được sử dụng.',
            'XacNhanMatKhau.same' => 'Xác nhận mật khẩu không khớp.',
        ]);

        try {
            DB::transaction(function () use ($request, &$newUser, &$newKhachHang) {
                $newUser = TaiKhoan::create([
                    'TenDangNhap' => $request->input('TenDangNhap'),
                    'MatKhau' => Hash::make($request->input('MatKhau')),
                    'Email' => $request->input('Email'),
                    'MaQuyen' => 3, // Khách hàng
                    'NgayTao' => now(),
                ]);

                $newKhachHang = KhachHang::create([
                    'HoTen' => $request->input('HoTen'),
                    'Email' => $request->input('Email'),
                    'SoDienThoai' => $request->input('SoDienThoai', ''),
                    'DiaChi' => $request->input('DiaChi', ''),
                    'MaTaiKhoan' => $newUser->MaTaiKhoan,
                    'NgayTao' => now(),
                ]);
            });

            Auth::login($newUser);

            session([
                'MaTaiKhoan' => $newUser->MaTaiKhoan,
                'TenDangNhap' => $newUser->TenDangNhap,
                'MaQuyen' => 3,
                'HoTen' => $newKhachHang->HoTen,
                'GioHangCount' => 0,
            ]);

            return redirect()->route('home')->with('message', 'Đăng ký tài khoản thành công!');
        } catch (Exception $e) {
            return back()->withInput()->withErrors([
                'register' => 'Lỗi khi tạo tài khoản: ' . $e->getMessage(),
            ]);
        }
    }

    public function dangXuat()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('home');
    }

    public function doiMatKhau()
    {
        return view('TaiKhoan.DoiMatKhau');
    }

    public function doiMatKhauPost(Request $request)
    {
        $request->validate([
            'matKhauCu' => 'required|string',
            'matKhauMoi' => 'required|string|min:4',
            'xacNhanMoi' => 'required|same:matKhauMoi',
        ], [
            'xacNhanMoi.same' => 'Mật khẩu mới không khớp.',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu cũ
        $isOldValid = Hash::check($request->input('matKhauCu'), $user->MatKhau) || ($user->MatKhau === $request->input('matKhauCu'));

        if (!$isOldValid) {
            return back()->withErrors(['matKhauCu' => 'Mật khẩu cũ không chính xác.']);
        }

        $user->MatKhau = Hash::make($request->input('matKhauMoi'));
        $user->save();

        return redirect()->route('home')->with('message', 'Đổi mật khẩu thành công!');
    }

    public function thongTin()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (!$khachHang) {
            return redirect()->route('home')->with('error', 'Tài khoản của bạn là quản trị, không có thông tin khách hàng.');
        }

        return view('TaiKhoan.ThongTin', compact('khachHang'));
    }
}
