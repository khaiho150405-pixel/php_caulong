@extends('layouts.app')

@section('title', 'Chi tiết Đơn hàng #' . $order->MaDonHang . ' - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 30px 15px;">
    <h2>Chi tiết Đơn hàng #{{ $order->MaDonHang }}</h2>
    <a href="{{ route('donhang.index') }}" class="btn btn-default btn-sm" style="margin-bottom: 20px;">
        &larr; Quay lại danh sách đơn hàng
    </a>
    <hr />

    <div class="row">
        <div class="col-md-7">
            <h4>Thông tin đơn hàng</h4>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 30%; background: #f9f9f9;">Mã đơn:</th>
                    <td>#{{ $order->MaDonHang }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Ngày đặt:</th>
                    <td>{{ $order->NgayDat ? \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Trạng thái:</th>
                    <td>
                        <span class="label label-warning" style="font-size: 13px;">{{ $order->TrangThai }}</span>
                    </td>
                </tr>
            </table>

            <h4>Thông tin người nhận</h4>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 30%; background: #f9f9f9;">Người đặt:</th>
                    <td>{{ $order->khachHang->HoTen ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Số điện thoại:</th>
                    <td>{{ $order->SoDienThoaiNhanHang }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Địa chỉ giao:</th>
                    <td>{{ $order->DiaChiGiaoHang }}</td>
                </tr>
                @if ($order->GhiChu)
                    <tr>
                        <th style="background: #f9f9f9;">Ghi chú:</th>
                        <td>{{ $order->GhiChu }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="col-md-5" style="text-align: right;">
            <h4>Tổng thanh toán</h4>
            <h2 style="color: var(--brand); font-weight: bold; margin-top: 10px;">{{ number_format($order->TongTien, 0, ',', '.') }}đ</h2>
        </div>
    </div>

    <hr />

    <h4>Chi tiết sản phẩm đã đặt</h4>
    <table class="table table-hover table-bordered">
        <thead>
            <tr class="active">
                <th colspan="2">Sản phẩm</th>
                <th>Phân loại</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->chiTietDonHangs as $item)
                @php
                    $variant = $item->chiTietSanPham;
                    $sp = $variant ? $variant->sanPham : null;
                    $tenSP = $sp ? $sp->TenSanPham : 'Sản phẩm';
                    $img = $variant->HinhAnh ?: ($sp->HinhAnhDaiDien ?: 'default-product.jpg');
                @endphp
                <tr>
                    <td style="width: 70px;">
                        <img src="{{ asset('images/' . $img) }}"
                             style="width: 60px; height: 60px; object-fit: contain; border-radius: 4px; border: 1px solid #eee;"
                             onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                    </td>
                    <td>
                        @if ($sp)
                            <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" style="font-weight: 600; color: #333; text-decoration: none;">
                                {{ $tenSP }}
                            </a>
                        @else
                            {{ $tenSP }}
                        @endif
                    </td>
                    <td>
                        @if ($variant && $variant->mauSac)
                            <span>Màu: {{ $variant->mauSac->TenMau }}</span><br />
                        @endif
                        @if ($variant && $variant->size)
                            <span>Size: {{ $variant->size->TenSize }}</span>
                        @endif
                    </td>
                    <td>{{ number_format($item->DonGia, 0, ',', '.') }}đ</td>
                    <td>{{ $item->SoLuong }}</td>
                    <td style="font-weight: bold; color: var(--price);">{{ number_format($item->ThanhTien, 0, ',', '.') }}đ</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
