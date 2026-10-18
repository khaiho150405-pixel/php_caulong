@extends('layouts.admin')

@section('title', 'Tạo Mới Tài Khoản - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 750px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-user-plus text-primary"></i> Tạo Mới Tài Khoản</h2>
        <a href="{{ route('quanlytaikhoan.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Quay lại danh sách
        </a>
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

    <form action="{{ route('quanlytaikhoan.create') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="TenDangNhap" class="form-label fw-semibold">Tên Đăng Nhập *</label>
                <input type="text" class="form-control" id="TenDangNhap" name="TenDangNhap" value="{{ old('TenDangNhap') }}" required />
            </div>

            <div class="col-md-6">
                <label for="MatKhau" class="form-label fw-semibold">Mật Khẩu *</label>
                <input type="password" class="form-control" id="MatKhau" name="MatKhau" required />
            </div>

            <div class="col-md-6">
                <label for="HoTen" class="form-label fw-semibold">Họ và Tên *</label>
                <input type="text" class="form-control" id="HoTen" name="HoTen" value="{{ old('HoTen') }}" required />
            </div>

            <div class="col-md-6">
                <label for="Email" class="form-label fw-semibold">Email *</label>
                <input type="email" class="form-control" id="Email" name="Email" value="{{ old('Email') }}" required />
            </div>

            <div class="col-md-6">
                <label for="SoDienThoai" class="form-label fw-semibold">Số Điện Thoại</label>
                <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai') }}" />
            </div>

            <div class="col-md-6">
                <label for="MaQuyen" class="form-label fw-semibold">Phân Quyền *</label>
                <select class="form-select" id="MaQuyen" name="MaQuyen" required>
                    <option value="">-- Chọn Phân Quyền --</option>
                    @foreach ($phanQuyens as $pq)
                        <option value="{{ $pq->MaQuyen }}" {{ old('MaQuyen') == $pq->MaQuyen ? 'selected' : '' }}>
                            {{ $pq->TenQuyen }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label for="DiaChi" class="form-label fw-semibold">Địa Chỉ</label>
                <input type="text" class="form-control" id="DiaChi" name="DiaChi" value="{{ old('DiaChi') }}" />
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save"></i> Tạo Tài Khoản
                </button>
                <a href="{{ route('quanlytaikhoan.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
            </div>
        </div>
    </form>
</div>
@endsection
