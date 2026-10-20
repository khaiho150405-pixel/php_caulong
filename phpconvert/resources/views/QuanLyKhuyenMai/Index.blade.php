@extends('layouts.admin')

@section('title', 'Quản lý Khuyến mãi - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-tags text-primary"></i> Quản Lý Chương Trình Khuyến Mãi</h2>
        <a href="{{ route('quanlykhuyenmai.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tạo chương trình mới
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
                    <th style="width: 70px;">Mã</th>
                    <th>Tên Chương Trình</th>
                    <th>% Giảm</th>
                    <th>Giảm Tối Đa</th>
                    <th>Bắt Đầu</th>
                    <th>Kết Thúc</th>
                    <th style="width: 150px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                    <tr>
                        <td class="fw-bold">#{{ $item->MaKhuyenMai }}</td>
                        <td><strong>{{ $item->TenChuongTrinh }}</strong></td>
                        <td class="fw-bold text-danger">-{{ round($item->PhanTramGiam) }}%</td>
                        <td>
                            @if ($item->GiamToiDa > 0)
                                {{ number_format($item->GiamToiDa, 0, ',', '.') }} VNĐ
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>
                        <td>{{ $item->NgayBatDau ? \Carbon\Carbon::parse($item->NgayBatDau)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $item->NgayKetThuc ? \Carbon\Carbon::parse($item->NgayKetThuc)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('quanlykhuyenmai.edit', ['id' => $item->MaKhuyenMai]) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            <a href="{{ route('quanlykhuyenmai.delete', ['id' => $item->MaKhuyenMai]) }}" class="btn btn-sm btn-outline-danger">
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
