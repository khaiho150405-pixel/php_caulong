@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-box-archive text-primary"></i> Danh Sách Sản Phẩm</h2>
        <a href="{{ route('quanlysanpham.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm mới
        </a>
    </div>

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá Gốc</th>
                    <th>Loại SP</th>
                    <th>Hãng</th>
                    <th>Ngày Tạo</th>
                    <th style="width: 190px;">Chức Năng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sanPhams as $item)
                    <tr>
                        <td class="text-center">
                            <img src="{{ asset('images/' . ($item->HinhAnhDaiDien ?: 'default-product.jpg')) }}"
                                 width="60" height="60"
                                 alt="{{ $item->TenSanPham }}"
                                 class="img-thumbnail"
                                 style="object-fit: contain;"
                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                        </td>
                        <td>
                            <strong>{{ $item->TenSanPham }}</strong>
                        </td>
                        <td class="fw-bold text-danger">
                            {{ number_format($item->GiaGoc, 0, ',', '.') }} VNĐ
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->loaiSanPham->TenLoai ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{ $item->hang->TenHang ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $item->NgayTao ? \Carbon\Carbon::parse($item->NgayTao)->format('d/m/Y') : '' }}
                        </td>
                        <td>
                            <a href="{{ route('quanlysanpham.edit', ['id' => $item->MaSanPham]) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            <a href="{{ route('quanlysanpham.details', ['id' => $item->MaSanPham]) }}" class="btn btn-sm btn-outline-info">
                                <i class="fa-solid fa-eye"></i> Chi tiết
                            </a>
                            <a href="{{ route('quanlysanpham.delete', ['id' => $item->MaSanPham]) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
