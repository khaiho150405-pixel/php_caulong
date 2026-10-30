@extends('layouts.app')

@section('title', 'Cửa Hàng Cầu Lông Online - Trang Chủ')

@section('styles')
<style>
    
    .section-head { margin: 0 0 22px; }
    .section-eyebrow {
        display: inline-block;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--brand);
        margin-bottom: 4px;
    }
    .section-head h2 { font-size: 1.5rem; font-weight: 700; margin: 0; }

    
    .cat-tiles {
        display: grid;
        grid-template-columns: repeat(var(--cols, 4), 1fr);
        gap: 18px;
    }
    .cat-tile {
        position: relative;
        display: block;
        aspect-ratio: 4 / 5;
        border-radius: var(--radius-lg);
        overflow: hidden;
        background: linear-gradient(135deg, var(--brand-900), var(--brand));
        color: #fff;
        isolation: isolate;
    }
    .cat-tile img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .5s ease;
        z-index: -2;
    }
    .cat-tile::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(11, 59, 43, 0) 35%, rgba(11, 59, 43, .88) 100%);
        z-index: -1;
    }
    .cat-tile:hover { color: #fff; }
    .cat-tile:hover img { transform: scale(1.06); }
    .cat-tile .cat-info {
        position: absolute;
        left: 0; right: 0; bottom: 0;
        padding: 18px 20px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 8px;
    }
    .cat-tile h3 { color: #fff; font-size: 1.15rem; font-weight: 700; margin: 0; }
    .cat-tile small { display: block; opacity: .8; font-size: .8rem; margin-top: 2px; }
    .cat-tile .go {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .16);
        display: grid;
        place-items: center;
        flex-shrink: 0;
        transition: background-color .2s, transform .2s;
    }
    .cat-tile:hover .go { background: var(--accent); transform: translateX(3px); }
    .cat-tile .tile-icon {
        position: absolute;
        top: 22%;
        left: 50%;
        transform: translateX(-50%);
        font-size: 3.2rem;
        opacity: .35;
    }

    
    .brand-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
    }
    .brand-chip {
        display: grid;
        place-items: center;
        height: 96px;
        padding: 18px 26px;
        overflow: hidden;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .brand-chip img {
        height: 46px;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        filter: grayscale(1);
        opacity: .75;
        transition: filter .25s, opacity .25s;
    }
    .brand-chip:hover { border-color: var(--brand-200); box-shadow: var(--shadow); transform: translateY(-2px); }
    .brand-chip:hover img { filter: none; opacity: 1; }
    .brand-chip .brand-text { color: #8a9892; font-weight: 800; font-size: 1.4rem; letter-spacing: .06em; text-transform: uppercase; }
    .brand-chip:hover .brand-text { color: var(--brand-700); }

    
    .category {
        display: flex;
        gap: 32px;
        align-items: stretch;
    }
    .category.left .image-block { order: 1; }
    .category.left .product-block { order: 2; }
    .category.right .image-block { order: 2; }
    .category.right .product-block { order: 1; }

    .image-block { flex: 0 0 300px; }
    .image-block img {
        width: 100%;
        height: 100%;
        min-height: 460px;
        border-radius: var(--radius-lg);
        object-fit: cover;
        border: 1px solid var(--line);
    }
    .product-block { flex: 1; min-width: 0; }
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line);
    }
    .header-row h2 { font-size: 1.4rem; font-weight: 700; margin: 0; }
    .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
    }
    .btn-xem-tat-ca {
        padding: 8px 16px;
        color: var(--brand-700);
        background: var(--surface);
        border: 1px solid var(--brand-200);
        border-radius: 999px;
        font-weight: 600;
        font-size: .85rem;
    }
    .btn-xem-tat-ca:hover { background: var(--brand); border-color: var(--brand); color: #fff; }

    @media (max-width: 992px) {
        .brand-strip { grid-template-columns: repeat(2, 1fr); }
        .cat-tiles { grid-template-columns: repeat(2, 1fr); }
        .category { flex-direction: column; }
        .image-block { flex-basis: auto; }
        .image-block img { min-height: 0; height: 220px; }
        .category .image-block { order: 1 !important; }
        .category .product-block { order: 2 !important; }
    }
    @media (max-width: 768px) {
        .grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .cat-tile { aspect-ratio: 1 / 1; }
    }
</style>
@endsection

@section('full')
    @php
        $groups = $sanPhams->groupBy('MaLoai');
        $categoryImageMap = [
            1 => 'yonex.png',
            2 => 'giay_lining.png',
            3 => 'aoquan.jpeg',
            4 => 'default-category.jpg',
        ];
        $categoryIconMap = [1 => 'fa-table-tennis-paddle-ball', 2 => 'fa-shoe-prints', 3 => 'fa-shirt', 4 => 'fa-bag-shopping'];
        $hasImage = fn ($f) => is_file(public_path('images/' . $f)) && filesize(public_path('images/' . $f)) > 0;
        $index = 0;
    @endphp

    
    <section class="band">
        <div class="container">
            @include('partials._banner')
        </div>
    </section>

    @if ($groups->isEmpty())
        <div class="container py-5">
            @include('partials._empty', ['message' => 'Chưa có sản phẩm nào để hiển thị.', 'compact' => false])
        </div>
    @else
        
        <section class="band band-tint bg-court">
            <div class="container">
                <div class="section-head">
                    <span class="section-eyebrow">Danh mục</span>
                    <h2>Mua sắm theo nhu cầu</h2>
                </div>
                <div class="cat-tiles" style="--cols: {{ min(4, max(1, $groups->count())) }}">
                    @foreach ($groups->take(4) as $maLoai => $items)
                        @php
                            $tenLoai = optional($items->first()->loaiSanPham)->TenLoai ?? 'Danh mục';
                            $file = $categoryImageMap[$maLoai] ?? 'default-category.jpg';
                        @endphp
                        <a class="cat-tile" href="{{ route('sanpham.index', ['loai' => $maLoai]) }}">
                            @if ($hasImage($file))
                                <img src="{{ asset('images/' . $file) }}" alt="{{ $tenLoai }}" loading="lazy">
                            @else
                                <i class="fa-solid {{ $categoryIconMap[$maLoai] ?? 'fa-bag-shopping' }} tile-icon"></i>
                            @endif
                            <div class="cat-info">
                                <div>
                                    <h3>{{ $tenLoai }}</h3>
                                    <small>{{ $items->count() }}+ sản phẩm</small>
                                </div>
                                <span class="go"><i class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        
        <section class="band">
            <div class="container">
                <div class="section-head text-center">
                    <span class="section-eyebrow">Thương hiệu</span>
                    <h2>Đồng hành cùng các hãng hàng đầu</h2>
                </div>
                <div class="brand-strip">
                    @foreach ([['Yonex', 'yonex'], ['Li-Ning', 'lining'], ['Victor', 'victor'], ['Mizuno', 'mizuno']] as [$tenHang, $slug])
                        @php
                            $logo = collect(['svg', 'png', 'webp', 'jpg'])
                                ->map(fn ($e) => "images/brands/{$slug}.{$e}")
                                ->first(fn ($f) => is_file(public_path($f)));
                        @endphp
                        <a class="brand-chip" href="{{ route('sanpham.index', ['hang' => $tenHang === 'Li-Ning' ? 'Lining' : $tenHang]) }}" title="{{ $tenHang }}">
                            @if ($logo)
                                <img src="{{ asset($logo) }}" alt="Logo {{ $tenHang }}" loading="lazy">
                            @else
                                <span class="brand-text">{{ $tenHang }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        
        @foreach ($groups as $maLoai => $productsInCat)
            @php
                $loai = $productsInCat->first()->loaiSanPham;
                $tenLoai = $loai ? $loai->TenLoai : 'Danh mục';
                $left = ($index % 2 == 0);
                $limitedProducts = $productsInCat->take(6);
                $hinhAnhCat = $categoryImageMap[$maLoai] ?? 'default-category.jpg';
            @endphp

            <section class="band {{ $index % 2 == 0 ? 'band-tint' : '' }}">
                <div class="container">
                    <div class="category {{ $left ? 'left' : 'right' }}">
                        @if ($hasImage($hinhAnhCat))
                            <div class="image-block">
                                <img src="{{ asset('images/' . $hinhAnhCat) }}" alt="Quảng cáo {{ $tenLoai }}" loading="lazy" />
                            </div>
                        @endif

                        <div class="product-block">
                            <div class="header-row">
                                <div>
                                    <span class="section-eyebrow">Nổi bật</span>
                                    <h2>{{ $tenLoai }}</h2>
                                </div>
                                <a href="{{ route('sanpham.index', ['loai' => $maLoai]) }}" class="btn-xem-tat-ca">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            </div>

                            <div class="grid">
                                @foreach ($limitedProducts as $sp)
                                    @php
                                        $giaGoc = (float)($sp->GiaGoc ?? 0);
                                        $giaBan = (float)$sp->gia_sau_khuyen_mai;
                                        $coKhuyenMai = ($giaBan < $giaGoc && $giaGoc > 0);
                                        $phanTramGiam = $coKhuyenMai ? max(1, round((1 - ($giaBan / $giaGoc)) * 100)) : 0;
                                    @endphp

                                    <div class="product-card">
                                        @if ($coKhuyenMai)
                                            <div class="badge-promo">-{{ $phanTramGiam }}%</div>
                                        @endif

                                        <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="thumb">
                                            <img src="{{ asset('images/' . $sp->anh_thumb) }}"
                                                 alt="{{ $sp->TenSanPham }}" loading="lazy"
                                                 onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                                        </a>

                                        <div class="body">
                                            <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                                            <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">{{ $sp->TenSanPham }}</a>
                                            <div class="product-footer">
                                                <span class="product-price">{{ number_format($giaBan, 0, ',', '.') }}₫</span>
                                                @if ($coKhuyenMai)
                                                    <span class="price-small">{{ number_format($giaGoc, 0, ',', '.') }}₫</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @php $index++; @endphp
        @endforeach
    @endif
@endsection
