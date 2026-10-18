@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 40px 15px; max-width: 600px;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-top: 0;">Đăng ký tài khoản mới</h2>
        <hr />

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('taikhoan.dangky') }}" method="POST">
            @csrf

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="HoTen" style="font-weight: 600; margin-bottom: 6px; display: block;">Họ và Tên *</label>
                <input type="text" id="HoTen" name="HoTen" class="form-control" value="{{ old('HoTen') }}" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="TenDangNhap" style="font-weight: 600; margin-bottom: 6px; display: block;">Tên đăng nhập *</label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control" value="{{ old('TenDangNhap') }}" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="Email" style="font-weight: 600; margin-bottom: 6px; display: block;">Email *</label>
                <input type="email" id="Email" name="Email" class="form-control" value="{{ old('Email') }}" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="SoDienThoai" style="font-weight: 600; margin-bottom: 6px; display: block;">Số điện thoại</label>
                <input type="tel" id="SoDienThoai" name="SoDienThoai" class="form-control" value="{{ old('SoDienThoai') }}" style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="DiaChi" style="font-weight: 600; margin-bottom: 6px; display: block;">Địa chỉ</label>
                <input type="text" id="DiaChi" name="DiaChi" class="form-control" value="{{ old('DiaChi') }}" style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="MatKhau" style="font-weight: 600; margin-bottom: 6px; display: block;">Mật khẩu *</label>
                <input type="password" id="MatKhau" name="MatKhau" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 25px;">
                <label for="XacNhanMatKhau" style="font-weight: 600; margin-bottom: 6px; display: block;">Xác nhận mật khẩu *</label>
                <input type="password" id="XacNhanMatKhau" name="XacNhanMatKhau" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <button type="submit" class="btn btn-primary" style="background-color: #f36f21; border-color: #f36f21; width: 100%; height: 44px; font-size: 16px; font-weight: bold; border-radius: 6px;">
                    Tạo tài khoản
                </button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 15px; font-size: 14px; color: #666;">
            Đã có tài khoản? <a href="{{ route('taikhoan.dangnhap') }}" style="color: #f36f21; font-weight: bold;">Đăng nhập ngay</a>
        </div>
    </div>
</div>
@endsection
