@extends('layouts.app')

@section('title', 'Đơn hàng của tôi - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 30px 15px;">
    <h2>Đơn hàng của tôi</h2>
    <hr />


    @if ($orders->isEmpty())
        @include('partials._empty', ['message' => 'Bạn chưa có đơn hàng nào.', 'linkUrl' => route('home'), 'linkText' => 'Mua sắm ngay', 'compact' => false])
    @else
        <table class="table table-hover table-bordered">
            <thead>
                <tr class="active">
                    <th>Mã Đơn</th>
                    <th>Ngày Đặt</th>
                    <th>Địa chỉ nhận</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td style="font-weight: bold;">#{{ $order->MaDonHang }}</td>
                        <td>{{ $order->NgayDat ? \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') : '' }}</td>
                        <td>{{ $order->DiaChiGiaoHang }}</td>
                        <td style="font-weight: bold; color: var(--price);">{{ number_format($order->TongTien, 0, ',', '.') }}đ</td>
                        <td>
                            @if ($order->TrangThai == 'Chờ xác nhận' || $order->TrangThai == 'Chờ thanh toán MoMo')
                                <span class="badge" style="background-color: var(--warn); font-size: 13px;">{{ $order->TrangThai }}</span>
                            @elseif ($order->TrangThai == 'Đã xử lý' || $order->TrangThai == 'Đã giao hàng' || $order->TrangThai == 'Đã hoàn thành' || str_contains($order->TrangThai, 'thành công') || str_contains($order->TrangThai, 'MoMo'))
                                <span class="badge" style="background-color: var(--brand); font-size: 13px;">{{ $order->TrangThai }}</span>
                            @elseif (str_contains($order->TrangThai, 'hủy') || str_contains($order->TrangThai, 'Hủy'))
                                <span class="badge" style="background-color: var(--danger); font-size: 13px;">{{ $order->TrangThai }}</span>
                            @else
                                <span class="badge" style="background-color: #777; font-size: 13px;">{{ $order->TrangThai }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('donhang.details', ['id' => $order->MaDonHang]) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> Xem Chi tiết
                            </a>
                            @if ($order->TrangThai == 'Chờ xác nhận' || $order->TrangThai == 'Chờ thanh toán MoMo')
                                <form action="{{ route('donhang.huydonhang', ['id' => $order->MaDonHang]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" style="margin-left: 5px;">
                                        <i class="fa fa-times"></i> Hủy đơn
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
