{{-- Bài viết liên quan, có ảnh bìa thay cho biểu tượng hoặc con số trang trí. --}}
<article class="bt-card">
    <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-media {{ $t->sanPham ? 'is-product' : 'is-editorial' }}" aria-label="{{ $t->TieuDe }}"><img src="{{ asset($t->anh ?: 'images/news/court-journal-thumb.webp') }}" alt="{{ $t->sanPham ? $t->sanPham->TenSanPham : 'Ảnh minh họa sân cầu lông' }}" loading="lazy" decoding="async" width="640" height="400" onerror="this.onerror=null;this.src='{{ asset('images/news/court-journal-thumb.webp') }}'"></a>
    <div class="bt-body"><div class="bt-item-top"><span class="bt-pill">{{ $t->nhanLoai }}</span><time datetime="{{ $t->NgayDang->toIso8601String() }}">{{ $t->NgayDang->format('d/m/Y') }}</time></div><h3><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h3>@if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif</div>
</article>
