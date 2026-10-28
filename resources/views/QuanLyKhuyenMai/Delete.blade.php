@extends('layouts.admin')

@section('title', 'Xóa Khuyến Mãi - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px; margin: 0 auto;">
    <h2 class="h4 mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Xác Nhận Xóa Khuyến Mãi</h2>
    <hr />

    <div class="alert alert-warning">
        Bạn có chắc chắn muốn xóa chương trình khuyến mãi <strong>"{{ $km->TenChuongTrinh }}"</strong> không? Các sản phẩm đang áp dụng khuyến mãi này sẽ được tự động gỡ bỏ.
    </div>

    <table class="table table-bordered">
        <tr>
            <th style="width: 35%; background: #f9f9f9;">Mã KM:</th>
            <td>#{{ $km->MaKhuyenMai }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Tên chương trình:</th>
            <td class="fw-bold">{{ $km->TenChuongTrinh }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Mức giảm:</th>
            <td class="text-danger fw-bold">-{{ round($km->PhanTramGiam) }}%</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Thời gian áp dụng:</th>
            <td>
                {{ $km->NgayBatDau ? \Carbon\Carbon::parse($km->NgayBatDau)->format('d/m/Y') : 'N/A' }}
                đến
                {{ $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc)->format('d/m/Y') : 'N/A' }}
            </td>
        </tr>
    </table>

    <form action="{{ route('quanlykhuyenmai.delete', ['id' => $km->MaKhuyenMai]) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger px-4">
            <i class="fa-solid fa-trash"></i> Xác Nhận Xóa
        </button>
        <a href="{{ route('quanlykhuyenmai.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
    </form>
</div>
@endsection
