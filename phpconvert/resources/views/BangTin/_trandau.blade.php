{{-- Thẻ kết quả một trận đấu. Tham số: $tran (TranDau), $gon (tùy chọn: hiển thị gọn trong cột bên) --}}
@php $coKetQua = $tran->DoiThang && $tran->TiSo; @endphp
<div class="bt-match {{ ($gon ?? false) ? 'is-compact' : '' }}">
    <div class="bt-match-head">
        <span>{{ $tran->NoiDung }} · {{ $tran->Vong }}</span>
        <time datetime="{{ $tran->NgayThiDau->format('Y-m-d') }}">{{ $tran->NgayThiDau->format('d/m/Y') }}</time>
    </div>
    <div class="bt-side {{ $tran->DoiThang === 'A' ? 'is-win' : '' }}">
        <span class="bt-name">@if ($tran->DoiThang === 'A')<i class="fa-solid fa-circle-check"></i>@endif {{ $tran->DoiA }}</span>
    </div>
    <div class="bt-score">{{ $coKetQua ? $tran->TiSo : ($tran->DoiThang ? 'Đã kết thúc' : 'VS') }}</div>
    <div class="bt-side {{ $tran->DoiThang === 'B' ? 'is-win' : '' }}">
        <span class="bt-name">@if ($tran->DoiThang === 'B')<i class="fa-solid fa-circle-check"></i>@endif {{ $tran->DoiB }}</span>
    </div>
    @unless ($gon ?? false)
        <div class="bt-match-foot"><i class="fa-solid fa-trophy"></i> {{ $tran->TenGiaiDau }}@if ($tran->DiaDiem) · {{ $tran->DiaDiem }}@endif</div>
    @endunless
</div>
