{{-- Một bài viết trong danh sách tin tức. --}}
@php $nho = $nho ?? false; @endphp
<article class="bt-item loai-{{ $t->LoaiTin }} {{ $nho ? 'is-small' : '' }}">
    @unless ($nho)<div class="bt-when" aria-hidden="true"><b>{{ $t->NgayDang->format('d') }}</b><span>Tháng {{ $t->NgayDang->format('m') }}</span></div>@endunless
    <div class="bt-item-main">
        <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-thumb {{ $t->sanPham ? 'is-product' : 'is-editorial' }}" tabindex="-1" aria-hidden="true"><img src="{{ asset($t->anh ?: 'images/news/court-journal-thumb.webp') }}" alt="" loading="lazy" decoding="async" width="160" height="110" onerror="this.onerror=null;this.src='{{ asset('images/news/court-journal-thumb.webp') }}'"></a>
        <div class="bt-item-body">
            <div class="bt-item-top"><span class="bt-pill">{{ $t->nhanLoai }}</span><time datetime="{{ $t->NgayDang->toIso8601String() }}">{{ $t->NgayDang->format('d/m/Y') }}</time></div>
            <h3><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h3>
            @if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif
            @if ($t->NguonTin)<div class="bt-item-foot"><span>Nguồn: {{ $t->NguonTin }}</span></div>@endif
        </div>
    </div>
</article>
