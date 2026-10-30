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

    
    <form action="{{ route('quanlysanpham.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên sản phẩm, hãng, loại..." />
        </div>
        <div class="col-md-3">
            <select name="maLoai" class="form-select">
                <option value="">-- Tất cả loại --</option>
                @foreach ($loaiSanPhams as $loai)
                    <option value="{{ $loai->MaLoai }}" @selected((string) $maLoai === (string) $loai->MaLoai)>{{ $loai->TenLoai }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="maHang" class="form-select">
                <option value="">-- Tất cả hãng --</option>
                @foreach ($hangs as $h)
                    <option value="{{ $h->MaHang }}" @selected((string) $maHang === (string) $h->MaHang)>{{ $h->TenHang }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '' || $maLoai || $maHang)
                <a href="{{ route('quanlysanpham.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">Hình Ảnh</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá Gốc</th>
                    <th>Loại SP</th>
                    <th>Hãng</th>
                    <th>Tồn Kho</th>
                    <th>Ngày Tạo</th>
                    <th style="width: 190px;">Chức Năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sanPhams as $item)
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
                        <td class="fw-bold text-price">
                            {{ number_format($item->GiaGoc, 0, ',', '.') }} VNĐ
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->loaiSanPham->TenLoai ?? 'N/A' }}</span>
                        </td>
                        <td>
                            {{ $item->hang->TenHang ?? 'N/A' }}
                        </td>
                        <td>
                            @if ((int) $item->tong_ton_kho > 0)
                                <span class="badge bg-success">{{ (int) $item->tong_ton_kho }}</span>
                            @else
                                <span class="badge bg-danger">Hết hàng</span>
                            @endif
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
                @empty
                    <tr><td colspan="8" class="p-0">@include('partials._empty', ['message' => 'Không tìm thấy sản phẩm nào.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $sanPhams])
</div>
@endsection
