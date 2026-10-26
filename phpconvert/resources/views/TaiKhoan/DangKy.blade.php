@extends('layouts.app')

@section('title', 'Đăng ký tài khoản - Cửa Hàng Cầu Lông')

@section('content')
<div class="auth-split">
    <aside class="auth-visual is-collage">
        <div class="auth-collage" aria-hidden="true">
            <figure><img src="{{ asset('images/thumbs/astrox100zz.webp') }}" alt="" width="260" height="260" loading="lazy" decoding="async"></figure>
            <figure><img src="{{ asset('images/thumbs/yonex_eclipsion_trang_z3.webp') }}" alt="" width="260" height="260" loading="lazy" decoding="async"></figure>
            <figure><img src="{{ asset('images/thumbs/yonex_trm3089.webp') }}" alt="" width="260" height="260" loading="lazy" decoding="async"></figure>
            <figure><img src="{{ asset('images/thumbs/lining_ayas018.webp') }}" alt="" width="260" height="260" loading="lazy" decoding="async"></figure>
        </div>
        <div class="auth-copy">
            <h2>Tham gia cùng chúng tôi</h2>
            <p>Tạo tài khoản chỉ mất vài giây, mua sắm cầu lông tiện lợi hơn.</p>
            <ul class="auth-perks">
                <li><i class="fa-solid fa-shield-halved"></i> Sản phẩm chính hãng, rõ nguồn gốc</li>
                <li><i class="fa-solid fa-truck-fast"></i> Giao hàng nhanh, đặt hàng dễ dàng</li>
                <li><i class="fa-solid fa-comments"></i> Trợ lý AI tư vấn vợt, giày, trang phục</li>
            </ul>
        </div>
    </aside>

    <section class="auth-form">
        <h1>Đăng ký tài khoản</h1>
        <p class="auth-sub">Các trường có dấu * là bắt buộc.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('taikhoan.dangky') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="HoTen" class="form-label">Họ và tên *</label>
                <input type="text" id="HoTen" name="HoTen" class="form-control" value="{{ old('HoTen') }}" required />
            </div>

            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label for="TenDangNhap" class="form-label">Tên đăng nhập *</label>
                    <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control" value="{{ old('TenDangNhap') }}" required />
                </div>
                <div class="col-sm-6">
                    <label for="SoDienThoai" class="form-label">Số điện thoại</label>
                    <input type="tel" id="SoDienThoai" name="SoDienThoai" class="form-control" value="{{ old('SoDienThoai') }}" />
                </div>
            </div>

            <div class="mb-3">
                <label for="Email" class="form-label">Email *</label>
                <input type="email" id="Email" name="Email" class="form-control" value="{{ old('Email') }}" required />
            </div>

            <div class="mb-3">
                <label for="DiaChi" class="form-label">Địa chỉ</label>
                <input type="text" id="DiaChi" name="DiaChi" class="form-control" value="{{ old('DiaChi') }}" />
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label for="MatKhau" class="form-label">Mật khẩu *</label>
                    <input type="password" id="MatKhau" name="MatKhau" class="form-control" required />
                </div>
                <div class="col-sm-6">
                    <label for="XacNhanMatKhau" class="form-label">Xác nhận mật khẩu *</label>
                    <input type="password" id="XacNhanMatKhau" name="XacNhanMatKhau" class="form-control" required />
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
        </form>

        <div class="auth-switch">
            Đã có tài khoản? <a href="{{ route('taikhoan.dangnhap') }}">Đăng nhập ngay</a>
        </div>
    </section>
</div>
@endsection
