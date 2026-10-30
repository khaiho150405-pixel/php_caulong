@extends('layouts.app')

@section('title', 'Thanh toán Đơn hàng - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 30px 15px;">
    <h2>Xác nhận Thông tin Thanh toán</h2>
    <hr />

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="row">
        <div class="col-md-7">
            <h4>Thông tin Giao hàng</h4>

            <form action="{{ route('donhang.checkout.post') }}" method="POST" id="checkoutForm">
                @csrf

                <div class="form-group mb-3" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; margin-bottom: 5px; display: block;">Họ và Tên Người nhận *</label>
                    <input type="text" name="HoTenNhan" class="form-control" value="{{ old('HoTenNhan', $khachHang->HoTen) }}" required style="height: 44px; border-radius: 6px;" />
                </div>

                <div class="form-group mb-3" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; margin-bottom: 5px; display: block;">Số điện thoại *</label>
                    <input type="tel" name="SoDienThoaiNhan" class="form-control" value="{{ old('SoDienThoaiNhan', $khachHang->SoDienThoai) }}" required style="height: 44px; border-radius: 6px;" />
                </div>

                <div class="form-group mb-3" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; margin-bottom: 5px; display: block;">Địa chỉ Giao hàng *</label>
                    <textarea name="DiaChiGiao" class="form-control" rows="3" required style="border-radius: 6px;">{{ old('DiaChiGiao', $khachHang->DiaChi) }}</textarea>
                </div>

                <div class="form-group mb-3" style="margin-bottom: 25px;">
                    <label style="font-weight: 600; margin-bottom: 5px; display: block;">Ghi chú (tùy chọn)</label>
                    <textarea name="GhiChu" class="form-control" rows="2" style="border-radius: 6px;">{{ old('GhiChu') }}</textarea>
                </div>

                <hr />

                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary btn-lg" style="background-color: var(--brand); border-color: var(--brand); font-weight: bold; border-radius: 6px; padding: 12px 25px;">
                        <i class="fa fa-truck"></i> Thanh toán khi nhận hàng (COD)
                    </button>

                    <button type="submit"
                            formaction="{{ route('donhang.payment.momo') }}"
                            class="btn btn-danger btn-lg"
                            style="background-color: #a50064; border-color: #a50064; color: white; display: inline-flex; align-items: center; font-weight: bold; border-radius: 6px; padding: 12px 25px;">
                        <img src="{{ asset('images/momo-logo.png') }}"
                             alt="MoMo Logo"
                             style="width: 25px; height: 25px; margin-right: 8px; background-color: white; border-radius: 4px; padding: 2px; object-fit: contain;" />
                        Thanh toán qua MoMo
                    </button>
                </div>
            </form>
        </div>

        <div class="col-md-5" style="background-color: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #eee;">
            <h4 style="font-weight: 700; margin-top: 0;">Tóm tắt Đơn hàng ({{ $cartItems->count() }} sản phẩm)</h4>
            <hr />

            @foreach ($cartItems as $item)
                @php
                    $variant = $item->chiTietSanPham;
                    $sp = $variant ? $variant->sanPham : null;
                    $tenSP = $sp ? $sp->TenSanPham : 'Sản phẩm';
                    $img = $variant->HinhAnh ?: ($sp->HinhAnhDaiDien ?: 'default-product.jpg');
                    $giaGoc = (float)($variant->GiaBan ?? $sp->GiaGoc ?? 0);
                    $donGia = (float)($variant->effective_price ?? $giaGoc);
                    $thanhTien = $donGia * (int)$item->SoLuong;
                @endphp
                <div style="display: flex; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #eee; align-items: center;">
                    <img src="{{ asset('images/' . $img) }}" style="width: 60px; height: 60px; object-fit: contain; margin-right: 12px; border-radius: 4px; border: 1px solid #ddd;" onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                    <div style="flex-grow: 1;">
                        <strong>{{ $tenSP }}</strong> <br />
                        <span style="font-size: 0.85em; color: #777;">
                            Màu: {{ $variant->mauSac->TenMau ?? 'N/A' }} | Size: {{ $variant->size->TenSize ?? 'N/A' }} | SL: {{ $item->SoLuong }}
                        </span>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: bold; color: var(--price);">
                            {{ number_format($thanhTien, 0, ',', '.') }}đ
                        </div>
                        @if ($donGia < $giaGoc)
                            <div style="font-size: 0.8em; color: #888; text-decoration: line-through;">
                                {{ number_format($giaGoc * (int)$item->SoLuong, 0, ',', '.') }}đ
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <hr />
            <h3 style="text-align: right; margin: 15px 0;">
                Tổng cộng: <span style="color: var(--brand); font-weight: bold;">{{ number_format($total, 0, ',', '.') }}đ</span>
            </h3>
        </div>
    </div>
</div>
@endsection
