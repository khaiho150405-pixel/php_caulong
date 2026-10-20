@extends('layouts.app')

@section('title', 'Sản phẩm khuyến mãi - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .promo-section {
        margin: 30px 0;
    }

    .promo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 10px 0;
        border-bottom: 3px solid #f0f0f0;
    }

    .promo-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #e51c23;
        position: relative;
        padding-left: 35px;
    }

    .promo-title::before {
        content: '🔥';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.4rem;
    }

    .promo-header small {
        font-size: 0.9rem;
        color: #757575;
        font-style: italic;
        margin-top: 5px;
        display: block;
    }

    .promo-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .promo-card {
        width: calc(25% - 12px);
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        position: relative;
        transition: transform .2s ease;
    }

    .promo-card:hover {
        transform: translateY(-4px);
    }

    .promo-card img {
        width: 100%;
        height: 200px;
        object-fit: contain;
        background: #fafafa;
        padding: 10px;
    }

    .promo-body {
        padding: 12px;
    }

    .promo-title-item {
        font-weight: 600;
        font-size: 14px;
        min-height: 44px;
        color: #333;
        text-decoration: none;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .promo-price {
        color: #e51c23;
        font-weight: 800;
        font-size: 16px;
    }

    .promo-old {
        text-decoration: line-through;
        color: #9e9e9e;
        margin-left: 8px;
        font-size: 13px;
    }

    .promo-badge {
        position: absolute;
        background: #c62828;
        color: #fff;
        padding: 5px 10px;
        border-radius: 12px;
        top: 10px;
        left: 10px;
        font-weight: 700;
        font-size: 12px;
        z-index: 2;
    }

    @media (max-width: 992px) {
        .promo-card { width: calc(33.333% - 12px); }
    }
    @media (max-width: 768px) {
        .promo-card { width: calc(50% - 12px); }
    }
    @media (max-width: 480px) {
        .promo-card { width: 100%; }
    }
</style>
@endsection

@section('content')
<div class="container" style="padding-top: 20px; padding-bottom: 40px;">
    <h2>🎁 Chương trình Khuyến Mãi</h2>
    <hr />

    @if ($promotions->isEmpty())
        <div class="alert alert-info">Hiện không có chương trình khuyến mãi nào đang diễn ra.</div>
    @else
        @php $hasAnyPromo = false; @endphp
        @foreach ($promotions as $km)
            @php
                $products = $promoProducts[$km->MaKhuyenMai] ?? collect();
            @endphp
            @if ($products->isNotEmpty())
                @php $hasAnyPromo = true; @endphp
                <section class="promo-section" id="promo-{{ $km->MaKhuyenMai }}">
                    <div class="promo-header">
                        <div>
                            <div class="promo-title">{{ $km->TenChuongTrinh }}</div>
                            <small>
                                Áp dụng: {{ $km->NgayBatDau ? \Carbon\Carbon::parse($km->NgayBatDau)->format('d/m/Y') : 'Bắt đầu' }} 
                                - {{ $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc)->format('d/m/Y') : 'Không giới hạn' }}
                            </small>
                        </div>
                    </div>

                    <div class="promo-grid">
                        @foreach ($products as $sp)
                            @php
                                $giaGoc = (float)($sp->GiaGoc ?? 0);
                                $phanTram = (float)($km->PhanTramGiam ?? 0);
                                $tienGiam = $giaGoc * ($phanTram / 100);
                                $giamToiDa = (float)($km->GiamToiDa ?? 0);
                                $badgeText = '-' . round($phanTram) . '%';
                                $noteGiamGia = '';

                                if ($giamToiDa > 0 && $tienGiam > $giamToiDa) {
                                    $tienGiam = $giamToiDa;
                                    $badgeText = '-' . number_format($tienGiam, 0, ',', '.') . 'đ (Max)';
                                    $noteGiamGia = '(Tối đa ' . number_format($giamToiDa, 0, ',', '.') . 'đ)';
                                }

                                $giaBanHienTai = max(0, $giaGoc - $tienGiam);
                                $img = $sp->HinhAnhDaiDien ?: 'default-product.jpg';
                            @endphp

                            <div class="promo-card">
                                @if ($tienGiam > 0)
                                    <div class="promo-badge">{{ $badgeText }}</div>
                                @endif

                                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}">
                                    <img src="{{ asset('images/' . $img) }}"
                                         alt="{{ $sp->TenSanPham }}"
                                         onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                                </a>

                                <div class="promo-body">
                                    <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="promo-title-item" title="{{ $sp->TenSanPham }}">
                                        {{ $sp->TenSanPham }}
                                    </a>

                                    <div class="mt-2" style="margin-top: 8px;">
                                        <span class="promo-price">{{ number_format($giaBanHienTai, 0, ',', '.') }} đ</span>
                                        @if ($tienGiam > 0)
                                            <span class="promo-old">{{ number_format($giaGoc, 0, ',', '.') }} đ</span>
                                        @endif
                                    </div>

                                    @if ($noteGiamGia)
                                        <small style="font-size: 11px; color: #d32f2f; display: block; margin-top: 4px;">{{ $noteGiamGia }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        @if (!$hasAnyPromo)
            <div class="alert alert-info">Hiện không có sản phẩm nào thuộc chương trình khuyến mãi.</div>
        @endif
    @endif
</div>
@endsection
