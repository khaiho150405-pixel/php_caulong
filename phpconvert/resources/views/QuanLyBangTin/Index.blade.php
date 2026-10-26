@extends('layouts.admin')

@section('title', 'Quản lý Bảng tin - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-newspaper text-primary"></i> Bài viết trên bảng tin</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('quanlytrandau.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-table-tennis-paddle-ball"></i> Kết quả trận đấu</a>
            <a href="{{ route('quanlybantin.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Đăng bài mới</a>
        </div>
    </div>

    <form action="{{ route('quanlybantin.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tiêu đề, tóm tắt, nội dung..." />
        </div>
        <div class="col-md-3">
            <select name="loai" class="form-select">
                <option value="">-- Tất cả loại tin --</option>
                @foreach (\App\Models\TinTuc::LOAI as $khoa => [$nhan])
                    <option value="{{ $khoa }}" @selected($loai === $khoa)>{{ $nhan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '' || $loai)
                <a href="{{ route('quanlybantin.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tiêu đề</th>
                    <th>Loại</th>
                    <th>Gắn với</th>
                    <th>Ngày đăng</th>
                    <th class="text-end">Lượt xem</th>
                    <th style="width: 220px;">Chức năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $t)
                    <tr>
                        <td style="max-width: 380px;">
                            <strong>{{ $t->TieuDe }}</strong>
                            @if ($t->NoiBat)<span class="badge bg-warning ms-1">Nổi bật</span>@endif
                            @if ($t->NgayDang->isFuture())<span class="badge bg-info ms-1">Hẹn giờ</span>@endif
                        </td>
                        <td><span class="badge bg-secondary"><i class="fa-solid {{ $t->iconLoai }}"></i> {{ $t->nhanLoai }}</span></td>
                        <td class="small">
                            @if ($t->sanPham)<i class="fa-solid fa-box-archive text-muted"></i> {{ \Illuminate\Support\Str::limit($t->sanPham->TenSanPham, 32) }}<br>@endif
                            @if ($t->tranDau)<i class="fa-solid fa-trophy text-muted"></i> {{ $t->tranDau->NoiDung }} · {{ $t->tranDau->Vong }}@endif
                            @if (!$t->sanPham && !$t->tranDau)<span class="text-muted">—</span>@endif
                        </td>
                        <td class="text-nowrap">{{ $t->NgayDang->format('d/m/Y H:i') }}</td>
                        <td class="text-end">{{ number_format($t->LuotXem) }}</td>
                        <td>
                            <a href="{{ route('bangtin.show', ['id' => $t->MaTin]) }}" class="btn btn-sm btn-outline-info" target="_blank"><i class="fa-solid fa-eye"></i> Xem</a>
                            <a href="{{ route('quanlybantin.edit', ['id' => $t->MaTin]) }}" class="btn btn-sm btn-outline-warning"><i class="fa-solid fa-pen-to-square"></i> Sửa</a>
                            <a href="{{ route('quanlybantin.delete', ['id' => $t->MaTin]) }}" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Xóa</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-0">@include('partials._empty', ['message' => 'Chưa có bài viết nào phù hợp.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $list])
</div>
@endsection
