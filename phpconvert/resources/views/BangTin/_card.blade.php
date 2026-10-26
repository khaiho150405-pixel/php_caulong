{{-- Thẻ tin trên bảng tin. Tham số: $t (TinTuc), $lon (tùy chọn: thẻ nổi bật) --}}
@php $lon = $lon ?? false; @endphp
<article class="bt-card {{ $lon ? 'is-featured' : '' }}">
    <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-media loai-{{ $t->LoaiTin }}" aria-label="{{ $t->TieuDe }}">
        @if ($t->sanPham)
            <img src="{{ asset($t->anh) }}" alt="{{ $t->sanPham->TenSanPham }}" loading="lazy" decoding="async" width="360" height="270">
        @elseif ($t->tranDau)
            <span class="bt-media-score">
                <small>{{ $t->tranDau->NoiDung }} · {{ $t->tranDau->Vong }}</small>
                <b>{{ $t->tranDau->TiSo ?: 'VS' }}</b>
                <em>{{ $t->tranDau->tenDoiThang ?? $t->tranDau->TenGiaiDau }}</em>
            </span>
        @else
            <i class="fa-solid {{ $t->iconLoai }} bt-media-icon"></i>
        @endif
        <span class="bt-tag"><i class="fa-solid {{ $t->iconLoai }}"></i> {{ $t->nhanLoai }}</span>
    </a>
    <div class="bt-body">
        <div class="bt-meta"><time datetime="{{ $t->NgayDang->format('Y-m-d') }}">{{ $t->NgayDang->format('d/m/Y') }}</time><span><i class="fa-regular fa-eye"></i> {{ number_format($t->LuotXem) }}</span></div>
        <h3><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h3>
        @if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif
    </div>
</article>
