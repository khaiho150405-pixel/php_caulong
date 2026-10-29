@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Tài Khoản - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 750px; margin: 0 auto;">
    <div class="mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-user-pen text-warning"></i> Chỉnh Sửa Tài Khoản #{{ $taiKhoan->MaTaiKhoan }}</h2>
    </div>
    <hr />

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('quanlytaikhoan.edit', ['id' => $taiKhoan->MaTaiKhoan]) }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="TenDangNhap" class="form-label fw-semibold">Tên Đăng Nhập</label>
                <input type="text" class="form-control bg-light" id="TenDangNhap" value="{{ $taiKhoan->TenDangNhap }}" readonly />
            </div>

            <div class="col-md-6">
                <label for="MatKhau" class="form-label fw-semibold">Mật Khẩu Mới (để trống nếu không đổi)</label>
                <input type="password" class="form-control" id="MatKhau" name="MatKhau" placeholder="Nhập mật khẩu mới..." />
            </div>

            <div class="col-md-6">
                <label for="HoTen" class="form-label fw-semibold">Họ và Tên *</label>
                <input type="text" class="form-control" id="HoTen" name="HoTen" value="{{ old('HoTen', $taiKhoan->khachHang->HoTen ?? '') }}" required />
            </div>

            <div class="col-md-6">
                <label for="Email" class="form-label fw-semibold">Email *</label>
                <input type="email" class="form-control" id="Email" name="Email" value="{{ old('Email', $taiKhoan->Email) }}" required />
            </div>

            <div class="col-md-6">
                <label for="SoDienThoai" class="form-label fw-semibold">Số Điện Thoại</label>
                <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai', $taiKhoan->khachHang->SoDienThoai ?? '') }}" />
            </div>

            <div class="col-md-6">
                <label for="MaQuyen" class="form-label fw-semibold">Phân Quyền *</label>
                <select class="form-select" id="MaQuyen" name="MaQuyen" required>
                    @foreach ($phanQuyens as $pq)
                        <option value="{{ $pq->MaQuyen }}" {{ old('MaQuyen', $taiKhoan->MaQuyen) == $pq->MaQuyen ? 'selected' : '' }}>
                            {{ $pq->TenQuyen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label for="DiaChi" class="form-label fw-semibold">Địa Chỉ</label>
                <input type="text" class="form-control" id="DiaChi" name="DiaChi" value="{{ old('DiaChi', $taiKhoan->khachHang->DiaChi ?? '') }}" />
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-warning px-4 fw-bold">
                    <i class="fa-solid fa-save"></i> Cập Nhật Tài Khoản
                </button>
                <a href="{{ route('quanlytaikhoan.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
            </div>
        </div>
    </form>
</div>
@endsection
