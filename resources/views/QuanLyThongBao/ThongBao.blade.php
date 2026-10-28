@extends('layouts.admin')

@section('title', 'Cảnh báo Đánh giá thấp - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold text-danger">
            <i class="fa-solid fa-triangle-exclamation"></i> Cảnh Báo Phản Hồi & Đánh Giá Thấp (<= 2 Sao)
        </h2>
        <span class="badge bg-danger fs-6">{{ $data->count() }} phản hồi cần xử lý</span>
    </div>
    <hr />

    <div class="alert alert-info">
        Hệ thống tự động phát hiện các đánh giá tiêu cực (1 - 2 sao) từ khách hàng để đội ngũ CSKH kịp thời liên hệ giải quyết khiếu nại.
    </div>

    @if ($data->isEmpty())
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> Tuyệt vời! Hiện không có đánh giá tiêu cực nào cần xử lý.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">Mã PH</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Đánh Giá</th>
                        <th>Nội Dung Phản Hồi</th>
                        <th>Thời Gian</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td class="fw-bold">#{{ $item->MaPhanHoi }}</td>
                            <td>
                                <strong>{{ $item->khachHang->HoTen ?? 'Ẩn danh' }}</strong>
                                @if ($item->khachHang && $item->khachHang->SoDienThoai)
                                    <br><small class="text-muted"><i class="fa fa-phone"></i> {{ $item->khachHang->SoDienThoai }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($item->sanPham)
                                    <a href="{{ route('home.chitietsanpham', ['id' => $item->sanPham->MaSanPham]) }}" target="_blank" class="fw-semibold text-decoration-none">
                                        {{ $item->sanPham->TenSanPham }} <i class="fa fa-external-link-alt small"></i>
                                    </a>
                                @else
                                    <span class="text-muted">Sản phẩm đã xóa</span>
                                @endif
                            </td>
                            <td class="text-danger fw-bold fs-6">
                                @for ($i = 1; $i <= $item->DanhGia; $i++)
                                    ★
                                @endfor
                                ({{ $item->DanhGia }} sao)
                            </td>
                            <td style="max-width: 350px;">
                                <div class="p-2 bg-light rounded border text-dark">
                                    {{ $item->NoiDung }}
                                </div>
                            </td>
                            <td>
                                {{ $item->NgayPhanHoi ? \Carbon\Carbon::parse($item->NgayPhanHoi)->format('d/m/Y H:i') : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
