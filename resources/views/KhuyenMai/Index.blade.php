@extends('layouts.app')

@section('title', 'Khuyến mãi - Cửa Hàng Cầu Lông')

@section('styles')
<link href="{{ asset('css/landing.css') }}" rel="stylesheet" />
<link href="{{ asset('css/promo.css') }}" rel="stylesheet" />
@endsection

@section('full')
@php
    $hamTien = fn ($v) => number_format((float) $v, 0, ',', '.') . '₫';
    $phanTramGiam = fn ($sp) => ($sp->GiaGoc > 0 && $sp->gia_sau_khuyen_mai < $sp->GiaGoc)
        ? max(1, round((1 - $sp->gia_sau_khuyen_mai / $sp->GiaGoc) * 100)) : 0;

    $cacCt = $promotions->filter(fn ($km) => ($promoProducts[$km->MaKhuyenMai] ?? collect())->isNotEmpty())->values();

    $noiBat = $products->sortByDesc($phanTramGiam)->unique('MaLoai')->take(3);
    if ($noiBat->count() < 3) {
        $noiBat = $noiBat->concat($products->sortByDesc($phanTramGiam)->whereNotIn('MaSanPham', $noiBat->pluck('MaSanPham')))->take(3);
    }
    $noiBat = $noiBat->values();
    $giamCaoNhat = (int) $products->map($phanTramGiam)->max();
@endphp


<section class="l-hero promo-hero bg-court">
    <div class="container l-hero-inner">
        <div class="l-hero-copy">
            <span class="l-kicker light">Khuyến mãi</span>
            <h1>Ưu đãi đang diễn ra,<br><em>săn ngay kẻo lỡ.</em></h1>
            <p>Các chương trình giảm giá cho vợt, giày, trang phục và phụ kiện chính hãng. Giá bên dưới đã trừ khuyến mãi.</p>

            <dl class="l-stats">
                <div><dt>{{ $cacCt->count() }}</dt><dd>Chương trình</dd></div>
                <div><dt>{{ $products->count() }}</dt><dd>Sản phẩm giảm giá</dd></div>
                <div><dt>{{ $giamCaoNhat }}%</dt><dd>Giảm cao nhất</dd></div>
            </dl>
        </div>

        <div class="l-hero-visual">
            @foreach ($noiBat as $sp)
                @php $pt = $phanTramGiam($sp); @endphp
                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="l-hero-card c{{ $loop->iteration }}">
                    @if ($pt)<span class="badge-promo">-{{ $pt }}%</span>@endif
                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" width="320" height="320"
                         @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                    <div class="l-hero-card-info">
                        <small>{{ $sp->hang->TenHang ?? 'Hãng khác' }}</small>
                        <b>{{ $sp->TenSanPham }}</b>
                        <span>{{ $hamTien($sp->gia_sau_khuyen_mai) }}</span>
                    </div>
                </a>
            @endforeach
            <span class="l-hero-chip chip-a"><i class="fa-solid fa-fire"></i> Giảm đến {{ $giamCaoNhat }}%</span>
        </div>
    </div>
</section>

@if ($cacCt->isEmpty())
    <div class="container py-5">
        @include('partials._empty', ['message' => 'Hiện chưa có chương trình khuyến mãi nào đang diễn ra.', 'linkUrl' => route('sanpham.index'), 'linkText' => 'Xem tất cả sản phẩm', 'compact' => false])
    </div>
@else
    
    <nav class="promo-nav" aria-label="Chương trình khuyến mãi">
        <div class="container">
            @foreach ($cacCt as $km)
                <a href="#promo-{{ $km->MaKhuyenMai }}"><b>-{{ round($km->PhanTramGiam) }}%</b> {{ $km->TenChuongTrinh }}</a>
            @endforeach
        </div>
    </nav>

    
    @foreach ($cacCt as $km)
        @php
            $sps = $promoProducts[$km->MaKhuyenMai];
            $ketThuc = $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc)->endOfDay() : null;
            $conLai = $ketThuc ? (int) now()->startOfDay()->diffInDays($ketThuc->copy()->startOfDay(), false) : null;
            $sapHet = $conLai !== null && $conLai <= 7;
        @endphp
        <section class="promo-block" id="promo-{{ $km->MaKhuyenMai }}">
            <div class="container">
                <header class="promo-head">
                    <div class="promo-meta">
                        <h2>{{ $km->TenChuongTrinh }}</h2>
                        @if ($km->MoTa)<p>{{ $km->MoTa }}</p>@endif
                        <div class="promo-chips">
                            <span><i class="fa-regular fa-calendar"></i>
                                {{ $km->NgayBatDau ? \Carbon\Carbon::parse($km->NgayBatDau)->format('d/m/Y') : 'Đang áp dụng' }}
                                – {{ $km->NgayKetThuc ? \Carbon\Carbon::parse($km->NgayKetThuc)->format('d/m/Y') : 'không giới hạn' }}
                            </span>
                            @if ($conLai !== null)
                                <span class="{{ $sapHet ? 'is-hot' : 'is-ok' }}">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $conLai > 0 ? 'Còn ' . $conLai . ' ngày' : ($conLai === 0 ? 'Kết thúc hôm nay' : 'Đã kết thúc') }}
                                </span>
                            @endif
                            <span><i class="fa-solid fa-box-open"></i> {{ $sps->count() }} sản phẩm</span>
                        </div>
                    </div>
                </header>

                @if ($sps->count() > 4)
                    <div class="promo-slider-container">
                        <button type="button" class="promo-slider-btn prev" aria-label="Sản phẩm trước"><i class="fa-solid fa-chevron-left"></i></button>
                        <div class="promo-slider-track-wrap">
                            <div class="promo-slider-track">
                                @foreach ($sps as $sp)
                                    @php
                                        $giaGoc = (float) ($sp->GiaGoc ?? 0);
                                        $giaBan = (float) $sp->gia_sau_khuyen_mai;
                                        $pt = $phanTramGiam($sp);
                                        $tietKiem = max(0, $giaGoc - $giaBan);
                                    @endphp
                                    <div class="product-card">
                                        @if ($pt)<div class="badge-promo">-{{ $pt }}%</div>@endif
                                        <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="thumb">
                                            <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" loading="lazy" decoding="async"
                                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                                        </a>
                                        <div class="body">
                                            <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                                            <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">{{ $sp->TenSanPham }}</a>
                                            <div class="product-footer">
                                                <span class="product-price">{{ $hamTien($giaBan) }}</span>
                                                @if ($tietKiem > 0)
                                                    <span class="price-small">{{ $hamTien($giaGoc) }}</span>
                                                @endif
                                            </div>
                                            @if ($tietKiem > 0)
                                                <div class="promo-save"><i class="fa-solid fa-piggy-bank"></i> Tiết kiệm {{ $hamTien($tietKiem) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="promo-slider-btn next" aria-label="Sản phẩm tiếp theo"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>
                @else
                    <div class="promo-grid">
                        @foreach ($sps as $sp)
                            @php
                                $giaGoc = (float) ($sp->GiaGoc ?? 0);
                                $giaBan = (float) $sp->gia_sau_khuyen_mai;
                                $pt = $phanTramGiam($sp);
                                $tietKiem = max(0, $giaGoc - $giaBan);
                            @endphp
                            <div class="product-card">
                                @if ($pt)<div class="badge-promo">-{{ $pt }}%</div>@endif
                                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="thumb">
                                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" loading="lazy" decoding="async"
                                         onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                                </a>
                                <div class="body">
                                    <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                                    <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">{{ $sp->TenSanPham }}</a>
                                    <div class="product-footer">
                                        <span class="product-price">{{ $hamTien($giaBan) }}</span>
                                        @if ($tietKiem > 0)
                                            <span class="price-small">{{ $hamTien($giaGoc) }}</span>
                                        @endif
                                    </div>
                                    @if ($tietKiem > 0)
                                        <div class="promo-save"><i class="fa-solid fa-piggy-bank"></i> Tiết kiệm {{ $hamTien($tietKiem) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endforeach

    <section class="l-final bg-court">
        <div class="container text-center">
            <h2>Chưa tìm thấy món ưng ý?</h2>
            <p>Lọc theo ngân sách, hãng và tình trạng còn hàng để tìm nhanh hơn.</p>
            <div class="l-cta justify-content-center">
                <a href="{{ route('sanpham.index', ['giam_gia' => 1]) }}" class="btn btn-light btn-lg">Lọc sản phẩm đang giảm giá</a>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">Về trang chủ</a>
            </div>
        </div>
    </section>
@endif

@endsection
