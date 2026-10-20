@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 30px 15px;">
    <h2>🛍️ Giỏ hàng của bạn</h2>
    <hr />

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($cartItems->isEmpty())
        <div class="alert alert-info">
            Giỏ hàng của bạn hiện đang trống. <a href="{{ route('home') }}" class="alert-link">Tiếp tục mua sắm</a>.
        </div>
    @else
        @php
            $total = 0;
        @endphp

        <table class="cart-table table table-hover">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Tùy chọn</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cartItems as $item)
                    @php
                        $variant = $item->chiTietSanPham;
                        $sp = $variant ? $variant->sanPham : null;
                        $tenSP = $sp ? $sp->TenSanPham : 'Sản phẩm';
                        $img = $variant->HinhAnh ?: ($sp->HinhAnhDaiDien ?: 'default-product.jpg');
                        $giaBan = (float)($variant->GiaBan ?? $sp->GiaGoc ?? 0);
                        $thanhTien = $giaBan * (int)$item->SoLuong;
                        $total += $thanhTien;
                    @endphp
                    <tr>
                        <td>
                            <img src="{{ asset('images/' . $img) }}"
                                 alt="{{ $tenSP }}"
                                 class="cart-item-img"
                                 style="width: 80px; height: 80px; object-fit: contain; border-radius: 4px; border: 1px solid #eee;"
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
                            Màu: {{ $variant->mauSac->TenMau ?? 'Mặc định' }} <br />
                            Size: {{ $variant->size->TenSize ?? 'Tiêu chuẩn' }}
                        </td>
                        <td>{{ number_format($giaBan, 0, ',', '.') }} đ</td>
                        <td>
                            <input type="number" value="{{ $item->SoLuong }}" min="1" readonly style="width: 60px; text-align: center; border: 1px solid #ccc; border-radius: 4px; padding: 4px;" />
                        </td>
                        <td style="font-weight: bold; color: #e65100;">{{ number_format($thanhTien, 0, ',', '.') }} đ</td>
                        <td>
                            <form action="{{ route('home.removefromcart', ['maGioHang' => $item->MaGioHang]) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="cart-summary" style="text-align: right; margin-top: 20px;">
            <h3>Tổng tiền giỏ hàng: <span style="color: #f36f21; font-weight: bold;">{{ number_format($total, 0, ',', '.') }} đ</span></h3>
            <a href="{{ route('donhang.checkout') }}" class="btn btn-primary btn-checkout" style="background-color: #f36f21; border-color: #f36f21; padding: 12px 28px; font-size: 16px; font-weight: bold; border-radius: 6px;">
                Tiến hành Thanh toán &rarr;
            </a>
        </div>
    @endif
</div>
@endsection
