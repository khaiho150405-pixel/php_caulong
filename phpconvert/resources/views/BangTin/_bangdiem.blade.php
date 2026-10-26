{{-- Bảng điểm kiểu tỉ số trận đấu. Tham số: $ds (collection TranDau, nên eager load tinTucs), $tieuDe (tùy chọn) --}}
<section class="bt-scoreboard" aria-label="Bảng điểm">
    <header>
        <h2><i class="fa-solid fa-table-tennis-paddle-ball"></i> {{ $tieuDe ?? 'Bảng điểm' }}</h2>
        <span class="bt-live"><i></i> Mới nhất</span>
    </header>

    @forelse ($ds as $tran)
        @php
            $vanDau = $tran->TiSo ? array_map(fn ($v) => array_map('intval', explode('-', trim($v))), explode(',', $tran->TiSo)) : [];
            $tin = $tran->relationLoaded('tinTucs') ? $tran->tinTucs->first() : null;
            $url = $tin ? route('bangtin.show', ['id' => $tin->MaTin]) : null;
        @endphp
        <{{ $url ? 'a href=' . $url : 'div' }} class="bt-sb-match">
            <div class="bt-sb-meta">
                <span>{{ $tran->NoiDung }} · {{ $tran->Vong }}</span>
                <time datetime="{{ $tran->NgayThiDau->format('Y-m-d') }}">{{ $tran->NgayThiDau->format('d/m/Y') }}</time>
            </div>
            @foreach (['A' => 0, 'B' => 1] as $ben => $chiSo)
                <div class="bt-sb-team {{ $tran->DoiThang === $ben ? 'is-win' : '' }}">
                    <span class="bt-sb-name">
                        @if ($tran->DoiThang === $ben)<i class="fa-solid fa-caret-right"></i>@endif
                        {{ $ben === 'A' ? $tran->DoiA : $tran->DoiB }}
                    </span>
                    <span class="bt-sb-sets">
                        @foreach ($vanDau as $van)
                            <b class="{{ $van[$chiSo] > $van[1 - $chiSo] ? 'won' : '' }}">{{ $van[$chiSo] }}</b>
                        @endforeach
                        @if (!$vanDau && $tran->DoiThang === $ben)<em>Thắng</em>@endif
                    </span>
                </div>
            @endforeach
            <div class="bt-sb-foot">{{ $tran->TenGiaiDau }}</div>
        </{{ $url ? 'a' : 'div' }}>
    @empty
        <p class="bt-sb-empty">Chưa có kết quả nào.</p>
    @endforelse
</section>
