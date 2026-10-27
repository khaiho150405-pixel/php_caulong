{{-- Cột bên: bảng điểm, sản phẩm hot (xếp hạng), sản phẩm mới. Dữ liệu: $tranDauMoi, $spHot, $spMoi --}}
@php $hamTien = fn ($v) => number_format((float) $v, 0, ',', '.') . '₫'; @endphp

@include('BangTin._bangdiem', ['ds' => $tranDauMoi, 'tieuDe' => 'Kết quả thi đấu'])

<section class="bt-panel">
    <h2><i class="fa-solid fa-fire"></i> Sản phẩm đang hot</h2>
    <ol class="bt-rank">
        @foreach ($spHot as $sp)
            <li>
                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}">
                    <span class="bt-rank-no">{{ $loop->iteration }}</span>
                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="" width="52" height="52" loading="lazy" decoding="async">
                    <span class="bt-rank-text"><b>{{ $sp->TenSanPham }}</b><em>{{ $hamTien($sp->gia_sau_khuyen_mai) }}</em></span>
                </a>
            </li>
        @endforeach
    </ol>
</section>

<section class="bt-panel">
    <h2><i class="fa-solid fa-star"></i> Hàng mới về</h2>
    <ul class="bt-mini">
        @foreach ($spMoi as $sp)
            <li>
                <a href="{{ route('home.chitietsanpham', ['id' => $sp->MaSanPham]) }}">
                    <img src="{{ asset('images/' . $sp->anh_thumb) }}" alt="" width="52" height="52" loading="lazy" decoding="async">
                    <span><b>{{ $sp->TenSanPham }}</b><em>{{ $hamTien($sp->gia_sau_khuyen_mai) }}</em></span>
                </a>
            </li>
        @endforeach
    </ul>
</section>
