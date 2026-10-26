{{-- Tin nổi bật dạng ảnh bìa + tiêu đề lớn. Tham số: $t (TinTuc) --}}
<article class="bt-lead loai-{{ $t->LoaiTin }}">
    <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-lead-media" aria-hidden="true" tabindex="-1">
        @if ($t->sanPham)
            <img src="{{ asset($t->anh) }}" alt="" loading="eager" decoding="async" width="480" height="360">
        @elseif ($t->tranDau)
            <span class="bt-lead-score">
                <small>{{ $t->tranDau->NoiDung }} · {{ $t->tranDau->Vong }}</small>
                <b>{{ $t->tranDau->TiSo ?: 'VS' }}</b>
                <em>{{ $t->tranDau->tenDoiThang ?? $t->tranDau->TenGiaiDau }}</em>
            </span>
        @else
            <i class="fa-solid {{ $t->iconLoai }}"></i>
        @endif
    </a>
    <div class="bt-lead-body">
        <span class="bt-ribbon"><i class="fa-solid fa-bolt"></i> Tin nổi bật</span>
        <div class="bt-item-top">
            <span class="bt-pill"><i class="fa-solid {{ $t->iconLoai }}"></i> {{ $t->nhanLoai }}</span>
            <time datetime="{{ $t->NgayDang->format('Y-m-d') }}">{{ $t->NgayDang->format('d/m/Y') }}</time>
        </div>
        <h2><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h2>
        @if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif
        <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-more">Đọc tiếp <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</article>
