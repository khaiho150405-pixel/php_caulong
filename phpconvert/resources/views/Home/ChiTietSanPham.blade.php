@extends('layouts.app')

@section('title', $sanPham->TenSanPham . ' - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .product-container {
        display: flex;
        max-width: 1200px;
        margin: 30px auto;
        gap: 40px;
        padding: 0 15px;
        background: #fff;
        border-radius: 8px;
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
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .main-image-box img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background-color: #fcfcfc;
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
        color: #ffc107;
        font-size: 18px;
    }

    .price-box {
        padding: 15px 20px;
        background-color: #fef0e9;
        border-radius: 4px;
        margin: 20px 0;
        display: flex;
        align-items: center;
    }

    .price {
        font-size: 32px;
        color: #f36f21;
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
        background-color: #f36f21;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
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
        border: 1px solid #ddd;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        background-color: #fff;
        transition: all 0.2s;
    }

    .option-group button:hover {
        border-color: #f36f21;
    }

    .option-group button.selected {
        border-color: #f36f21;
        background-color: #fff4ed;
        font-weight: 600;
        color: #f36f21;
    }

    .btn-action {
        padding: 15px 30px;
        font-size: 18px;
        margin-right: 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-add-cart {
        background-color: #fff;
        border: 2px solid #f36f21;
        color: #f36f21;
    }

    .btn-add-cart:hover {
        background-color: #f36f21;
        color: #fff;
    }

    .btn-buy-now {
        background-color: #f36f21;
        color: #fff;
    }

    .btn-buy-now:hover {
        background-color: #d15d18;
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
        border: 1px solid #ddd;
        border-radius: 4px;
        width: 60px;
    }

    .racket-specs-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 15px;
        border: 1px solid #eee;
    }

    .racket-specs-table th, .racket-specs-table td {
        padding: 12px;
        border: none;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    .racket-specs-table th {
        background-color: #f9f9f9;
        font-weight: 600;
        width: 40%;
        color: #333;
    }

    .btn-review-action {
        display: inline-block;
        background-color: #28a745;
        color: #fff;
        padding: 10px 18px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 15px;
        margin-top: 15px;
        text-align: center;
        text-decoration: none;
    }

    .btn-review-action:hover {
        background-color: #218838;
        color: #fff;
    }

    .review-list {
        margin-top: 20px;
    }

    .review-box {
        border: 1px solid #f0f0f0;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 10px;
        background-color: #fcfcfc;
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
    $isRacket = !empty($thongSoVot);

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
            <p style="margin-top:15px; color:#f36f21;">
                <a href="{{ route('taikhoan.dangnhap', ['returnUrl' => url()->current()]) }}" style="color:#f36f21; font-weight:bold; text-decoration:underline;">Đăng nhập</a> để gửi đánh giá.
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

            <div style="margin-top: 25px;">
                <button type="submit" class="btn-action btn-add-cart" id="btn-add-cart">
                    <i class="fas fa-shopping-cart"></i> Thêm vào giỏ
                </button>
                <button type="button" class="btn-action btn-buy-now" onclick="document.getElementById('addToCartForm').submit();">
                    Mua ngay
                </button>
            </div>
        </form>

        <hr style="margin-top: 30px; margin-bottom: 30px;" />

        @if ($isRacket && $thongSoVot)
            <h3>Thông số chi tiết Vợt</h3>
            <table class="racket-specs-table">
                <tbody>
                    <tr><th>Hãng</th><td id="spec-hang">{{ $sanPham->hang->TenHang ?? 'N/A' }}</td></tr>
                    <tr><th>Độ cứng</th><td id="spec-do-cung">{{ $thongSoVot->DoCung ?? 'N/A' }}</td></tr>
                    <tr><th>Điểm cân bằng</th><td id="spec-diem-can-bang">{{ $thongSoVot->DoCanBang ?? 'N/A' }}</td></tr>
                    <tr><th>Sức căng</th><td id="spec-suc-cang">{{ $thongSoVot->SucCang ?? 'N/A' }}</td></tr>
                    <tr><th>Trọng lượng</th><td id="spec-trong-luong">{{ $thongSoVot->TrongLuong ?? 'N/A' }}</td></tr>
                    <tr><th>Chiều dài</th><td id="spec-chieu-dai">{{ $thongSoVot->ChieuDai ?? 'N/A' }}</td></tr>
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
        const variant = allVariants.find(v =>
            (!selectedColorId || v.MaMau === selectedColorId) &&
            (!selectedSizeId || v.MaSize === selectedSizeId)
        );

        const stockElement = document.getElementById('stock-status');
        const hiddenIdInput = document.getElementById('selected-chitiet-id');
        const priceElement = document.getElementById('display-price');
        const mainProductImage = document.getElementById('main-product-image');
        const badgeElement = document.getElementById('discount-badge');
        const btnAddCart = document.getElementById('btn-add-cart');
        const qtyInput = document.getElementById('quantity-input');

        if (variant) {
            if (variant.SoLuongTon > 0) {
                stockElement.textContent = `Còn hàng (${variant.SoLuongTon})`;
                stockElement.style.color = 'green';
                btnAddCart.disabled = false;
                btnAddCart.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
                qtyInput.max = variant.SoLuongTon;
            } else {
                stockElement.textContent = 'Hết hàng';
                stockElement.style.color = 'red';
                btnAddCart.disabled = true;
                btnAddCart.textContent = 'Hết hàng';
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
            priceElement.innerHTML = displayPriceHtml;

            if (variant.HinhAnh && mainProductImage) {
                mainProductImage.src = "/images/" + variant.HinhAnh;
            }

            if (isRacket) {
                if(document.getElementById('spec-do-cung')) document.getElementById('spec-do-cung').textContent = variant.DoCung || 'N/A';
                if(document.getElementById('spec-diem-can-bang')) document.getElementById('spec-diem-can-bang').textContent = variant.DoCanBang || 'N/A';
                if(document.getElementById('spec-suc-cang')) document.getElementById('spec-suc-cang').textContent = variant.SucCang || 'N/A';
                if(document.getElementById('spec-trong-luong')) document.getElementById('spec-trong-luong').textContent = variant.TrongLuong || 'N/A';
                if(document.getElementById('spec-chieu-dai')) document.getElementById('spec-chieu-dai').textContent = variant.ChieuDai || 'N/A';
            }

        } else {
            stockElement.textContent = 'Tùy chọn này không khả dụng';
            stockElement.style.color = 'orange';
            btnAddCart.disabled = true;
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
        }
        const firstSize = document.querySelector('#weight-options button');
        if (firstSize) {
            firstSize.classList.add('selected');
        }
        updateProductState();
    });
</script>
@endsection
