@extends('layouts.app')

@section('title', 'Đăng nhập tài khoản - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 40px 15px; max-width: 550px;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-top: 0;">Đăng nhập tài khoản</h2>
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

        @if (session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('taikhoan.dangnhap') }}" method="POST">
            @csrf
            @if(request('returnUrl'))
                <input type="hidden" name="returnUrl" value="{{ request('returnUrl') }}">
            @endif

            <div class="form-group mb-3" style="margin-bottom: 20px;">
                <label for="TenDangNhap" style="font-weight: 600; margin-bottom: 6px; display: block;">Tên đăng nhập</label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control" value="{{ old('TenDangNhap') }}" required autofocus style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 25px;">
                <label for="MatKhau" style="font-weight: 600; margin-bottom: 6px; display: block;">Mật khẩu</label>
                <input type="password" id="MatKhau" name="MatKhau" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <button type="submit" class="btn btn-primary" style="background-color: #f36f21; border-color: #f36f21; width: 100%; height: 44px; font-size: 16px; font-weight: bold; border-radius: 6px;">
                    Đăng nhập
                </button>
            </div>
        </form>

        <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
            Bạn chưa có tài khoản? <a href="{{ route('taikhoan.dangky') }}" style="color: #f36f21; font-weight: bold;">Đăng ký ngay</a>
        </div>
    </div>
</div>
@endsection
