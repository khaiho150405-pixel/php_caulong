@extends('layouts.app')

@section('title', 'Khám phá cửa hàng cầu lông - Cửa Hàng Cầu Lông')

@section('styles')
<link href="{{ asset('css/landing.css') }}" rel="stylesheet" />
<link href="{{ asset('css/bangtin.css') }}" rel="stylesheet" />
<script>
    // Chỉ ẩn nội dung chờ hiệu ứng khi JS chạy; nếu script lỗi thì tự hiện lại sau 2,5 giây
    document.documentElement.classList.add('l-js');
    setTimeout(function () { document.documentElement.classList.remove('l-js'); }, 2500);
</script>
@endsection

@section('full')
@php
    $hamTien = fn ($v) => number_format((float) $v, 0, ',', '.') . '₫';

    $cauHinh = [
        1 => ['anh' => 'landing/cat-vot.webp', 'mota' => 'Khung nhẹ, đầm tay, đủ lối chơi công thủ.'],
        2 => ['anh' => 'landing/cat-giay.webp', 'mota' => 'Bám sân, êm chân cho từng pha di chuyển.'],
        3 => ['anh' => 'landing/cat-aoquan.webp', 'mota' => 'Thoáng khí, co giãn, thoải mái khi vào trận.'],
        4 => ['anh' => 'landing/cat-phukien.webp', 'mota' => 'Cầu lông, quấn cán và những món nhỏ nhưng cần thiết.'],
    ];
@endphp

{{-- ===== 1. HERO: ảnh sản phẩm thật lấy từ CSDL ===== --}}
<section class="l-hero bg-court" id="lHero">
    <div class="container l-hero-inner">
        <div class="l-hero-copy">
            <span class="l-kicker light">Cửa hàng cầu lông trực tuyến</span>
            <h1>Chọn đúng dụng cụ,<br><em>chơi cầu thăng hoa.</em></h1>
            <p>Vợt, giày, trang phục và phụ kiện chính hãng từ Yonex, Li-Ning, Victor, Mizuno. Lọc theo ngân sách, hãng và ưu đãi để tìm đúng món bạn cần.</p>

            <div class="l-cta">
                <a href="{{ route('sanpham.index') }}" class="btn btn-light btn-lg">Vào xem sản phẩm <i class="fa-solid fa-arrow-right ms-1"></i></a>
                <a href="{{ route('khuyenmai.index') }}" class="btn btn-outline-light btn-lg">Xem khuyến mãi</a>
            </div>

            <dl class="l-stats">
                <div><dt>{{ $thongKe['sanPham'] }}</dt><dd>Sản phẩm</dd></div>
                <div><dt>{{ $thongKe['hang'] }}</dt><dd>Thương hiệu</dd></div>
                <div><dt>{{ $thongKe['uuDai'] }}</dt><dd>Ưu đãi đang chạy</dd></div>
            </dl>
        </div>

        <div class="l-hero-visual">
            @foreach ($heroSp as $sp)
                @php
                    $gg = (float) ($sp->GiaGoc ?? 0);
                    $gb = (float) $sp->gia_sau_khuyen_mai;
                    $pt = $gg > 0 && $gb < $gg ? max(1, round((1 - $gb / $gg) * 100)) : 0;
                @endphp
                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="l-hero-card c{{ $loop->iteration }}">
                    @if ($pt)<span class="badge-promo">-{{ $pt }}%</span>@endif
                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" width="320" height="320"
                         @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif decoding="async">
                    <div class="l-hero-card-info">
                        <small>{{ $sp->hang->TenHang ?? 'Hãng khác' }}</small>
                        <b>{{ $sp->TenSanPham }}</b>
                        <span>{{ $hamTien($gb) }}</span>
                    </div>
                </a>
            @endforeach

            <span class="l-hero-chip chip-a"><i class="fa-solid fa-tags"></i> Giảm đến {{ $giamToiDa }}%</span>
            <span class="l-hero-chip chip-b"><i class="fa-solid fa-shield-halved"></i> {{ $thongKe['hang'] }} thương hiệu chính hãng</span>
        </div>
    </div>
</section>

{{-- ===== 2. Chọn theo ngân sách (dùng bộ lọc giá của trang sản phẩm) ===== --}}
<section class="l-section">
    <div class="container">
        <div class="l-head l-reveal">
            <span class="l-kicker">Theo ngân sách</span>
            <h2>Bạn định chi bao nhiêu?</h2>
            <p>Chọn mức giá, chúng tôi lọc sẵn các sản phẩm phù hợp (đã tính khuyến mãi).</p>
        </div>

        <div class="l-budgets">
            @foreach ($nganSach as $m)
                <a href="{{ route('sanpham.index', ['khoang' => $m['khoa']]) }}" class="l-budget l-reveal" style="--d: {{ $loop->index * 50 }}ms">
                    <strong>{{ $m['nhan'] }}</strong>
                    <span>{{ $m['soLuong'] }} sản phẩm</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== 3. Danh mục ===== --}}
<section class="l-section l-tint">
    <div class="container">
        <div class="l-head l-reveal">
            <span class="l-kicker">Danh mục</span>
            <h2>Mua sắm theo nhu cầu</h2>
        </div>

        <div class="l-cats">
            @foreach ($danhMuc as $dm)
                @php $cfg = $cauHinh[$dm->MaLoai] ?? ['anh' => null, 'icon' => 'fa-bag-shopping', 'mota' => '']; @endphp
                <a href="{{ route('sanpham.index', ['loai' => $dm->MaLoai]) }}" class="l-cat l-reveal" style="--d: {{ $loop->index * 60 }}ms">
                    @if ($cfg['anh'])
                        <img src="{{ asset('images/' . $cfg['anh']) }}" alt="{{ $dm->TenLoai }}" loading="lazy" decoding="async" width="360" height="450">
                    @else
                        <i class="fa-solid {{ $cfg['icon'] }} l-cat-icon"></i>
                    @endif
                    <div class="l-cat-info">
                        <h3>{{ $dm->TenLoai }}</h3>
                        <span>{{ $dm->san_phams_count }} sản phẩm</span>
                        <p>{{ $cfg['mota'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== 4. Ưu đãi nổi bật (CSDL) ===== --}}
<section class="l-section">
    <div class="container">
        <div class="l-head l-head-row l-reveal">
            <div>
                <span class="l-kicker">Ưu đãi</span>
                <h2>Đang giảm giá</h2>
            </div>
            <a href="{{ route('khuyenmai.index') }}" class="btn-xem-tat-ca">Xem tất cả ưu đãi <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="l-products">
            @foreach ($uuDai as $sp)
                @php
                    $giaGoc = (float) ($sp->GiaGoc ?? 0);
                    $giaBan = (float) $sp->gia_sau_khuyen_mai;
                    $giam = $giaGoc > 0 && $giaBan < $giaGoc;
                @endphp
                <div class="product-card l-reveal" style="--d: {{ ($loop->index % 4) * 50 }}ms">
                    @if ($giam)
                        <div class="badge-promo">-{{ max(1, round((1 - $giaBan / $giaGoc) * 100)) }}%</div>
                    @endif
                    <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="thumb">
                        <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="{{ $sp->TenSanPham }}" loading="lazy" decoding="async"
                             onerror="this.src='{{ asset('images/default-product.jpg') }}'">
                    </a>
                    <div class="body">
                        <div class="product-brand">{{ $sp->hang->TenHang ?? 'Hãng khác' }}</div>
                        <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}" class="product-title" title="{{ $sp->TenSanPham }}">{{ $sp->TenSanPham }}</a>
                        <div class="product-footer">
                            <span class="product-price">{{ $hamTien($giaBan) }}</span>
                            @if ($giam)
                                <span class="price-small">{{ $hamTien($giaGoc) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== 5. Hướng dẫn chọn vợt theo lối chơi ===== --}}
<section class="l-section l-tint">
    <div class="container">
        <div class="l-head l-reveal">
            <span class="l-kicker">Hướng dẫn nhanh</span>
            <h2>Chọn vợt theo lối chơi</h2>
            <p>Điểm cân bằng của vợt quyết định cảm giác đánh. Bắt đầu từ ba dòng vợt Yonex phổ biến để so sánh.</p>
        </div>

        <div class="l-guides">
            <article class="l-guide l-reveal">
                <span class="l-guide-icon"><i class="fa-solid fa-bolt"></i></span>
                <h3>Thiên về tấn công</h3>
                <p>Vợt đầu nặng cho cú đập mạnh và lực mạnh từ cuối sân. Phù hợp người chơi chủ động dồn điểm.</p>
                <a href="{{ route('sanpham.index', ['loai' => 1, 'ten' => 'Astrox']) }}">Xem dòng Astrox <i class="fa-solid fa-arrow-right"></i></a>
            </article>
            <article class="l-guide l-reveal" style="--d: 60ms">
                <span class="l-guide-icon"><i class="fa-solid fa-feather"></i></span>
                <h3>Nhanh và linh hoạt</h3>
                <p>Vợt đầu nhẹ, vung nhanh, thuận lợi cho các pha phản tạt và đánh đôi ở lưới.</p>
                <a href="{{ route('sanpham.index', ['loai' => 1, 'ten' => 'Nanoflare']) }}">Xem dòng Nanoflare <i class="fa-solid fa-arrow-right"></i></a>
            </article>
            <article class="l-guide l-reveal" style="--d: 120ms">
                <span class="l-guide-icon"><i class="fa-solid fa-scale-balanced"></i></span>
                <h3>Cân bằng, dễ kiểm soát</h3>
                <p>Điểm cân bằng ở giữa, cảm giác cầu ổn định, dễ làm quen cho người mới và người chơi toàn diện.</p>
                <a href="{{ route('sanpham.index', ['loai' => 1, 'ten' => 'Arcsaber']) }}">Xem dòng Arcsaber <i class="fa-solid fa-arrow-right"></i></a>
            </article>
        </div>
    </div>
</section>

{{-- ===== 5b. Bảng tin cầu lông (CSDL): tin mới + bảng điểm ===== --}}
@if ($tinMoi->isNotEmpty())
<section class="l-section l-news-sec">
    <div class="container">
        <div class="l-head l-head-row l-reveal">
            <div>
                <span class="l-board-tag"><span class="bt-live"><i></i> Bảng tin</span></span>
                <h2>Tin nóng làng cầu lông</h2>
                <p>Tin mới nhất, kết quả trận đấu và sản phẩm được quan tâm, cập nhật liên tục.</p>
            </div>
            <a href="{{ route('bangtin.index') }}" class="btn-xem-tat-ca">Mở bảng tin <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="l-board l-reveal">
            <div class="l-board-feed">
                @include('BangTin._noibat', ['t' => $tinMoi->first()])
                <div class="bt-timeline">
                    @foreach ($tinMoi->skip(1) as $t)
                        @include('BangTin._dong', ['t' => $t, 'nho' => true])
                    @endforeach
                </div>
            </div>
            <div class="l-board-score">
                @include('BangTin._bangdiem', ['ds' => $tranMoi, 'tieuDe' => 'Bảng điểm'])
            </div>
        </div>
    </div>
</section>
@endif

{{-- ===== 6. Đánh giá của khách hàng (CSDL) ===== --}}
@if ($danhGia->isNotEmpty())
<section class="l-section">
    <div class="container">
        <div class="l-head l-reveal">
            <span class="l-kicker">Khách hàng nói gì</span>
            <h2>Đánh giá gần đây</h2>
        </div>

        <div class="l-reviews">
            @foreach ($danhGia as $dg)
                <figure class="l-review l-reveal" style="--d: {{ $loop->index * 60 }}ms">
                    <div class="l-stars" aria-label="{{ $dg->DanhGia }} trên 5 sao">
                        @for ($i = 1; $i <= 5; $i++)<i class="fa-solid fa-star {{ $i <= $dg->DanhGia ? '' : 'off' }}"></i>@endfor
                    </div>
                    <blockquote>{{ $dg->NoiDung }}</blockquote>
                    <figcaption>
                        <strong>{{ $dg->khachHang->HoTen ?? 'Khách hàng' }}</strong>
                        @if ($dg->sanPham)
                            <a href="{{ route('home.chitietsanpham', ['id' => $dg->sanPham->MaSanPham]) }}">{{ $dg->sanPham->TenSanPham }}</a>
                        @endif
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== 7. Thương hiệu ===== --}}
<section class="l-section {{ $danhGia->isNotEmpty() ? 'l-tint' : '' }}">
    <div class="container">
        <div class="l-head text-center l-reveal">
            <span class="l-kicker">Thương hiệu</span>
            <h2>Đồng hành cùng các hãng hàng đầu</h2>
        </div>
        <div class="l-brands">
            @foreach ([['Yonex', 'yonex', 'Yonex'], ['Li-Ning', 'lining', 'Lining'], ['Victor', 'victor', 'Victor'], ['Mizuno', 'mizuno', 'Mizuno']] as [$ten, $slug, $query])
                @php
                    $logo = collect(['svg', 'png', 'webp', 'jpg'])->map(fn ($e) => "images/brands/{$slug}.{$e}")->first(fn ($f) => is_file(public_path($f)));
                @endphp
                <a href="{{ route('sanpham.index', ['hang' => $query]) }}" class="l-brand l-reveal" title="{{ $ten }}">
                    @if ($logo)
                        <img src="{{ asset($logo) }}" alt="Logo {{ $ten }}" loading="lazy" decoding="async">
                    @else
                        <span>{{ $ten }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ===== 8. Kêu gọi hành động ===== --}}
<section class="l-final bg-court">
    <div class="container text-center l-reveal">
        <h2>Sẵn sàng ra sân?</h2>
        <p>Chọn vợt, giày và trang phục phù hợp ngay hôm nay, nhận ưu đãi dành cho thành viên.</p>
        <div class="l-cta justify-content-center">
            <a href="{{ route('sanpham.index') }}" class="btn btn-light btn-lg">Bắt đầu mua sắm</a>
            @guest
                <a href="{{ route('taikhoan.dangky') }}" class="btn btn-outline-light btn-lg">Tạo tài khoản</a>
            @endguest
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ asset('js/landing/landing.js') }}" defer></script>
@endsection
