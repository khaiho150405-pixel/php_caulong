
<header class="bt-editorial-banner {{ ($trangTin ?? false) ? 'bt-masthead' : '' }}">
    <picture class="bt-banner-picture">
        <source type="image/webp" srcset="{{ asset('images/news/court-journal-800.webp') }} 800w, {{ asset('images/news/court-journal-1600.webp') }} 1600w" sizes="(max-width: 767px) 100vw, 1140px">
        <img src="{{ asset('images/news/court-journal-1600.webp') }}" alt="Sân cầu lông trong nhà và quả cầu bên đường biên — ảnh minh họa" width="1600" height="667" loading="{{ ($trangTin ?? false) ? 'eager' : 'lazy' }}" decoding="async">
    </picture>
    <div class="bt-banner-copy">
        <span class="bt-banner-kicker">HUIT BADMINTON <span aria-hidden="true">/</span> GÓC CẦU LÔNG</span>
        @if ($trangTin ?? false)
            <h1>Tin tức &amp; sự kiện</h1>
        @else
            <h2>Tin tức &amp; sự kiện</h2>
        @endif
        <p>Giải đấu, tuyển thủ và những câu chuyện bên lề sân cầu.</p>
        <a href="{{ route('bangtin.index', ($trangTin ?? false) ? ['loai' => 'tran-dau'] : []) }}">{{ ($trangTin ?? false) ? 'Theo dõi các trận đấu' : 'Khám phá bảng tin' }} <span aria-hidden="true">↗</span></a>
    </div>
    <span class="bt-banner-credit">Ảnh minh họa</span>
</header>
