@extends('layouts.app')

@section('title', 'Giỏ hàng của bạn - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .cart-table {
        vertical-align: middle;
    }

    .cart-qty-wrapper {
        display: inline-flex;
        align-items: center;
        border: 1px solid var(--line-strong, #ccc);
        border-radius: var(--radius-sm, 6px);
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .cart-qty-wrapper:focus-within {
        border-color: var(--brand, #008744);
        box-shadow: 0 0 0 2px var(--brand-50, rgba(0, 135, 68, 0.15));
    }

    .btn-qty {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border: none;
        color: #333;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
        padding: 0;
        line-height: 1;
        transition: background-color 0.15s, color 0.15s;
    }

    .btn-qty:hover:not(:disabled) {
        background: var(--brand-50, #e8f5e9);
        color: var(--brand, #008744);
    }

    .btn-qty:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        background: #f1f3f5;
        color: #999;
    }

    .input-qty {
        width: 48px;
        height: 32px;
        border: none;
        border-left: 1px solid #e9ecef;
        border-right: 1px solid #e9ecef;
        text-align: center;
        font-weight: 600;
        font-size: 14px;
        color: #212529;
        padding: 0;
        outline: none;
        background: #fff;
        -moz-appearance: textfield;
    }

    .input-qty::-webkit-outer-spin-button,
    .input-qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .cart-qty-wrapper.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .cart-table th, .cart-table td {
            padding: 8px 4px;
            font-size: 13px;
        }

        .cart-item-img {
            width: 60px !important;
            height: 60px !important;
        }

        .btn-qty {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }

        .input-qty {
            width: 40px;
            height: 28px;
            font-size: 13px;
        }
    }
</style>
@endsection

@section('content')
<div class="container" style="padding: 30px 15px;">
    <h2>🛍️ Giỏ hàng của bạn</h2>
    <hr />

    @if ($cartItems->isEmpty())
        @include('partials._empty', ['message' => 'Giỏ hàng của bạn hiện đang trống.', 'linkUrl' => route('home'), 'linkText' => 'Tiếp tục mua sắm', 'compact' => false])
    @else
        @php
            $total = 0;
        @endphp

        <div class="table-responsive">
            <table class="cart-table table table-hover">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Tùy chọn</th>
                        <th>Đơn giá</th>
                        <th style="min-width: 140px;">Số lượng</th>
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
                            $giaGoc = (float)($variant->GiaBan ?? $sp->GiaGoc ?? 0);
                            $donGia = (float)($variant->effective_price ?? $giaGoc);
                            $thanhTien = $donGia * (int)$item->SoLuong;
                            $total += $thanhTien;
                            $tonKho = (int)($variant->SoLuongTon ?? 999);
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
                            <td>
                                <div style="font-weight: 600; color: var(--price);">{{ number_format($donGia, 0, ',', '.') }} đ</div>
                                @if ($donGia < $giaGoc)
                                    <div style="font-size: 12px; color: #888; text-decoration: line-through;">{{ number_format($giaGoc, 0, ',', '.') }} đ</div>
                                @endif
                            </td>
                            <td>
                                <form class="cart-qty-form" action="{{ route('home.updatecartquantity', ['maGioHang' => $item->MaGioHang]) }}" method="POST">
                                    @csrf
                                    <div class="cart-qty-wrapper" data-cart-id="{{ $item->MaGioHang }}" data-unit-price="{{ $donGia }}" data-max-stock="{{ $tonKho }}">
                                        <button type="button" class="btn-qty btn-qty-dec" title="Giảm số lượng" @disabled($item->SoLuong <= 1)>-</button>
                                        <input type="number" name="quantity" class="input-qty" value="{{ $item->SoLuong }}" min="1" max="{{ $tonKho }}" aria-label="Số lượng" />
                                        <button type="button" class="btn-qty btn-qty-inc" title="Tăng số lượng" @disabled($item->SoLuong >= $tonKho)>+</button>
                                    </div>
                                </form>
                            </td>
                            <td class="item-total-cell" data-cart-id="{{ $item->MaGioHang }}" style="font-weight: bold; color: var(--price);">{{ number_format($thanhTien, 0, ',', '.') }} đ</td>
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
        </div>

        <div class="cart-summary" style="text-align: right; margin-top: 20px;">
            <h3>Tổng tiền giỏ hàng: <span id="cart-total-display" style="color: var(--brand); font-weight: bold;">{{ number_format($total, 0, ',', '.') }} đ</span></h3>
            <a href="{{ route('donhang.checkout') }}" class="btn btn-primary btn-checkout" style="background-color: var(--brand); border-color: var(--brand); padding: 12px 28px; font-size: 16px; font-weight: bold; border-radius: 6px;">
                Tiến hành Thanh toán &rarr;
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function updateQuantity(wrapper, newQty) {
            const cartId = wrapper.dataset.cartId;
            const maxStock = parseInt(wrapper.dataset.maxStock) || 999;
            const input = wrapper.querySelector('.input-qty');
            const btnDec = wrapper.querySelector('.btn-qty-dec');
            const btnInc = wrapper.querySelector('.btn-qty-inc');
            const form = wrapper.closest('form');
            const url = form ? form.action : `/Home/UpdateCartQuantity/${cartId}`;

            if (isNaN(newQty) || newQty < 1) {
                newQty = 1;
            }
            if (newQty > maxStock) {
                alert(`Số lượng trong kho chỉ còn tối đa ${maxStock} sản phẩm.`);
                newQty = maxStock;
            }

            input.value = newQty;
            btnDec.disabled = (newQty <= 1);
            btnInc.disabled = (newQty >= maxStock);
            wrapper.classList.add('loading');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({ quantity: newQty })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Lỗi cập nhật số lượng');
                    });
                }
                return response.json();
            })
            .then(data => {
                wrapper.classList.remove('loading');
                if (data.success) {
                    input.value = data.quantity;
                    btnDec.disabled = (data.quantity <= 1);
                    if (data.maxStock) {
                        btnInc.disabled = (data.quantity >= data.maxStock);
                    }

                    const rowTotal = document.querySelector(`.item-total-cell[data-cart-id="${cartId}"]`);
                    if (rowTotal && data.itemTotalFormatted) {
                        rowTotal.textContent = data.itemTotalFormatted;
                    }

                    const cartTotalEl = document.getElementById('cart-total-display');
                    if (cartTotalEl && data.cartTotalFormatted) {
                        cartTotalEl.textContent = data.cartTotalFormatted;
                    }

                    if (data.cartCount !== undefined) {
                        document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.cartCount);
                        document.querySelectorAll('.cart-count-text').forEach(el => el.textContent = data.cartCount);
                    }
                }
            })
            .catch(err => {
                wrapper.classList.remove('loading');
                alert(err.message || 'Không thể cập nhật số lượng. Vui lòng thử lại.');
                location.reload();
            });
        }

        document.querySelectorAll('.cart-qty-wrapper').forEach(wrapper => {
            const btnDec = wrapper.querySelector('.btn-qty-dec');
            const btnInc = wrapper.querySelector('.btn-qty-inc');
            const input = wrapper.querySelector('.input-qty');

            btnDec?.addEventListener('click', function (e) {
                e.preventDefault();
                let current = parseInt(input.value) || 1;
                if (current > 1) {
                    updateQuantity(wrapper, current - 1);
                }
            });

            btnInc?.addEventListener('click', function (e) {
                e.preventDefault();
                let current = parseInt(input.value) || 1;
                const maxStock = parseInt(wrapper.dataset.maxStock) || 999;
                if (current < maxStock) {
                    updateQuantity(wrapper, current + 1);
                } else {
                    alert(`Số lượng trong kho chỉ còn tối đa ${maxStock} sản phẩm.`);
                }
            });

            input?.addEventListener('change', function () {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) {
                    val = 1;
                    this.value = 1;
                }
                updateQuantity(wrapper, val);
            });

            input?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.blur();
                }
            });
        });
    });
</script>
@endsection
