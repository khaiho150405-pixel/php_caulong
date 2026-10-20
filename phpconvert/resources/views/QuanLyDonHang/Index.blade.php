@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-file-invoice-dollar text-primary"></i> Quản Lý Đơn Hàng</h2>
        <span class="badge bg-secondary fs-6">{{ $orders->count() }} đơn</span>
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
                    <th style="width: 90px;">Mã Đơn</th>
                    <th>Người Đặt</th>
                    <th>Ngày Đặt</th>
                    <th>Địa Chỉ Nhận</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th style="width: 140px;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td class="fw-bold">#{{ $order->MaDonHang }}</td>
                        <td>
                            <strong>{{ $order->khachHang->HoTen ?? 'Khách lẻ' }}</strong>
                            @if ($order->SoDienThoaiNhanHang)
                                <br><small class="text-muted">{{ $order->SoDienThoaiNhanHang }}</small>
                            @endif
                        </td>
                        <td>{{ $order->NgayDat ? \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') : '' }}</td>
                        <td>{{ $order->DiaChiGiaoHang }}</td>
                        <td class="fw-bold text-danger">{{ number_format($order->TongTien, 0, ',', '.') }} VNĐ</td>
                        <td>
                            @if ($order->TrangThai == 'Chờ xác nhận' || $order->TrangThai == 'Chờ thanh toán MoMo')
                                <span class="badge bg-warning text-dark">{{ $order->TrangThai }}</span>
                            @elseif ($order->TrangThai == 'Đang xử lý')
                                <span class="badge bg-info text-dark">{{ $order->TrangThai }}</span>
                            @elseif ($order->TrangThai == 'Đang giao hàng')
                                <span class="badge bg-primary">{{ $order->TrangThai }}</span>
                            @elseif ($order->TrangThai == 'Đã hoàn thành' || str_contains($order->TrangThai, 'thành công') || str_contains($order->TrangThai, 'MoMo'))
                                <span class="badge bg-success">{{ $order->TrangThai }}</span>
                            @elseif (str_contains($order->TrangThai, 'hủy') || str_contains($order->TrangThai, 'Hủy'))
                                <span class="badge bg-danger">{{ $order->TrangThai }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->TrangThai }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('quanlydonhang.details', ['id' => $order->MaDonHang]) }}" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-eye"></i> Xem Chi Tiết
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
