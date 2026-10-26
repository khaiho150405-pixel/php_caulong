{{-- Một dòng tin trên dòng thời gian. Tham số: $t (TinTuc), $nho (tùy chọn: bản gọn không có ngày bên trái) --}}
@php $nho = $nho ?? false; @endphp
<article class="bt-item loai-{{ $t->LoaiTin }} {{ $nho ? 'is-small' : '' }}">
    @unless ($nho)
        <div class="bt-when" aria-hidden="true">
            <b>{{ $t->NgayDang->format('d') }}</b>
            <span>Th{{ $t->NgayDang->format('m') }}</span>
        </div>
    @endunless
    <div class="bt-item-main">
        <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="bt-thumb" tabindex="-1" aria-hidden="true">
            @if ($t->sanPham)
                <img src="{{ asset($t->anh) }}" alt="" loading="lazy" decoding="async" width="160" height="120">
            @elseif ($t->tranDau)
                <span class="bt-thumb-score"><b>{{ $t->tranDau->TiSo ? explode(',', $t->tranDau->TiSo)[0] : 'VS' }}</b><small>{{ $t->tranDau->NoiDung }}</small></span>
            @else
                <i class="fa-solid {{ $t->iconLoai }}"></i>
            @endif
        </a>
        <div class="bt-item-body">
            <div class="bt-item-top">
                <span class="bt-pill"><i class="fa-solid {{ $t->iconLoai }}"></i> {{ $t->nhanLoai }}</span>
                <time datetime="{{ $t->NgayDang->format('Y-m-d') }}" title="{{ $t->NgayDang->format('d/m/Y H:i') }}">{{ $t->NgayDang->diffForHumans() }}</time>
            </div>
            <h3><a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}">{{ $t->TieuDe }}</a></h3>
            @if ($t->TomTat)<p>{{ $t->TomTat }}</p>@endif
            <div class="bt-item-foot"><span><i class="fa-regular fa-eye"></i> {{ number_format($t->LuotXem) }}</span>@if ($t->NguonTin)<span><i class="fa-solid fa-link"></i> {{ $t->NguonTin }}</span>@endif</div>
        </div>
    </div>
</article>
