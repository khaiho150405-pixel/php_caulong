@extends('layouts.admin')

@section('title', 'Kết quả trận đấu - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-table-tennis-paddle-ball text-primary"></i> Kết quả trận đấu</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('quanlybantin.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-newspaper"></i> Bài viết</a>
            <a href="{{ route('quanlytrandau.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm trận đấu</a>
        </div>
    </div>

    <form action="{{ route('quanlytrandau.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo giải đấu, nội dung, tên tuyển thủ/cặp đấu..." />
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '')
                <a href="{{ route('quanlytrandau.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ngày</th>
                    <th>Giải đấu</th>
                    <th>Nội dung</th>
                    <th>Trận đấu</th>
                    <th>Tỉ số</th>
                    <th class="text-center">Bài viết</th>
                    <th style="width: 150px;">Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $td)
                    <tr>
                        <td class="text-nowrap">{{ $td->NgayThiDau->format('d/m/Y') }}</td>
                        <td>{{ $td->TenGiaiDau }}<br><small class="text-muted">{{ $td->Vong }}</small></td>
                        <td>{{ $td->NoiDung }}</td>
                        <td class="small">
                            <span class="{{ $td->DoiThang === 'A' ? 'fw-bold' : '' }}">{{ $td->DoiA }}</span><br>
                            <span class="{{ $td->DoiThang === 'B' ? 'fw-bold' : '' }}">{{ $td->DoiB }}</span>
                        </td>
                        <td class="fw-bold text-nowrap">{{ $td->TiSo ?: '—' }}</td>
                        <td class="text-center"><span class="badge bg-secondary">{{ $td->tin_tucs_count }}</span></td>
                        <td>
                            <a href="{{ route('quanlytrandau.edit', ['id' => $td->MaTranDau]) }}" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen"></i> Sửa</a>
                            <a href="{{ route('quanlytrandau.delete', ['id' => $td->MaTranDau]) }}" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Xóa</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-0">@include('partials._empty', ['message' => 'Chưa có trận đấu nào phù hợp.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $list])
</div>
@endsection
