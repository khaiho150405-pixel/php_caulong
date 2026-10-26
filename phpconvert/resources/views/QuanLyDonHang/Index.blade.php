@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-file-invoice-dollar text-primary"></i> Quản Lý Đơn Hàng</h2>
        <span class="badge bg-secondary fs-6">{{ $orders->total() }} đơn</span>
    </div>

    <form action="{{ route('quanlydonhang.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo mã đơn, tên khách, số điện thoại..." />
        </div>
        <div class="col-md-3">
            <select name="trangThai" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                @foreach ($dsTrangThai as $tt)
                    <option value="{{ $tt }}" @selected($trangThai === $tt)>{{ $tt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '' || $trangThai)
                <a href="{{ route('quanlydonhang.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>


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
                @forelse ($orders as $order)
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
                        <td class="fw-bold text-price">{{ number_format($order->TongTien, 0, ',', '.') }} VNĐ</td>
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
                @empty
                    <tr><td colspan="7" class="p-0">@include('partials._empty', ['message' => 'Không tìm thấy đơn hàng nào.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $orders])
</div>
@endsection
