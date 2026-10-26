{{-- Dải tin nhanh chạy ngang. Tham số: $ds (collection TranDau). Nội dung lặp hai lần để vòng lặp liền mạch. --}}
@if ($ds->isNotEmpty())
    <div class="bt-ticker" role="region" aria-label="Tin nhanh kết quả trận đấu">
        <span class="bt-ticker-label"><i class="fa-solid fa-bolt"></i> Tin nhanh</span>
        <div class="bt-ticker-view">
            <div class="bt-ticker-track">
                @foreach ([1, 2] as $lap)
                    <ul @if ($lap === 2) aria-hidden="true" @endif>
                        @foreach ($ds as $tran)
                            <li>
                                <em>{{ $tran->NoiDung }}</em>
                                <strong>{{ $tran->tenDoiThang ?? $tran->DoiA }}</strong>
                                @if ($tran->TiSo)<span>{{ $tran->TiSo }}</span>@else<span>thắng</span>@endif
                                {{ $tran->DoiThang === 'A' ? $tran->DoiB : $tran->DoiA }}
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
@endif
