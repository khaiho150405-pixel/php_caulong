{{-- Tin nổi bật: ảnh bìa và nội dung bài viết, giữ ảnh sản phẩm nếu có. --}}
<article class="bt-lead loai-{{ $t->LoaiTin }}">
    <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-lead-media {{ $t->sanPham ? 'is-product' : 'is-editorial' }}" aria-hidden="true" tabindex="-1">
        <img src="{{ asset($t->anh ?: 'images/news/court-journal-800.webp') }}" alt="" loading="lazy" decoding="async" width="800" height="500" onerror="this.onerror=null;this.src='{{ asset('images/news/court-journal-800.webp') }}'">
        @unless ($t->sanPham)<span class="bt-photo-credit">Ảnh minh họa</span>@endunless
    </a>
    <div class="bt-lead-body">
        <span class="bt-ribbon">Tin nổi bật</span>
        <div class="bt-item-top"><span class="bt-pill">{{ $t->nhanLoai }}</span><time datetime="{{ $t->NgayDang->toIso8601String() }}">{{ $t->NgayDang->format('d/m/Y') }}</time></div>
        <h2><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h2>
        @if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif
        <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-more">Đọc bài viết <span aria-hidden="true">↗</span></a>
    </div>
</article>
