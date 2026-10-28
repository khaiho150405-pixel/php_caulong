@extends('layouts.admin')

@section('title', 'Xóa bài - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 700px; margin: 0 auto;">
    <h2 class="h4 mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Xác nhận xóa bài viết</h2>
    <hr />
    <p>Bạn có chắc muốn xóa bài viết sau khỏi bảng tin? (Bài được xóa mềm nên có thể khôi phục trong CSDL.)</p>

    <table class="table">
        <tr><th style="width: 160px;">Tiêu đề</th><td>{{ $tin->TieuDe }}</td></tr>
        <tr><th>Loại tin</th><td>{{ $tin->nhanLoai }}</td></tr>
        <tr><th>Ngày đăng</th><td>{{ $tin->NgayDang->format('d/m/Y H:i') }}</td></tr>
        <tr><th>Lượt xem</th><td>{{ number_format($tin->LuotXem) }}</td></tr>
    </table>

    <form action="{{ route('quanlybantin.delete.post', ['id' => $tin->MaTin]) }}" method="POST" class="d-flex gap-2">
        @csrf
        <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Xóa bài viết</button>
        <a href="{{ route('quanlybantin.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
