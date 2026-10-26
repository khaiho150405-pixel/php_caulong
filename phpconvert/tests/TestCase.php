<?php

namespace Tests;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function admin(): TaiKhoan
    {
        return TaiKhoan::where('MaQuyen', TaiKhoan::QUYEN_ADMIN)->firstOrFail();
    }

    protected function nhanVien(): TaiKhoan
    {
        return TaiKhoan::where('MaQuyen', TaiKhoan::QUYEN_NHANVIEN)->firstOrFail();
    }

    protected function khachHang(): TaiKhoan
    {
        return TaiKhoan::where('MaQuyen', TaiKhoan::QUYEN_KHACHHANG)->firstOrFail();
    }
}
