@extends('layouts.app')

@section('title', 'Cửa Hàng Cầu Lông Online - Trang Chủ')

@section('styles')
<style>
    .category {
        display: flex;
        gap: 40px;
        margin: 60px 0;
        align-items: center;
    }
    .category.left .image-block { order: 1; }
    .category.left .product-block { order: 2; }
    .category.right .image-block { order: 2; }
    .category.right .product-block { order: 1; }

    .image-block img {
        width: 360px;
        height: auto;
        border-radius: 12px;
        object-fit: cover;
    }
    .product-block {
        flex-grow: 1;
    }
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    .card {
        padding: 10px;
        height: 100%;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .card img {
        width: 100%;
        height: 220px;
        object-fit: contain;
        border-radius: 8px;
        margin-bottom: 5px;
        background: #f8f8f8;
    }
    .card h3 {
        font-size: 0.95em;
        margin: 5px 0;
        line-height: 1.2;
        min-height: 35px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: #333;
    }
    .card p {
        color: #ff5722;
        font-weight: bold;
        font-size: 1.15em;
        margin-top: 5px;
        margin-bottom: 0;
    }
    .card .old-price {
        text-decoration: line-through;
        color: #9e9e9e;
        font-weight: normal;
        margin-left: 8px;
        font-size: 0.85em;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    .btn-xem-tat-ca {
        padding: 8px 15px;
        background-color: #ff5722;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    }
    .btn-xem-tat-ca:hover {
        background-color: #e64a19;
        color: white;
    }
    .promo-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #d32f2f;
        color: #fff;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 11px;
        z-index: 2;
    }
</style>
@endsection

@section('content')
    @include('partials._banner')

    @php
        $groups = $sanPhams->groupBy('MaLoai');
        $categoryImageMap = [
            1 => 'yonex.png',
            2 => 'giay_lining.png',
            3 => 'aoquan.jpeg',
            4 => 'default-category.jpg',
        ];
        $index = 0;
    @endphp

    @if ($groups->isEmpty())
        <div class="alert alert-info my-5 text-center">
            <h5>Chưa có sản phẩm nào để hiển thị.</h5>
        </div>
    @else
        @foreach ($groups as $maLoai => $productsInCat)
            @php
                $loai = $productsInCat->first()->loaiSanPham;
                $tenLoai = $loai ? $loai->TenLoai : 'Danh mục';
                $left = ($index % 2 == 0);
                $limitedProducts = $productsInCat->take(6);
                $hinhAnhCat = $categoryImageMap[$maLoai] ?? 'default-category.jpg';
            @endphp

            <section class="category {{ $left ? 'left' : 'right' }}">
                <div class="image-block">
                    <img src="{{ asset('images/' . $hinhAnhCat) }}"
                         alt="Quảng cáo {{ $tenLoai }}"
                         style="width: 100%; max-width: 360px; height: 550px; object-fit: cover;" />
                </div>

                <div class="product-block">
                    <div class="header-row">
                        <h2 class="fw-bold">{{ $tenLoai }}</h2>
                        <a href="{{ route('sanpham.index', ['loai' => $maLoai]) }}" class="btn-xem-tat-ca">Xem tất cả</a>
                    </div>

                    <div class="grid">
                        @foreach ($limitedProducts as $sp)
                            @php
                                $giaGoc = (float)($sp->GiaGoc ?? 0);
                                $giaBan = (float)$sp->gia_sau_khuyen_mai;
                                $coKhuyenMai = ($giaBan < $giaGoc && $giaGoc > 0);
                                $phanTramGiam = $coKhuyenMai ? max(1, round((1 - ($giaBan / $giaGoc)) * 100)) : 0;
                            @endphp

                            <div class="card">
                                @if ($coKhuyenMai)
                                    <div class="promo-badge">-{{ $phanTramGiam }}%</div>
                                @endif

                                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="d-block text-decoration-none">
                                    <img src="{{ asset('images/' . ($sp->HinhAnhDaiDien ?: 'default-product.jpg')) }}"
                                         alt="{{ $sp->TenSanPham }}" />

                                    <h3>{{ $sp->TenSanPham }}</h3>

                                    <div>
                                        <p>
                                            <span>{{ number_format($giaBan, 0, ',', '.') }}₫</span>
                                            @if ($coKhuyenMai)
                                                <span class="old-price">{{ number_format($giaGoc, 0, ',', '.') }}₫</span>
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @php $index++; @endphp
        @endforeach
    @endif
@endsection
