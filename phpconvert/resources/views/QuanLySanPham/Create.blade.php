@extends('layouts.admin')

@section('title', 'Tạo Mới Sản Phẩm - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 900px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-plus-circle text-primary"></i> Tạo Mới Sản Phẩm</h2>
        <a href="{{ route('quanlysanpham.index') }}" class="btn btn-outline-secondary btn-sm">
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

    <form action="{{ route('quanlysanpham.create.post') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-md-8">
                <label for="TenSanPham" class="form-label fw-semibold">Tên Sản Phẩm *</label>
                <input type="text" class="form-control" id="TenSanPham" name="TenSanPham" value="{{ old('TenSanPham') }}" required />
            </div>

            <div class="col-md-4">
                <label for="GiaGoc" class="form-label fw-semibold">Giá Gốc (VNĐ) *</label>
                <input type="number" class="form-control" id="GiaGoc" name="GiaGoc" value="{{ old('GiaGoc') }}" required min="0" step="1000" />
            </div>

            <div class="col-md-4">
                <label for="MaLoai" class="form-label fw-semibold">Loại Sản Phẩm *</label>
                <select class="form-select" id="MaLoai" name="MaLoai" required>
                    <option value="">-- Chọn Loại SP --</option>
                    @foreach ($loaiSanPhams as $loai)
                        <option value="{{ $loai->MaLoai }}" {{ old('MaLoai') == $loai->MaLoai ? 'selected' : '' }}>
                            {{ $loai->TenLoai }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="MaHang" class="form-label fw-semibold">Hãng Sản Xuất *</label>
                <select class="form-select" id="MaHang" name="MaHang" required>
                    <option value="">-- Chọn Hãng --</option>
                    @foreach ($hangs as $h)
                        <option value="{{ $h->MaHang }}" {{ old('MaHang') == $h->MaHang ? 'selected' : '' }}>
                            {{ $h->TenHang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label for="MaNhaCungCap" class="form-label fw-semibold">Nhà Cung Cấp *</label>
                <select class="form-select" id="MaNhaCungCap" name="MaNhaCungCap" required>
                    <option value="">-- Chọn NCC --</option>
                    @foreach ($nhaCungCaps as $ncc)
                        <option value="{{ $ncc->MaNhaCungCap }}" {{ old('MaNhaCungCap') == $ncc->MaNhaCungCap ? 'selected' : '' }}>
                            {{ $ncc->TenNhaCungCap }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="MaKhuyenMai" class="form-label fw-semibold">Chương Trình Khuyến Mãi</label>
                <select class="form-select" id="MaKhuyenMai" name="MaKhuyenMai">
                    <option value="">-- Không Áp Dụng --</option>
                    @foreach ($khuyenMais as $km)
                        <option value="{{ $km->MaKhuyenMai }}" {{ old('MaKhuyenMai') == $km->MaKhuyenMai ? 'selected' : '' }}>
                            {{ $km->TenChuongTrinh }} (-{{ round($km->PhanTramGiam) }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label for="HinhAnhUpload" class="form-label fw-semibold">Hình Ảnh Đại Diện</label>
                <input type="file" class="form-control" id="HinhAnhUpload" name="HinhAnhUpload" accept="image/*" />
            </div>

            <div class="col-12">
                <label for="MoTa" class="form-label fw-semibold">Mô Tả Chi Tiết</label>
                <textarea class="form-control" id="MoTa" name="MoTa" rows="5">{{ old('MoTa') }}</textarea>
            </div>

            <div class="col-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="CoSize" name="CoSize" value="1" {{ old('CoSize') ? 'checked' : '' }}>
                    <label class="form-check-label" for="CoSize">Sản phẩm có Size</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="CoMau" name="CoMau" value="1" {{ old('CoMau') ? 'checked' : '' }}>
                    <label class="form-check-label" for="CoMau">Sản phẩm có Màu</label>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fa-solid fa-save"></i> Lưu Sản Phẩm
                </button>
                <a href="{{ route('quanlysanpham.index') }}" class="btn btn-secondary px-4 ms-2">Hủy</a>
            </div>
        </div>
    </form>
</div>
@endsection
