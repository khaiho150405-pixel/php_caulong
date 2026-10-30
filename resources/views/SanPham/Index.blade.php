@extends('layouts.app')

@section('title', $title ?? 'Danh sách sản phẩm - Cửa Hàng Cầu Lông')

@section('styles')
<style>
    .product-list { margin-top: 8px; margin-bottom: 40px; }
    .list-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--line);
    }
    .list-head h2 { font-size: 1.5rem; font-weight: 700; margin: 0; }
    .list-head .count { font-size: .9rem; color: var(--muted); font-weight: 500; margin-left: 8px; }

    .sort-box { display: flex; align-items: center; gap: 8px; font-size: .88rem; color: var(--muted); }
    .sort-box .form-select { width: auto; min-width: 170px; }

    
    @media (min-width: 992px) {
        aside.col-lg-3 {
            position: relative;
        }
        #filterCollapse {
            position: -webkit-sticky;
            position: sticky;
            top: 145px; 
            z-index: 90;
            display: block !important;
            height: auto;
            transition: top 0.2s ease;
        }
        .filter-panel {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg, 14px);
            padding: 12px 14px 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: visible;
        }
    }
    @media (max-width: 991px) {
        .filter-panel {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg, 14px);
            padding: 14px 16px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
    }

    .filter-header { font-size: 0.92rem; }
    .filter-group { padding: 8px 0; border-bottom: 1px solid var(--line); }
    .filter-group:last-of-type { border-bottom: 0; padding-bottom: 6px; }
    .filter-title {
        display: block;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--muted);
        margin: 0 0 6px;
    }

    .price-chips { display: grid; grid-template-columns: repeat(2, 1fr); gap: 5px; }
    .price-chips > span:last-child:nth-child(odd) { grid-column: span 2; }
    .price-chips input { position: absolute; opacity: 0; pointer-events: none; }
    .price-chips label {
        display: block;
        text-align: center;
        padding: 4px 6px;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-size: .74rem;
        font-weight: 500;
        color: var(--ink-soft);
        cursor: pointer;
        transition: all .15s ease;
        background: #f8fafc;
        white-space: nowrap;
    }
    .price-chips label:hover {
        border-color: var(--brand);
        color: var(--brand-700);
        background: #fff;
    }
    .price-chips input:checked + label {
        background: var(--brand-50, #fef2f2);
        border-color: var(--brand, #dc2626);
        color: var(--brand-700, #b91c1c);
        font-weight: 700;
    }

    .price-range { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 6px; }
    .price-range .form-control { font-size: .8rem; padding: 4px 8px; height: 31px; border-radius: 6px; }
    .price-range span { color: var(--muted); font-size: 0.8rem; }

    .brand-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px 8px; }
    .utility-grid { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }

    .filter-check {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .82rem;
        cursor: pointer;
        font-weight: 500;
        color: var(--ink-soft);
        margin: 0;
        user-select: none;
    }
    .filter-check input {
        width: 15px;
        height: 15px;
        accent-color: var(--brand, #dc2626);
        flex-shrink: 0;
        cursor: pointer;
        border-radius: 3px;
    }
    .filter-check:hover { color: var(--brand-700, #b91c1c); }

    .filter-actions { display: flex; gap: 8px; padding-top: 8px; }
    .filter-actions .btn { font-size: 0.82rem; padding: 5px 10px; }

    .active-filters { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 18px; }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: var(--brand-50);
        border: 1px solid var(--brand-100);
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 600;
        color: var(--brand-700);
    }
    .clear-filter { font-size: .82rem; font-weight: 600; color: var(--muted); }
    .clear-filter:hover { color: var(--danger); }

    @media (max-width: 991.98px) { .filter-panel { position: static; } }
</style>
@endsection

@section('content')
@php
    $hamTien = fn ($v) => number_format((float) $v, 0, ',', '.') . 'đ';
    $laTrangDanhMuc = request()->routeIs('sanpham.index', 'sanpham.viewallproduct');
    $khoangNhan = [
        '0-1000000' => 'Dưới 1 triệu',
        '1000000-2000000' => '1 – 2 triệu',
        '2000000-3000000' => '2 – 3 triệu',
        '3000000-5000000' => '3 – 5 triệu',
        '5000000-' => 'Trên 5 triệu',
    ];
    $tuKhoa = $keyword ?? null;
@endphp
<div class="container product-list">
    <div class="list-head">
        <h2>
            {{ $title }}
            @if(isset($sanPhams))
                <span class="count">({{ $sanPhams->total() }} sản phẩm)</span>
            @endif
        </h2>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                <i class="fa-solid fa-sliders"></i> Bộ lọc
            </button>
            <label class="sort-box mb-0">
                Sắp xếp
                <select name="sap_xep" form="filterForm" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="moi" @selected($sapXep === 'moi')>Mới nhất</option>
                    <option value="gia_tang" @selected($sapXep === 'gia_tang')>Giá thấp đến cao</option>
                    <option value="gia_giam" @selected($sapXep === 'gia_giam')>Giá cao đến thấp</option>
                    <option value="ten" @selected($sapXep === 'ten')>Tên A → Z</option>
                </select>
            </label>
        </div>
    </div>

    <div class="row g-4">
        
        <aside class="col-lg-3">
            <div class="collapse d-lg-block" id="filterCollapse">
                <form id="filterForm" class="filter-panel" action="{{ url()->current() }}" method="GET">
                    @if ($tuKhoa)
                        <input type="hidden" name="keyword" value="{{ $tuKhoa }}">
                    @endif
                    @if (request('ten'))
                        <input type="hidden" name="ten" value="{{ request('ten') }}">
                    @endif

                    
                    <div class="filter-header d-flex align-items-center justify-content-between pb-2 mb-1 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-sliders text-danger"></i>
                            <span class="fw-bold text-dark">Bộ lọc tìm kiếm</span>
                        </div>
                        @if ($dangLoc)
                            <a href="{{ url()->current() }}{{ $tuKhoa ? '?keyword=' . urlencode($tuKhoa) : '' }}"
                               class="clear-filter small text-danger text-decoration-none fw-semibold d-inline-flex align-items-center gap-1"
                               title="Đặt lại tất cả bộ lọc">
                                <i class="fa-solid fa-rotate-left"></i> Đặt lại
                            </a>
                        @endif
                    </div>

                    @if ($laTrangDanhMuc)
                        <div class="filter-group">
                            <label for="filterLoaiSelect" class="filter-title"><i class="fa-solid fa-layer-group me-1 text-muted"></i> Danh mục</label>
                            <select name="loai" id="filterLoaiSelect" class="form-select form-select-sm">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($dsLoai as $l)
                                    <option value="{{ $l->MaLoai }}" @selected((string) request('loai', request('maLoai')) === (string) $l->MaLoai)>{{ $l->TenLoai }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="filter-group">
                        <span class="filter-title"><i class="fa-solid fa-money-bill-wave me-1 text-muted"></i> Khoảng giá</span>
                        <div class="price-chips">
                            @foreach ($khoangNhan as $khoa => $nhan)
                                <span>
                                    <input type="radio" name="khoang" id="khoang-{{ $loop->index }}" value="{{ $khoa }}" @checked($locKhoang === $khoa && !request()->filled('gia_tu') && !request()->filled('gia_den'))>
                                    <label for="khoang-{{ $loop->index }}">{{ $nhan }}</label>
                                </span>
                            @endforeach
                        </div>
                        <div class="price-range mt-2">
                            <input type="number" name="gia_tu" min="0" step="10000" class="form-control" placeholder="Từ (đ)" value="{{ request('gia_tu') }}">
                            <span>–</span>
                            <input type="number" name="gia_den" min="0" step="10000" class="form-control" placeholder="Đến (đ)" value="{{ request('gia_den') }}">
                        </div>
                    </div>

                    <div class="filter-group">
                        <span class="filter-title"><i class="fa-solid fa-medal me-1 text-muted"></i> Thương hiệu</span>
                        <div class="brand-grid">
                            @foreach ($dsHang as $h)
                                <label class="filter-check">
                                    <input type="checkbox" name="hangs[]" value="{{ $h->MaHang }}" @checked(in_array($h->MaHang, $locHang))>
                                    <span>{{ $h->TenHang }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="filter-group">
                        <span class="filter-title"><i class="fa-solid fa-bolt me-1 text-muted"></i> Tiện ích</span>
                        <div class="utility-grid">
                            <label class="filter-check">
                                <input type="checkbox" name="giam_gia" value="1" @checked(request()->boolean('giam_gia'))>
                                <span>Đang giảm giá</span>
                            </label>
                            <label class="filter-check">
                                <input type="checkbox" name="con_hang" value="1" @checked(request()->boolean('con_hang'))>
                                <span>Còn hàng</span>
                            </label>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-danger btn-sm fw-semibold flex-fill shadow-sm">
                            <i class="fa-solid fa-filter me-1"></i> Áp dụng
                        </button>
                        @if ($dangLoc)
                            <a href="{{ url()->current() }}{{ $tuKhoa ? '?keyword=' . urlencode($tuKhoa) : '' }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                Xóa lọc
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </aside>

        
        <div class="col-lg-9">
            @if ($dangLoc)
                <div class="active-filters">
                    @if ($locGiaTu !== null || $locGiaDen !== null)
                        <span class="filter-tag"><i class="fa-solid fa-tag"></i>
                            @if ($locGiaTu !== null && $locGiaDen !== null) {{ $hamTien($locGiaTu) }} – {{ $hamTien($locGiaDen) }}
                            @elseif ($locGiaTu !== null) Từ {{ $hamTien($locGiaTu) }}
                            @else Đến {{ $hamTien($locGiaDen) }} @endif
                        </span>
                    @endif
                    @foreach ($dsHang->whereIn('MaHang', $locHang) as $h)
                        <span class="filter-tag">{{ $h->TenHang }}</span>
                    @endforeach
                    @if (request()->boolean('giam_gia'))
                        <span class="filter-tag">Đang giảm giá</span>
                    @endif
                    @if (request()->boolean('con_hang'))
                        <span class="filter-tag">Còn hàng</span>
                    @endif
                    <a class="clear-filter" href="{{ url()->current() }}{{ $tuKhoa ? '?keyword=' . urlencode($tuKhoa) : '' }}">Xóa tất cả</a>
                </div>
            @endif

            @if ($sanPhams->isEmpty())
                @include('partials._empty', [
                    'message' => $dangLoc ? 'Không có sản phẩm nào khớp bộ lọc. Hãy thử nới rộng điều kiện.' : 'Không tìm thấy sản phẩm nào phù hợp.',
                    'linkUrl' => $dangLoc ? url()->current() . ($tuKhoa ? '?keyword=' . urlencode($tuKhoa) : '') : route('home'),
                    'linkText' => $dangLoc ? 'Xóa bộ lọc' : 'Quay lại trang chủ',
                    'compact' => false,
                ])
            @else
                <div class="row g-3 g-lg-4">
                    @foreach ($sanPhams as $sp)
                        @php
                            $giaGoc = (float)($sp->GiaGoc ?? 0);
                            $giaBan = (float)$sp->gia_sau_khuyen_mai;
                            $km = $sp->khuyenMai;
                            $hasDiscount = $km && $giaBan < $giaGoc;
                            $img = $sp->anh_thumb;
                            $avgRating = $sp->trung_binh_danh_gia;
                        @endphp
                        <div class="col-6 col-md-4">
                            <div class="product-card">
                                @if ($hasDiscount)
                                    <div class="badge-promo">-{{ round($km->PhanTramGiam) }}%</div>
                                @endif

                                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="thumb">
                                    <img src="{{ asset('images/' . $img) }}"
                                         alt="{{ $sp->TenSanPham }}"
                                         loading="lazy"
                                         onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                                </a>

                                <div class="body">
                                    <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                                    <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">
                                        {{ $sp->TenSanPham }}
                                    </a>

                                    <div class="rating-small">
                                        @for ($i = 0; $i < 5; $i++)
                                            @if ($i < round($avgRating))
                                                <span>★</span>
                                            @else
                                                <span class="off">★</span>
                                            @endif
                                        @endfor
                                        <span class="count">({{ number_format($avgRating, 1) }})</span>
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

                @include('partials._pagination', ['items' => $sanPhams])
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function adjustFilterStickyOffset() {
        var header = document.querySelector('.main-header');
        var filter = document.getElementById('filterCollapse');
        if (header && filter && window.innerWidth >= 992) {
            var offset = header.offsetHeight + 20; // Giữ khoảng cách thoáng đúng 20px dưới header
            filter.style.top = offset + 'px';
        }
    }
    adjustFilterStickyOffset();
    window.addEventListener('resize', adjustFilterStickyOffset);
});
</script>
@endpush
@endsection
