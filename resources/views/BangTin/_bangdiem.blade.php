
<section class="bt-scoreboard" aria-label="Kết quả trận đấu">
    <header class="bt-scoreboard-heading">
        <div><span class="bt-scoreboard-kicker">TRÊN SÂN CẦU</span><h2>{{ $tieuDe ?? 'Kết quả thi đấu' }}</h2></div>
        <a href="{{ route('bangtin.index', ['loai' => 'tran-dau']) }}" aria-label="Xem tin trận đấu"><span aria-hidden="true">↗</span></a>
    </header>
    @forelse ($ds as $tran)
        @php
            $diem = collect($tran->TiSo ? explode(',', $tran->TiSo) : [])->take(3)->map(function ($van) {
                return preg_match('/^\s*(\d{1,2})\s*[-–]\s*(\d{1,2})\s*$/u', $van, $ketQua)
                    ? [(int) $ketQua[1], (int) $ketQua[2]] : null;
            })->values();
            $tin = $tran->relationLoaded('tinTucs') ? $tran->tinTucs->first() : null;
            $coDoiThang = in_array($tran->DoiThang, ['A', 'B'], true);
        @endphp
        <article class="bt-sb-match">
            <div class="bt-sb-competition"><span>{{ $tran->TenGiaiDau }}</span><span class="bt-sb-status">{{ $coDoiThang ? 'Kết thúc' : 'Chưa có kết quả' }}</span></div>
            <div class="bt-sb-meta"><span>{{ $tran->NoiDung }} · {{ $tran->Vong }}</span><time datetime="{{ $tran->NgayThiDau->format('Y-m-d') }}">{{ $tran->NgayThiDau->format('d.m.Y') }}</time></div>
            <table class="bt-sb-table">
                <caption class="visually-hidden">{{ $tran->DoiA }} gặp {{ $tran->DoiB }} — {{ $tran->TenGiaiDau }}</caption>
                <thead><tr><th scope="col">VĐV / Đội</th>@for ($i = 1; $i <= 3; $i++)<th scope="col"><span class="visually-hidden">Ván </span>{{ $i }}</th>@endfor</tr></thead>
                <tbody>
                    @foreach (['A' => 0, 'B' => 1] as $ben => $chiSo)
                    <tr class="{{ $tran->DoiThang === $ben ? 'is-win' : '' }}">
                        <th scope="row"><span class="bt-sb-name">{{ $ben === 'A' ? $tran->DoiA : $tran->DoiB }}</span>@if ($tran->DoiThang === $ben)<span class="bt-sb-winner"><span aria-hidden="true">✓</span><span class="visually-hidden"> — Thắng trận</span></span>@endif</th>
                        @for ($i = 0; $i < 3; $i++)
                            @php $van = $diem->get($i); @endphp
                            <td class="{{ $van && $van[$chiSo] > $van[1 - $chiSo] ? 'won' : '' }}">{{ $van ? $van[$chiSo] : '—' }}</td>
                        @endfor
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if (!$tran->TiSo)<p class="bt-sb-note">Chưa cập nhật điểm từng ván.</p>@elseif ($diem->contains(null))<p class="bt-sb-note">Điểm từng ván chưa đầy đủ.</p>@endif
            @if ($tin)<a class="bt-sb-report" href="{{ route('bangtin.show', ['id' => $tin->MaTin]) }}">Đọc tin trận đấu <span aria-hidden="true">→</span></a>@endif
        </article>
    @empty
        <p class="bt-sb-empty">Chưa có kết quả nào.</p>
    @endforelse
</section>
