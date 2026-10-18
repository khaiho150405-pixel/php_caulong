@extends('layouts.admin')

@section('title', 'Xóa Sản Phẩm - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 650px; margin: 0 auto;">
    <h2 class="h4 mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Xác Nhận Xóa Sản Phẩm</h2>
    <hr />

    <div class="alert alert-warning">
        Bạn có chắc chắn muốn xóa sản phẩm <strong>"{{ $sanPham->TenSanPham }}"</strong> không? Thao tác này sẽ xóa các biến thể và không thể hoàn tác!
    </div>

    <div class="text-center mb-3">
        <img src="{{ asset('images/' . ($sanPham->HinhAnhDaiDien ?: 'default-product.jpg')) }}"
             style="max-height: 180px; object-fit: contain;"
             class="img-thumbnail"
             alt="{{ $sanPham->TenSanPham }}"
             onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
    </div>

    <table class="table table-bordered">
        <tr>
            <th style="width: 35%; background: #f9f9f9;">Mã sản phẩm:</th>
            <td>#{{ $sanPham->MaSanPham }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Tên sản phẩm:</th>
            <td class="fw-bold">{{ $sanPham->TenSanPham }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Giá gốc:</th>
            <td class="text-danger fw-bold">{{ number_format($sanPham->GiaGoc, 0, ',', '.') }} VNĐ</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Loại sản phẩm:</th>
            <td>{{ $sanPham->loaiSanPham->TenLoai ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Hãng:</th>
            <td>{{ $sanPham->hang->TenHang ?? 'N/A' }}</td>
        </tr>
    </table>

    <form action="{{ route('quanlysanpham.delete.post', ['id' => $sanPham->MaSanPham]) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger px-4">
            <i class="fa-solid fa-trash"></i> Xác Nhận Xóa
        </button>
        <a href="{{ route('quanlysanpham.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
    </form>
</div>
@endsection
