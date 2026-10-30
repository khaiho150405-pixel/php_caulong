@extends('layouts.app')

@section('title', $sanPham->TenSanPham . ' - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .product-container {
        display: flex;
        max-width: 1200px;
        margin: 30px auto;
        gap: 40px;
        padding: 12px 28px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius-lg);
    }

    .product-container h1 {
        font-size: 28px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 10px;
    }

    .media-column {
        flex: 1.5;
        padding: 20px 0;
        text-align: center;
    }

    .info-column {
        flex: 1;
        padding: 20px 0;
    }

    .main-image-box {
        position: relative;
        padding-bottom: 75%;
        height: 0;
        overflow: hidden;
        margin-bottom: 20px;
        border: 1px solid var(--line);
        border-radius: var(--radius);
    }

    .main-image-box img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background-color: #f6f8f7;
    }

    .rating {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        color: #666;
        font-size: 14px;
    }

    .rating-stars {
        color: var(--star);
        font-size: 18px;
    }

    .price-box {
        padding: 15px 20px;
        background-color: var(--accent-50);
        border-radius: var(--radius);
        margin: 20px 0;
        display: flex;
        align-items: center;
    }

    .price {
        font-size: 32px;
        color: var(--price);
        font-weight: 700;
    }

    .original-price {
        text-decoration: line-through;
        color: #9e9e9e;
        margin-left: 15px;
        font-size: 20px;
        font-weight: 400;
    }

    .discount-badge {
        background-color: var(--accent);
        color: white;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 14px;
        margin-left: 15px;
        font-weight: bold;
    }

    .option-selection h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .option-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .option-group button {
        border: 1px solid var(--line-strong);
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        cursor: pointer;
        background-color: #fff;
        transition: all 0.2s;
    }

    .option-group button:hover {
        border-color: var(--brand);
    }

    .option-group button.selected {
        border-color: var(--brand);
        background-color: var(--brand-50);
        font-weight: 600;
        color: var(--brand);
    }

    .btn-action {
        padding: 15px 30px;
        font-size: 18px;
        margin-right: 15px;
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s;
    }

    .btn-add-cart {
        background-color: #fff;
        border: 2px solid var(--brand);
        color: var(--brand);
    }

    .btn-add-cart:hover {
        background-color: var(--brand);
        color: #fff;
    }

    .btn-buy-now {
        background-color: var(--brand);
        color: #fff;
    }

    .btn-buy-now:hover {
        background-color: var(--brand-700);
    }

    .quantity-control {
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .quantity-control input {
        text-align: center;
        padding: 5px;
        border: 1px solid var(--line-strong);
        border-radius: var(--radius-sm);
        width: 64px;
    }

    .racket-specs-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 15px;
        border: 1px solid var(--line);
    }

    .racket-specs-table th, .racket-specs-table td {
        padding: 12px;
        border: none;
        border-bottom: 1px solid var(--line);
        text-align: left;
    }

    .racket-specs-table th {
        background-color: #f6f8f7;
        font-weight: 600;
        width: 40%;
        color: var(--ink-soft);
    }

    .btn-review-action {
        display: inline-block;
        background-color: var(--brand);
        color: #fff;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        font-weight: 600;
        cursor: pointer;
        font-size: 15px;
        margin-top: 15px;
        text-align: center;
        text-decoration: none;
    }

    .btn-review-action:hover {
        background-color: var(--brand-700);
        color: #fff;
    }

    .review-list {
        margin-top: 20px;
    }

    .review-box {
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 16px;
        margin-bottom: 12px;
        background-color: #fafcfb;
    }

    .review-box strong {
        display: block;
        margin-bottom: 5px;
        font-size: 15px;
    }

    .review-box p {
        margin: 5px 0;
    }

    .review-box small {
        color: #999;
        font-size: 12px;
    }

    .btn-action:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        border-color: #ccc;
        color: #999;
    }
</style>
@endsection

@section('content')
@php
    $defaultVariant = $variants->first();
    $originalPrice = $defaultVariant ? ($defaultVariant->GiaBan ?? $sanPham->GiaGoc ?? 0) : ($sanPham->GiaGoc ?? 0);
    $defaultChiTietId = $defaultVariant ? $defaultVariant->MaChiTiet : 0;
    $mainImageUrl = asset('images/' . ($sanPham->HinhAnhDaiDien ?: 'default-product.jpg'));
    $isRacket = ($sanPham->MaLoai == 1) || (!empty($thongSoVot)) || (isset($sanPham->loaiSanPham) && str_contains(mb_strtolower($sanPham->loaiSanPham->TenLoai), 'vợt'));

    $discountPercent = 0;
    $maxDiscount = 0;
    $isActivePromo = false;
    $km = $sanPham->khuyenMai;

    if ($km) {
        $now = now();
        $start = $km->NgayBatDau ? \Carbon\Carbon::parse($km->NgayBatDau) : null;
        $end = $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc) : null;

        if ($end && $end->isStartOfDay()) {
            $end = $end->endOfDay();
        }

        if (($start === null || $start <= $now) && ($end === null || $end >= $now)) {
            $isActivePromo = true;
            $discountPercent = (float)($km->PhanTramGiam ?? 0);
            $maxDiscount = (float)($km->GiamToiDa ?? 0);
        }
    }

    $discountAmount = 0;
    $finalPrice = $originalPrice;
    if ($isActivePromo && $discountPercent > 0) {
        $discountAmount = $originalPrice * ($discountPercent / 100);
        if ($maxDiscount > 0 && $discountAmount > $maxDiscount) {
            $discountAmount = $maxDiscount;
        }
        $finalPrice = max(0, $originalPrice - $discountAmount);
    }
@endphp

<div class="product-container">
    <div class="media-column">
        <div class="main-image-box">
            <img id="main-product-image" src="{{ $mainImageUrl }}" alt="{{ $sanPham->TenSanPham }}" onerror="this.src='{{ asset('images/default-product.jpg') }}'">
        </div>
    </div>

    <div class="info-column">
        <h1>{{ $sanPham->TenSanPham }}</h1>
        <hr />

        <div class="rating">
            <span class="rating-stars">
                @for ($i = 0; $i < 5; $i++)
                    @if ($i < round($averageRating))
                        <span>★</span>
                    @else
                        <span style="color:#ddd">★</span>
                    @endif
                @endfor
            </span>
            <span>{{ number_format($averageRating, 1) }} sao</span>
            <span>| {{ $reviewCount }} đánh giá</span>
        </div>

        @auth
            <a href="{{ route('themphanhoisp.reviewform', ['maSP' => $sanPham->MaSanPham]) }}" class="btn-review-action">
                <i class="fa fa-pen"></i> Gửi đánh giá của bạn
            </a>
        @else
            <p style="margin-top:15px; color:var(--brand);">
                <a href="{{ route('taikhoan.dangnhap', ['returnUrl' => url()->current()]) }}" style="color:var(--brand); font-weight:bold; text-decoration:underline;">Đăng nhập</a> để gửi đánh giá.
            </p>
        @endauth

        <div class="price-box">
            <span class="price" id="display-price">
                @if ($isActivePromo && $discountAmount > 0)
                    {{ number_format($finalPrice, 0, ',', '.') }}đ <span class="original-price">{{ number_format($originalPrice, 0, ',', '.') }}đ</span>
                @else
                    {{ number_format($originalPrice, 0, ',', '.') }}đ
                @endif
            </span>

            @if ($isActivePromo && $discountAmount > 0)
                <span class="discount-badge" id="discount-badge">
                    @if ($maxDiscount > 0 && $discountAmount >= $maxDiscount)
                        -{{ number_format($maxDiscount, 0, ',', '.') }}đ (Max)
                    @else
                        -{{ round($discountPercent) }}%
                    @endif
                </span>
            @endif
        </div>

        @if(!empty($sanPham->MoTa))
            <div style="margin: 15px 0; color: #555; line-height: 1.6;">
                <p>{{ $sanPham->MoTa }}</p>
            </div>
        @endif

        <hr>

        <form action="{{ route('home.addtocart') }}" method="POST" id="addToCartForm">
            @csrf
            <input type="hidden" id="selected-chitiet-id" name="chiTietId" value="{{ $defaultChiTietId }}">

            @if ($variants->isEmpty())
                <div style="margin: 15px 0; padding: 12px 16px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; border-radius: var(--radius-sm);">
                    <i class="fa fa-info-circle"></i> Sản phẩm đang cập nhật thông tin phiên bản và số lượng tồn kho.
                </div>
            @endif

            @if ($availableColors->isNotEmpty())
                <div class="option-selection">
                    <h4>Chọn Màu Sắc:</h4>
                    <div class="option-group" id="color-options" data-option="color">
                        @foreach ($availableColors as $color)
                            <button type="button" data-color-id="{{ $color->MaMau }}" data-value="{{ $color->TenMau }}">{{ $color->TenMau }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="colorOption" id="input-color-id" />
                </div>
            @endif

            @if ($availableSizes->isNotEmpty())
                <div class="option-selection">
                    <h4>Chọn Size/Phiên bản:</h4>
                    <div class="option-group" id="weight-options" data-option="size">
                        @foreach ($availableSizes as $size)
                            <button type="button" data-size-id="{{ $size->MaSize }}" data-value="{{ $size->TenSize }}">{{ $size->TenSize }}</button>
                        @endforeach
                    </div>
                    <input type="hidden" name="sizeOption" id="input-size-id" />
                </div>
            @endif

            <div class="quantity-control">
                <span>Số lượng:</span>
                <input type="number" name="quantity" id="quantity-input" value="1" min="1" max="10">
                <span id="stock-status" style="margin-left: 15px; font-weight:bold;">Đang kiểm tra...</span>
            </div>

            <div class="product-actions-wrap" style="margin-top: 25px; display: flex; gap: 12px; flex-wrap: wrap;">
                <button type="submit" class="btn-action btn-add-cart" id="btn-add-cart">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
                <button type="submit" class="btn-action btn-buy-now" id="btn-buy-now">
                    <i class="fa-solid fa-bolt"></i> Mua ngay
                </button>
            </div>
        </form>

        <hr style="margin-top: 30px; margin-bottom: 30px;" />

        @if ($isRacket && ($thongSoVot || $variants->isNotEmpty()))
            <h3>Thông số chi tiết Vợt</h3>
            <table class="racket-specs-table">
                <tbody>
                    <tr><th>Hãng</th><td id="spec-hang">{{ $sanPham->hang->TenHang ?? 'N/A' }}</td></tr>
                    <tr><th>Độ cứng</th><td id="spec-do-cung">{{ $thongSoVot->DoCung ?? 'Đang cập nhật' }}</td></tr>
                    <tr><th>Điểm cân bằng</th><td id="spec-diem-can-bang">{{ $thongSoVot->DoCanBang ?? 'Đang cập nhật' }}</td></tr>
                    <tr><th>Sức căng</th><td id="spec-suc-cang">{{ $thongSoVot->SucCang ?? 'Đang cập nhật' }}</td></tr>
                    <tr><th>Trọng lượng</th><td id="spec-trong-luong">{{ $thongSoVot->TrongLuong ?? 'Đang cập nhật' }}</td></tr>
                    <tr><th>Chiều dài</th><td id="spec-chieu-dai">{{ $thongSoVot->ChieuDai ?? 'Đang cập nhật' }}</td></tr>
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="product-container" style="display: block; margin-top: 0;">
    <hr />
    <div class="review-list">
        <h3>Phản hồi của khách hàng ({{ $reviewCount }})</h3>
        @if ($reviews->isEmpty())
            <p>Chưa có phản hồi nào.</p>
        @else
            @foreach ($reviews as $ph)
                <div class="review-box">
                    <strong>{{ $ph->khachHang->HoTen ?? 'Khách hàng ẩn danh' }} ({{ $ph->DanhGia }} sao)</strong>
                    <p>{{ $ph->NoiDung }}</p>
                    <small>{{ $ph->NgayPhanHoi ? \Carbon\Carbon::parse($ph->NgayPhanHoi)->format('d/m/Y H:i') : '' }}</small>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

@section('scripts')
@php
    $variantsData = $variants->map(function($v) use ($sanPham) {
        $firstSpec = $v->thongSoVots->first();
        return [
            'MaChiTiet' => $v->MaChiTiet,
            'MaMau' => $v->MaMau,
            'MaSize' => $v->MaSize,
            'GiaBan' => (float)($v->GiaBan ?? $sanPham->GiaGoc ?? 0),
            'SoLuongTon' => (int)($v->SoLuongTon ?? 0),
            'HinhAnh' => $v->HinhAnh,
            'DoCung' => $firstSpec ? $firstSpec->DoCung : '',
            'DoCanBang' => $firstSpec ? $firstSpec->DoCanBang : '',
            'SucCang' => $firstSpec ? $firstSpec->SucCang : '',
            'TrongLuong' => $firstSpec ? $firstSpec->TrongLuong : '',
            'ChieuDai' => $firstSpec ? $firstSpec->ChieuDai : '',
        ];
    })->values();

    $firstColorId = $availableColors->isNotEmpty() ? $availableColors->first()->MaMau : null;
    $firstSizeId = $availableSizes->isNotEmpty() ? $availableSizes->first()->MaSize : null;
@endphp
<script>
    const allVariants = {!! json_encode($variantsData) !!};
    let selectedColorId = {!! json_encode($firstColorId) !!};
    let selectedSizeId = {!! json_encode($firstSizeId) !!};

    const promotionData = {
        isActive: {{ $isActivePromo ? 'true' : 'false' }},
        percent: {{ (float)$discountPercent }},
        maxDiscount: {{ (float)$maxDiscount }}
    };

    const isRacket = {{ $isRacket ? 'true' : 'false' }};

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    };

    function updateProductState() {
        const stockElement = document.getElementById('stock-status');
        const hiddenIdInput = document.getElementById('selected-chitiet-id');
        const priceElement = document.getElementById('display-price');
        const mainProductImage = document.getElementById('main-product-image');
        const badgeElement = document.getElementById('discount-badge');
        const btnAddCart = document.getElementById('btn-add-cart');
        const btnBuyNow = document.getElementById('btn-buy-now');
        const qtyInput = document.getElementById('quantity-input');

        if (allVariants.length === 0) {
            if (stockElement) {
                stockElement.textContent = 'Sản phẩm tạm hết hàng / Đang cập nhật';
                stockElement.style.color = '#c9830f';
            }
            if (btnAddCart) {
                btnAddCart.disabled = true;
                btnAddCart.innerHTML = '<i class="fas fa-box-open"></i> Tạm hết hàng';
            }
            if (btnBuyNow) {
                btnBuyNow.disabled = true;
                btnBuyNow.innerHTML = '<i class="fas fa-ban"></i> Chưa thể mua';
            }
            if (hiddenIdInput) {
                hiddenIdInput.value = "";
            }
            return;
        }

        let variant = allVariants.find(v =>
            (!selectedColorId || v.MaMau === selectedColorId) &&
            (!selectedSizeId || v.MaSize === selectedSizeId)
        );

        if (!variant && selectedColorId) {
            const alt = allVariants.find(v => v.MaMau === selectedColorId);
            if (alt && alt.MaSize) {
                selectedSizeId = alt.MaSize;
                const sizeBtn = document.querySelector(`#weight-options button[data-size-id="${selectedSizeId}"]`);
                if (sizeBtn) {
                    document.querySelectorAll('#weight-options button').forEach(b => b.classList.remove('selected'));
                    sizeBtn.classList.add('selected');
                }
                const inputSize = document.getElementById('input-size-id');
                if (inputSize) inputSize.value = selectedSizeId;
                variant = alt;
            }
        }

        if (!variant && selectedSizeId) {
            const alt = allVariants.find(v => v.MaSize === selectedSizeId);
            if (alt && alt.MaMau) {
                selectedColorId = alt.MaMau;
                const colorBtn = document.querySelector(`#color-options button[data-color-id="${selectedColorId}"]`);
                if (colorBtn) {
                    document.querySelectorAll('#color-options button').forEach(b => b.classList.remove('selected'));
                    colorBtn.classList.add('selected');
                }
                const inputColor = document.getElementById('input-color-id');
                if (inputColor) inputColor.value = selectedColorId;
                variant = alt;
            }
        }

        if (!variant && allVariants.length > 0 && !selectedColorId && !selectedSizeId) {
            variant = allVariants[0];
        }

        if (variant) {
            if (variant.SoLuongTon > 0) {
                stockElement.textContent = `Còn hàng (${variant.SoLuongTon})`;
                stockElement.style.color = 'green';
                btnAddCart.disabled = false;
                btnAddCart.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
                if (btnBuyNow) {
                    btnBuyNow.disabled = false;
                    btnBuyNow.innerHTML = '<i class="fa-solid fa-bolt"></i> Mua ngay';
                }
                if (qtyInput) {
                    qtyInput.max = variant.SoLuongTon;
                }
            } else {
                stockElement.textContent = 'Hết hàng';
                stockElement.style.color = 'red';
                btnAddCart.disabled = true;
                btnAddCart.innerHTML = '<i class="fas fa-times-circle"></i> Hết hàng';
                if (btnBuyNow) {
                    btnBuyNow.disabled = true;
                    btnBuyNow.innerHTML = '<i class="fas fa-times-circle"></i> Hết hàng';
                }
            }

            hiddenIdInput.value = variant.MaChiTiet;

            let original = variant.GiaBan;
            let finalPrice = original;
            let displayPriceHtml = formatCurrency(original);

            if (promotionData.isActive && promotionData.percent > 0) {
                let discountAmount = original * (promotionData.percent / 100);
                let isCapped = false;
                if (promotionData.maxDiscount > 0 && discountAmount > promotionData.maxDiscount) {
                    discountAmount = promotionData.maxDiscount;
                    isCapped = true;
                }

                finalPrice = Math.max(0, original - discountAmount);
                displayPriceHtml = `${formatCurrency(finalPrice)} <span class="original-price">${formatCurrency(original)}</span>`;

                if (badgeElement) {
                    badgeElement.style.display = 'inline-block';
                    if (isCapped) {
                        badgeElement.textContent = `-${formatCurrency(discountAmount)} (Max)`;
                    } else {
                        badgeElement.textContent = `-${promotionData.percent}%`;
                    }
                }
            } else {
                if (badgeElement) badgeElement.style.display = 'none';
            }

            if (priceElement) {
                priceElement.innerHTML = displayPriceHtml;
            }

            if (variant.HinhAnh && mainProductImage) {
                mainProductImage.src = "/images/" + variant.HinhAnh;
            }

            if (isRacket) {
                if (document.getElementById('spec-do-cung') && variant.DoCung) {
                    document.getElementById('spec-do-cung').textContent = variant.DoCung;
                }
                if (document.getElementById('spec-diem-can-bang') && variant.DoCanBang) {
                    document.getElementById('spec-diem-can-bang').textContent = variant.DoCanBang;
                }
                if (document.getElementById('spec-suc-cang') && variant.SucCang) {
                    document.getElementById('spec-suc-cang').textContent = variant.SucCang;
                }
                if (document.getElementById('spec-trong-luong') && variant.TrongLuong) {
                    document.getElementById('spec-trong-luong').textContent = variant.TrongLuong;
                }
                if (document.getElementById('spec-chieu-dai') && variant.ChieuDai) {
                    document.getElementById('spec-chieu-dai').textContent = variant.ChieuDai;
                }
            }

        } else {
            stockElement.textContent = 'Tùy chọn này tạm thời không có sẵn';
            stockElement.style.color = '#c9830f';
            btnAddCart.disabled = true;
            btnAddCart.innerHTML = '<i class="fas fa-ban"></i> Chưa có sẵn';
            if (btnBuyNow) {
                btnBuyNow.disabled = true;
                btnBuyNow.innerHTML = '<i class="fas fa-ban"></i> Chưa thể mua';
            }
            hiddenIdInput.value = "";
        }
    }

    document.querySelectorAll('.option-group button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const group = this.closest('.option-group');
            group.querySelectorAll('button').forEach(btn => btn.classList.remove('selected'));
            this.classList.add('selected');

            const optionType = group.dataset.option;
            if (optionType === 'color') {
                selectedColorId = parseInt(this.dataset.colorId);
                const el = document.getElementById('input-color-id');
                if (el) el.value = selectedColorId;
            } else if (optionType === 'size') {
                selectedSizeId = parseInt(this.dataset.sizeId);
                const el = document.getElementById('input-size-id');
                if (el) el.value = selectedSizeId;
            }

            updateProductState();
        });
    });

    window.addEventListener('DOMContentLoaded', () => {
        const firstColor = document.querySelector('#color-options button');
        if (firstColor) {
            firstColor.classList.add('selected');
            selectedColorId = parseInt(firstColor.dataset.colorId);
            const el = document.getElementById('input-color-id');
            if (el) el.value = selectedColorId;
        }
        const firstSize = document.querySelector('#weight-options button');
        if (firstSize) {
            firstSize.classList.add('selected');
            selectedSizeId = parseInt(firstSize.dataset.sizeId);
            const el = document.getElementById('input-size-id');
            if (el) el.value = selectedSizeId;
        }
        updateProductState();
    });

    document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
        const chiTietId = document.getElementById('selected-chitiet-id')?.value;
        if (!chiTietId || chiTietId === '0') {
            e.preventDefault();
            alert('Vui lòng chọn đầy đủ phiên bản (màu sắc / kích cỡ) khả dụng trước khi đặt hàng.');
        }
    });
</script>
@endsection
