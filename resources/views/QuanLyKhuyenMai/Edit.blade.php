@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Khuyến Mãi - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 700px; margin: 0 auto;">
    <div class="mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-pen-to-square text-warning"></i> Chỉnh Sửa Khuyến Mãi #{{ $km->MaKhuyenMai }}</h2>
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

    <form action="{{ route('quanlykhuyenmai.edit', ['id' => $km->MaKhuyenMai]) }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-12">
                <label for="TenChuongTrinh" class="form-label fw-semibold">Tên Chương Trình *</label>
                <input type="text" class="form-control" id="TenChuongTrinh" name="TenChuongTrinh" value="{{ old('TenChuongTrinh', $km->TenChuongTrinh) }}" required />
            </div>

            <div class="col-md-6">
                <label for="PhanTramGiam" class="form-label fw-semibold">Phần Trăm Giảm (%) *</label>
                <input type="number" class="form-control" id="PhanTramGiam" name="PhanTramGiam" value="{{ old('PhanTramGiam', round($km->PhanTramGiam)) }}" min="0" max="100" step="1" required />
            </div>

            <div class="col-md-6">
                <label for="GiamToiDa" class="form-label fw-semibold">Số Tiền Giảm Tối Đa (VNĐ)</label>
                <input type="number" class="form-control" id="GiamToiDa" name="GiamToiDa" value="{{ old('GiamToiDa', $km->GiamToiDa) }}" min="0" step="1000" placeholder="Để trống nếu không giới hạn" />
            </div>

            <div class="col-12">
                <label for="MoTa" class="form-label fw-semibold">Mô Tả</label>
                <input type="text" class="form-control" id="MoTa" name="MoTa" value="{{ old('MoTa', $km->MoTa) }}" maxlength="255" placeholder="Mô tả ngắn về chương trình" />
            </div>

            <div class="col-md-6">
                <label for="NgayBatDau" class="form-label fw-semibold">Ngày Bắt Đầu *</label>
                <input type="date" class="form-control" id="NgayBatDau" name="NgayBatDau" value="{{ old('NgayBatDau', $km->NgayBatDau ? \Carbon\Carbon::parse($km->NgayBatDau)->format('Y-m-d') : '') }}" required />
            </div>

            <div class="col-md-6">
                <label for="NgayKetThuc" class="form-label fw-semibold">Ngày Kết Thúc *</label>
                <input type="date" class="form-control" id="NgayKetThuc" name="NgayKetThuc" value="{{ old('NgayKetThuc', $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc)->format('Y-m-d') : '') }}" required />
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-warning px-4 fw-bold">
                    <i class="fa-solid fa-save"></i> Cập Nhật Khuyến Mãi
                </button>
                <a href="{{ route('quanlykhuyenmai.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
            </div>
        </div>
    </form>
</div>
@endsection
