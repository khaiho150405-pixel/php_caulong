@extends('layouts.app')

@section('title', $tin->TieuDe . ' - Bảng tin cầu lông')

@section('styles')
<link href="{{ asset('css/bangtin.css') }}" rel="stylesheet" />
@endsection

@section('content')
@php $hamTien = fn ($v) => number_format((float) $v, 0, ',', '.') . '₫'; @endphp
<div class="bt-page">
    <nav class="bt-crumb" aria-label="Điều hướng">
        <a href="{{ route('bangtin.index') }}"><i class="fa-solid fa-arrow-left"></i> Bảng tin</a>
        <span>/</span>
        <a href="{{ route('bangtin.index', ['loai' => $tin->LoaiTin]) }}">{{ $tin->nhanLoai }}</a>
    </nav>

    <div class="row g-4">
        <article class="col-lg-8 bt-article">
            <span class="bt-tag static"><i class="fa-solid {{ $tin->iconLoai }}"></i> {{ $tin->nhanLoai }}</span>
            <h1>{{ $tin->TieuDe }}</h1>
            <div class="bt-meta big">
                <time datetime="{{ $tin->NgayDang->format('Y-m-d') }}"><i class="fa-regular fa-calendar"></i> {{ $tin->NgayDang->format('d/m/Y') }}</time>
                <span><i class="fa-regular fa-eye"></i> {{ number_format($tin->LuotXem) }} lượt xem</span>
                @if ($tin->tacGia)<span><i class="fa-regular fa-user"></i> {{ $tin->tacGia->TenDangNhap }}</span>@endif
            </div>

            @if ($tin->TomTat)<p class="bt-lead-text">{{ $tin->TomTat }}</p>@endif

            @if ($tin->tranDau)
                <div class="bt-feature-match">@include('BangTin._trandau', ['tran' => $tin->tranDau])</div>
            @endif

            <div class="bt-content">
                @foreach (preg_split('/\R{2,}/', trim($tin->NoiDung)) as $doan)
                    <p>{!! nl2br(e($doan)) !!}</p>
                @endforeach
            </div>

            @if ($tin->sanPham)
                @php
                    $sp = $tin->sanPham;
                    $gg = (float) $sp->GiaGoc; $gb = (float) $sp->gia_sau_khuyen_mai;
                @endphp
                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="bt-product">
                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" width="120" height="120" loading="lazy">
                    <div>
                        <small>{{ $sp->hang->TenHang ?? 'Sản phẩm trong bài' }}</small>
                        <b>{{ $sp->TenSanPham }}</b>
                        <span class="product-price">{{ $hamTien($gb) }}</span>
                        @if ($gb < $gg)<span class="price-small">{{ $hamTien($gg) }}</span>@endif
                    </div>
                    <span class="btn btn-primary btn-sm">Xem sản phẩm <i class="fa-solid fa-arrow-right"></i></span>
                </a>
            @endif

            @if ($tin->NguonTin || $tin->LienKetNguon)
                <p class="bt-source"><i class="fa-solid fa-link"></i> Nguồn tham khảo:
                    @if ($tin->LienKetNguon)
                        <a href="{{ $tin->LienKetNguon }}" target="_blank" rel="noopener nofollow">{{ $tin->NguonTin ?: $tin->LienKetNguon }}</a>
                    @else
                        {{ $tin->NguonTin }}
                    @endif
                    @if ($tin->tranDau && $tin->tranDau->LienKetNguon && $tin->tranDau->LienKetNguon !== $tin->LienKetNguon)
                        · <a href="{{ $tin->tranDau->LienKetNguon }}" target="_blank" rel="noopener nofollow">{{ $tin->tranDau->NguonTin ?: 'Tỉ số' }} (tỉ số)</a>
                    @endif
                </p>
            @endif

            @if ($lienQuan->isNotEmpty())
                <section class="bt-related">
                    <h2>Tin cùng chuyên mục</h2>
                    <div class="bt-grid">
                        @foreach ($lienQuan as $t)
                            @include('BangTin._card', ['t' => $t])
                        @endforeach
                    </div>
                </section>
            @endif
        </article>

        <aside class="col-lg-4">
            @include('BangTin._cotphu')
        </aside>
    </div>
</div>
@endsection
