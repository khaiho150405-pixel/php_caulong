@extends('layouts.app')

@section('title', 'Đổi Mật Khẩu - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 40px 15px; max-width: 550px;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-top: 0;">Đổi Mật Khẩu</h2>
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

        <form action="{{ route('taikhoan.doimatkhau') }}" method="POST">
            @csrf

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="matKhauCu" style="font-weight: 600; margin-bottom: 6px; display: block;">Mật khẩu hiện tại</label>
                <input type="password" id="matKhauCu" name="matKhauCu" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 18px;">
                <label for="matKhauMoi" style="font-weight: 600; margin-bottom: 6px; display: block;">Mật khẩu mới</label>
                <input type="password" id="matKhauMoi" name="matKhauMoi" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group mb-3" style="margin-bottom: 25px;">
                <label for="xacNhanMoi" style="font-weight: 600; margin-bottom: 6px; display: block;">Xác nhận mật khẩu mới</label>
                <input type="password" id="xacNhanMoi" name="xacNhanMoi" class="form-control" required style="height: 44px; border-radius: 6px;" />
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="background-color: #f36f21; border-color: #f36f21; width: 100%; height: 44px; font-size: 16px; font-weight: bold; border-radius: 6px;">
                    Xác nhận đổi mật khẩu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
