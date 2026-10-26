@extends('layouts.admin')

@section('title', 'Xóa trận đấu - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 700px; margin: 0 auto;">
    <h2 class="h4 mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Xác nhận xóa trận đấu</h2>
    <hr />
    <p>Bạn có chắc muốn xóa kết quả trận đấu này?</p>

    <table class="table">
        <tr><th style="width: 160px;">Giải đấu</th><td>{{ $tran->TenGiaiDau }} · {{ $tran->Vong }}</td></tr>
        <tr><th>Nội dung</th><td>{{ $tran->NoiDung }}</td></tr>
        <tr><th>Trận đấu</th><td>{{ $tran->DoiA }} <strong>vs</strong> {{ $tran->DoiB }}</td></tr>
        <tr><th>Tỉ số</th><td>{{ $tran->TiSo ?: '—' }}</td></tr>
        <tr><th>Ngày</th><td>{{ $tran->NgayThiDau->format('d/m/Y') }}</td></tr>
    </table>

    @if ($tran->tin_tucs_count > 0)
        <div class="alert alert-warning">
            Có <strong>{{ $tran->tin_tucs_count }}</strong> bài viết đang gắn với trận này. Các bài vẫn được giữ lại, chỉ mất liên kết với trận đấu.
        </div>
    @endif

    <form action="{{ route('quanlytrandau.delete.post', ['id' => $tran->MaTranDau]) }}" method="POST" class="d-flex gap-2">
        @csrf
        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Xóa trận đấu</button>
        <a href="{{ route('quanlytrandau.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
