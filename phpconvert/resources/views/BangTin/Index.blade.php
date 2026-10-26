@extends('layouts.app')

@section('title', 'Bảng tin cầu lông - Cửa Hàng Cầu Lông')

@section('styles')
<link href="{{ asset('css/bangtin.css') }}" rel="stylesheet" />
@endsection

@section('content')
<div class="bt-page">
    {{-- Đầu trang kiểu bảng tin: tên chuyên mục, trạng thái cập nhật, số liệu nhanh --}}
    <header class="bt-masthead bg-court">
        <div class="bt-mast-main">
            <span class="bt-live"><i></i> Cập nhật {{ now()->format('d/m/Y') }}</span>
            <h1><span>BẢNG TIN</span> Cầu lông</h1>
            <p>Kết quả trận đấu, tuyển thủ, các giải lớn và sản phẩm đang được quan tâm, sắp xếp từ mới đến cũ. Mỗi tin đều có nguồn tham khảo.</p>
        </div>
        <dl class="bt-mast-stats">
            <div><dt>{{ $tong['tin'] }}</dt><dd>Tin đã đăng</dd></div>
            <div><dt>{{ $tong['tran'] }}</dt><dd>Trận đấu</dd></div>
            <div><dt>{{ $tong['luotXem'] }}</dt><dd>Lượt xem</dd></div>
        </dl>
    </header>

    @include('BangTin._ticker', ['ds' => $tickerTran])

    {{-- Thanh chuyên mục + tìm kiếm --}}
    <div class="bt-toolbar">
        <nav class="bt-sections" aria-label="Chuyên mục">
            <a href="{{ route('bangtin.index', array_filter(['keyword' => $keyword])) }}" class="{{ !$loai ? 'active' : '' }}">Tất cả <b>{{ $tong['tin'] }}</b></a>
            @foreach (\App\Models\TinTuc::LOAI as $khoa => [$nhan, $icon])
                <a href="{{ route('bangtin.index', array_filter(['loai' => $khoa, 'keyword' => $keyword])) }}" class="{{ $loai === $khoa ? 'active' : '' }}">
                    <i class="fa-solid {{ $icon }}"></i> {{ $nhan }} <b>{{ $demLoai[$khoa] ?? 0 }}</b>
                </a>
            @endforeach
        </nav>
        <form action="{{ route('bangtin.index') }}" method="GET" class="bt-search">
            @if ($loai)<input type="hidden" name="loai" value="{{ $loai }}">@endif
            <input type="search" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm tin, tuyển thủ, giải đấu..." aria-label="Tìm trong bảng tin">
            <button type="submit" class="btn btn-primary" aria-label="Tìm"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @if ($keyword !== '' || $loai)
                <p class="bt-result-note">
                    Hiển thị <b>{{ $tins->total() }}</b> tin
                    @if ($loai) trong mục <b>{{ \App\Models\TinTuc::LOAI[$loai][0] }}</b>@endif
                    @if ($keyword !== '') cho từ khóa "<b>{{ $keyword }}</b>"@endif
                    · <a href="{{ route('bangtin.index') }}">Xóa lọc</a>
                </p>
            @endif

            @if ($noiBat)
                @include('BangTin._noibat', ['t' => $noiBat])
            @endif

            {{-- Dòng thời gian theo tháng --}}
            @if ($tins->isEmpty() && !$noiBat)
                @include('partials._empty', [
                    'message' => $keyword !== '' ? 'Không có tin nào khớp "' . $keyword . '".' : 'Chưa có tin nào trong mục này.',
                    'linkUrl' => route('bangtin.index'), 'linkText' => 'Xem tất cả tin', 'compact' => false,
                ])
            @else
                @foreach ($tins->getCollection()->groupBy(fn ($t) => $t->NgayDang->format('Y-m')) as $thang => $nhom)
                    <section class="bt-month">
                        <h2><span>Tháng {{ (int) substr($thang, 5, 2) }}, {{ substr($thang, 0, 4) }}</span><em>{{ $nhom->count() }} tin</em></h2>
                        <div class="bt-timeline">
                            @foreach ($nhom as $t)
                                @include('BangTin._dong', ['t' => $t])
                            @endforeach
                        </div>
                    </section>
                @endforeach
                @include('partials._pagination', ['items' => $tins])
            @endif
        </div>

        <aside class="col-lg-4">
            @include('BangTin._cotphu')
        </aside>
    </div>
</div>
@endsection
