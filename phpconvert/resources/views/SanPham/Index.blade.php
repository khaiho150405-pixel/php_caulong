@extends('layouts.app')

@section('title', $title ?? 'Danh sách sản phẩm - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .product-list {
        margin-top: 25px;
        margin-bottom: 40px;
    }

    .product-card {
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
        transition: transform .2s ease, box-shadow .2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12);
    }

    .card-img-top {
        width: 100%;
        height: 200px;
        object-fit: contain;
        background: #fafafa;
        padding: 15px;
        display: block;
    }

    .card-body {
        padding: 15px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        gap: 8px;
    }

    .product-title {
        font-size: 15px;
        font-weight: 600;
        min-height: 40px;
        line-height: 1.3;
        margin-bottom: 4px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #222;
        text-decoration: none;
    }

    .product-title:hover {
        color: #f36f21;
    }

    .product-brand {
        font-size: 13px;
        color: #777;
    }

    .product-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .product-price {
        color: #e65100;
        font-weight: 700;
        font-size: 17px;
    }

    .price-small {
        font-size: 13px;
        color: #999;
        text-decoration: line-through;
        margin-left: 6px;
    }

    .badge-promo {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #f36f21;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        z-index: 2;
    }

    .rating-small {
        color: #ffc107;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<div class="container product-list">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 24px; font-weight: 700; color: #222;">
            {{ $title }}
            @if(isset($sanPhams))
                <span style="font-size: 16px; color: #777; font-weight: normal;">({{ $sanPhams->count() }} sản phẩm)</span>
            @endif
        </h2>
    </div>

    @if ($sanPhams->isEmpty())
        <div class="alert alert-info">
            Không tìm thấy sản phẩm nào phù hợp. <a href="{{ route('home') }}" class="alert-link">Quay lại trang chủ</a>.
        </div>
    @else
        <div class="row">
            @foreach ($sanPhams as $sp)
                @php
                    $giaGoc = (float)($sp->GiaGoc ?? 0);
                    $giaBan = (float)$sp->gia_sau_khuyen_mai;
                    $km = $sp->khuyenMai;
                    $hasDiscount = $km && $giaBan < $giaGoc;
                    $img = $sp->HinhAnhDaiDien ?: 'default-product.jpg';
                    $avgRating = $sp->trung_binh_danh_gia;
                @endphp
                <div class="col-xs-6 col-sm-4 col-md-3" style="margin-bottom: 25px;">
                    <div class="product-card">
                        @if ($hasDiscount)
                            <div class="badge-promo">-{{ round($km->PhanTramGiam) }}%</div>
                        @endif

                        <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}">
                            <img src="{{ asset('images/' . $img) }}"
                                 alt="{{ $sp->TenSanPham }}"
                                 class="card-img-top"
                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                        </a>

                        <div class="card-body">
                            <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                            <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">
                                {{ $sp->TenSanPham }}
                            </a>

                            <div class="rating-small">
                                @for ($i = 0; $i < 5; $i++)
                                    @if ($i < round($avgRating))
                                        <span>★</span>
                                    @else
                                        <span style="color:#ddd">★</span>
                                    @endif
                                @endfor
                                <span style="color: #888; font-size: 11px;">({{ number_format($avgRating, 1) }})</span>
                            </div>

                            <div class="product-footer">
                                <div>
                                    <span class="product-price">{{ number_format($giaBan, 0, ',', '.') }} đ</span>
                                    @if ($hasDiscount)
                                        <span class="price-small">{{ number_format($giaGoc, 0, ',', '.') }} đ</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
