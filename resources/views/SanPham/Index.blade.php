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

    /* ----- Khung bộ lọc ----- */
    .filter-panel {
        position: sticky;
        top: 150px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--radius);
        padding: 4px 18px 18px;
    }
    .filter-group { padding: 16px 0; border-bottom: 1px solid var(--line); }
    .filter-group:last-of-type { border-bottom: 0; }
    .filter-title {
        font-size: .74rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--muted);
        margin: 0 0 10px;
    }

    .price-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
    .price-chips input { position: absolute; opacity: 0; pointer-events: none; }
    .price-chips label {
        padding: 6px 12px;
        border: 1px solid var(--line-strong);
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 500;
        color: var(--ink-soft);
        cursor: pointer;
        transition: all .15s ease;
    }
    .price-chips label:hover { border-color: var(--brand); color: var(--brand-700); }
    .price-chips input:checked + label {
        background: var(--brand-50);
        border-color: var(--brand);
        color: var(--brand-700);
        font-weight: 600;
    }
    .price-chips input:focus-visible + label { box-shadow: 0 0 0 .2rem rgba(20, 143, 95, .25); }

    .price-range { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 8px; }
    .price-range .form-control { font-size: .85rem; padding: .4rem .6rem; }
    .price-range span { color: var(--muted); }

    .filter-check { display: flex; align-items: center; gap: 10px; padding: 5px 0; font-size: .9rem; cursor: pointer; font-weight: 500; color: var(--ink-soft); }
    .filter-check input { width: 17px; height: 17px; accent-color: var(--brand); flex-shrink: 0; }
    .filter-actions { display: flex; gap: 8px; padding-top: 6px; }
    .filter-actions .btn { flex: 1; }

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
        {{-- Cột bộ lọc --}}
        <aside class="col-lg-3">
            <div class="collapse d-lg-block" id="filterCollapse">
                <form id="filterForm" class="filter-panel" action="{{ url()->current() }}" method="GET">
                    @if ($tuKhoa)
                        <input type="hidden" name="keyword" value="{{ $tuKhoa }}">
                    @endif
                    @if (request('ten'))
                        <input type="hidden" name="ten" value="{{ request('ten') }}">
                    @endif

                    @if ($laTrangDanhMuc)
                        <div class="filter-group">
                            <h3 class="filter-title">Danh mục</h3>
                            <select name="loai" class="form-select form-select-sm">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($dsLoai as $l)
                                    <option value="{{ $l->MaLoai }}" @selected((string) request('loai', request('maLoai')) === (string) $l->MaLoai)>{{ $l->TenLoai }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="filter-group">
                        <h3 class="filter-title">Khoảng giá</h3>
                        <div class="price-chips">
                            @foreach ($khoangNhan as $khoa => $nhan)
                                <span>
                                    <input type="radio" name="khoang" id="khoang-{{ $loop->index }}" value="{{ $khoa }}" @checked($locKhoang === $khoa && !request()->filled('gia_tu') && !request()->filled('gia_den'))>
                                    <label for="khoang-{{ $loop->index }}">{{ $nhan }}</label>
                                </span>
                            @endforeach
                        </div>
                        <div class="price-range">
                            <input type="number" name="gia_tu" min="0" step="10000" class="form-control" placeholder="Từ (đ)" value="{{ request('gia_tu') }}">
                            <span>–</span>
                            <input type="number" name="gia_den" min="0" step="10000" class="form-control" placeholder="Đến (đ)" value="{{ request('gia_den') }}">
                        </div>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Thương hiệu</h3>
                        @foreach ($dsHang as $h)
                            <label class="filter-check">
                                <input type="checkbox" name="hangs[]" value="{{ $h->MaHang }}" @checked(in_array($h->MaHang, $locHang))>
                                {{ $h->TenHang }}
                            </label>
                        @endforeach
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Tiện ích</h3>
                        <label class="filter-check">
                            <input type="checkbox" name="giam_gia" value="1" @checked(request()->boolean('giam_gia'))>
                            Đang giảm giá
                        </label>
                        <label class="filter-check">
                            <input type="checkbox" name="con_hang" value="1" @checked(request()->boolean('con_hang'))>
                            Còn hàng
                        </label>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Áp dụng</button>
                        @if ($dangLoc)
                            <a href="{{ url()->current() }}{{ $tuKhoa ? '?keyword=' . urlencode($tuKhoa) : '' }}" class="btn btn-outline-secondary">Xóa lọc</a>
                        @endif
                    </div>
                </form>
            </div>
        </aside>

        {{-- Cột sản phẩm --}}
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
@endsection
