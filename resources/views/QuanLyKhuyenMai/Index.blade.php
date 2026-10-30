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

    
    <form action="{{ route('quanlykhuyenmai.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên chương trình..." />
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '')
                <a href="{{ route('quanlykhuyenmai.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

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
                    <th>Số SP áp dụng</th>
                    <th style="width: 150px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $item)
                    <tr>
                        <td class="fw-bold">#{{ $item->MaKhuyenMai }}</td>
                        <td><strong>{{ $item->TenChuongTrinh }}</strong></td>
                        <td class="fw-bold text-price">-{{ round($item->PhanTramGiam) }}%</td>
                        <td>
                            @if ($item->GiamToiDa > 0)
                                {{ number_format($item->GiamToiDa, 0, ',', '.') }} VNĐ
                            @else
                                <span class="text-muted">Không giới hạn</span>
                            @endif
                        </td>
                        <td>{{ $item->NgayBatDau ? \Carbon\Carbon::parse($item->NgayBatDau)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $item->NgayKetThuc ? \Carbon\Carbon::parse($item->NgayKetThuc)->format('d/m/Y') : 'N/A' }}</td>
                        <td><span class="badge bg-secondary">{{ $item->san_phams_count }}</span></td>
                        <td>
                            <a href="{{ route('quanlykhuyenmai.edit', ['id' => $item->MaKhuyenMai]) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            <a href="{{ route('quanlykhuyenmai.delete', ['id' => $item->MaKhuyenMai]) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-0">@include('partials._empty', ['message' => 'Không tìm thấy chương trình khuyến mãi nào.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $list])
</div>
@endsection
