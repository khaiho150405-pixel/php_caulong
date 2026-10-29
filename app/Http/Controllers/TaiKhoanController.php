<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\DangKyRequest;
use App\Http\Requests\Auth\DangNhapRequest;
use App\Http\Requests\Auth\DoiMatKhauRequest;
use App\Models\GioHang;
use App\Models\KhachHang;
use App\Models\TaiKhoan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    public function dangNhapPost(DangNhapRequest $request)
    {
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
            // 2. Kiểm tra mật khẩu plain-text và tự động mã hóa Bcrypt
            elseif ($user->MatKhau === $password) {
                $isPasswordValid = true;
                $user->MatKhau = Hash::make($password);
                $user->save();
            }

            if ($isPasswordValid) {
                // Chặn tài khoản Quản trị viên đăng nhập ở đường link chung, bắt buộc qua /admin
                if ($user->isAdmin()) {
                    return back()->withInput($request->only('TenDangNhap'))->withErrors([
                        'login' => 'Tài khoản Quản trị viên vui lòng đăng nhập tại trang quản trị (/admin).',
                    ]);
                }

                Auth::login($user);
                // Chống tấn công Session Fixation
                $request->session()->regenerate();

                // Lưu thông tin người dùng vào session
                session([
                    'MaTaiKhoan' => $user->MaTaiKhoan,
                    'TenDangNhap' => $user->TenDangNhap,
                    'MaQuyen' => (int) $user->MaQuyen,
                    'HoTen' => $user->khachHang ? $user->khachHang->HoTen : $user->TenDangNhap,
                ]);

                // Đếm số lượng giỏ hàng
                $cartCount = 0;
                if ($user->khachHang) {
                    $cartCount = GioHang::where('MaKhachHang', $user->khachHang->MaKhachHang)->sum('SoLuong');
                }
                session(['GioHangCount' => (int) $cartCount]);

                if ($user->isAdmin()) {
                    return redirect()->route('quanlysanpham.dashboard');
                }

                // Chỉ chuyển hướng về URL nội bộ (chặn open-redirect kiểu //trang-la.com)
                if (! empty($returnUrl)) {
                    $laUrlNoiBo = (str_starts_with($returnUrl, '/') && ! str_starts_with($returnUrl, '//'))
                        || str_starts_with($returnUrl, url('/').'/');
                    if ($laUrlNoiBo) {
                        return redirect($returnUrl);
                    }
                }

                // Admin / Nhân viên vào thẳng khu vực quản trị /admin
                if ($user->hasAdminAccess()) {
                    return redirect()->route('quanlysanpham.dashboard');
                }

                return redirect()->route('home');
            }
        }

        return back()->withInput()->withErrors([
            'login' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ]);
    }

    public function adminDangNhapPost(DangNhapRequest $request)
    {
        $username = trim($request->input('TenDangNhap'));
        $password = $request->input('MatKhau');
        $returnUrl = $request->input('returnUrl');

        $user = TaiKhoan::with('khachHang')->where('TenDangNhap', $username)->first();

        if ($user) {
            $isPasswordValid = false;

            if (Hash::check($password, $user->MatKhau)) {
                $isPasswordValid = true;
            } elseif ($user->MatKhau === $password) {
                $isPasswordValid = true;
                $user->MatKhau = Hash::make($password);
                $user->save();
            }

            if ($isPasswordValid) {
                if (! $user->hasAdminAccess()) {
                    return back()->withInput()->withErrors([
                        'login' => 'Tài khoản không có quyền truy cập vào trang quản trị.',
                    ]);
                }

                Auth::login($user);
                $request->session()->regenerate();

                session([
                    'MaTaiKhoan' => $user->MaTaiKhoan,
                    'TenDangNhap' => $user->TenDangNhap,
                    'MaQuyen' => (int) $user->MaQuyen,
                    'HoTen' => $user->khachHang ? $user->khachHang->HoTen : $user->TenDangNhap,
                ]);

                if (! empty($returnUrl)) {
                    $laUrlNoiBo = (str_starts_with($returnUrl, '/') && ! str_starts_with($returnUrl, '//'))
                        || str_starts_with($returnUrl, url('/').'/');
                    if ($laUrlNoiBo && ! str_contains($returnUrl, '/TaiKhoan/')) {
                        return redirect($returnUrl);
                    }
                }

                return redirect()->route('quanlysanpham.dashboard');
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

    public function dangKyPost(DangKyRequest $request)
    {
        try {
            DB::transaction(function () use ($request, &$newUser, &$newKhachHang) {
                $newUser = TaiKhoan::create([
                    'TenDangNhap' => $request->input('TenDangNhap'),
                    'MatKhau' => Hash::make($request->input('MatKhau')),
                    'Email' => $request->input('Email'),
                    'MaQuyen' => TaiKhoan::QUYEN_KHACHHANG,
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
            $request->session()->regenerate();

            session([
                'MaTaiKhoan' => $newUser->MaTaiKhoan,
                'TenDangNhap' => $newUser->TenDangNhap,
                'MaQuyen' => TaiKhoan::QUYEN_KHACHHANG,
                'HoTen' => $newKhachHang->HoTen,
                'GioHangCount' => 0,
            ]);

            return redirect()->route('home')->with('message', 'Đăng ký tài khoản thành công!');
        } catch (Exception $e) {
            return back()->withInput()->withErrors([
                'register' => 'Lỗi khi tạo tài khoản: '.$e->getMessage(),
            ]);
        }
    }

    public function dangXuat(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('message', 'Bạn đã đăng xuất.');
    }

    public function doiMatKhau()
    {
        return view('TaiKhoan.DoiMatKhau');
    }

    public function doiMatKhauPost(DoiMatKhauRequest $request)
    {
        $user = Auth::user();

        // Kiểm tra mật khẩu cũ
        $isOldValid = Hash::check($request->input('matKhauCu'), $user->MatKhau) || ($user->MatKhau === $request->input('matKhauCu'));

        if (! $isOldValid) {
            return back()->withErrors(['matKhauCu' => 'Mật khẩu cũ không chính xác.']);
        }

        $user->MatKhau = Hash::make($request->input('matKhauMoi'));
        $user->save();

        return redirect()->route($user->isAdmin() ? 'quanlysanpham.dashboard' : 'home')->with('message', 'Đổi mật khẩu thành công!');
    }

    public function thongTin()
    {
        $user = Auth::user();
        $khachHang = $user->khachHang;

        if (! $khachHang) {
            return redirect()->route('home')->with('error', 'Tài khoản của bạn là quản trị, không có thông tin khách hàng.');
        }

        return view('TaiKhoan.ThongTin', compact('khachHang'));
    }
}
