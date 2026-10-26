@extends('layouts.app')

@section('title', 'Đăng nhập tài khoản - Cửa Hàng Cầu Lông')

@section('content')
<div class="auth-split">
    <aside class="auth-visual has-photo" style="background-image: url('{{ asset('images/landing/cat-aoquan.webp') }}');">
        <h2>Chào mừng trở lại</h2>
        <p>Đăng nhập để theo dõi đơn hàng và mua sắm nhanh hơn.</p>
        <ul class="auth-perks">
            <li><i class="fa-solid fa-box-open"></i> Theo dõi trạng thái đơn hàng</li>
            <li><i class="fa-solid fa-tags"></i> Nhận ưu đãi dành cho thành viên</li>
            <li><i class="fa-solid fa-star"></i> Đánh giá sản phẩm đã mua</li>
        </ul>
    </aside>

    <section class="auth-form">
        <h1>Đăng nhập</h1>
        <p class="auth-sub">Nhập thông tin tài khoản của bạn để tiếp tục.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('taikhoan.dangnhap') }}" method="POST">
            @csrf
            @if(request('returnUrl'))
                <input type="hidden" name="returnUrl" value="{{ request('returnUrl') }}">
            @endif

            <div class="mb-3">
                <label for="TenDangNhap" class="form-label">Tên đăng nhập</label>
                <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control" value="{{ old('TenDangNhap') }}" required autofocus />
            </div>

            <div class="mb-4">
                <label for="MatKhau" class="form-label">Mật khẩu</label>
                <input type="password" id="MatKhau" name="MatKhau" class="form-control" required />
            </div>

            <button type="submit" class="btn btn-primary">Đăng nhập</button>
        </form>

        <div class="auth-switch">
            Bạn chưa có tài khoản? <a href="{{ route('taikhoan.dangky') }}">Đăng ký ngay</a>
        </div>
    </section>
</div>
@endsection
