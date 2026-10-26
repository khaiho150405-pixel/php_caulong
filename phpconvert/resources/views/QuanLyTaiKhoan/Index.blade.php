@extends('layouts.admin')

@section('title', 'Quản lý Tài khoản - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-users-gear text-primary"></i> Quản Lý Tài Khoản</h2>
        <a href="{{ route('quanlytaikhoan.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Thêm tài khoản mới
        </a>
    </div>

    <form action="{{ route('quanlytaikhoan.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="keyword" value="{{ $keyword }}" class="form-control" placeholder="Tìm theo tên đăng nhập, email, họ tên..." />
        </div>
        <div class="col-md-3">
            <select name="maQuyen" class="form-select">
                <option value="">-- Tất cả quyền --</option>
                @foreach ($phanQuyens as $pq)
                    <option value="{{ $pq->MaQuyen }}" @selected((string) $maQuyen === (string) $pq->MaQuyen)>{{ $pq->TenQuyen }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
            @if ($keyword !== '' || $maQuyen)
                <a href="{{ route('quanlytaikhoan.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th>Tên Đăng Nhập</th>
                    <th>Họ và Tên</th>
                    <th>Email</th>
                    <th>Phân Quyền</th>
                    <th>Ngày Tạo</th>
                    <th style="width: 150px;">Chức Năng</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($danhSach as $item)
                    <tr>
                        <td class="fw-bold">#{{ $item->MaTaiKhoan }}</td>
                        <td>{{ $item->TenDangNhap }}</td>
                        <td>{{ $item->khachHang->HoTen ?? 'N/A' }}</td>
                        <td>{{ $item->Email }}</td>
                        <td>
                            @if ($item->MaQuyen == 1)
                                <span class="badge bg-danger">Admin</span>
                            @elseif ($item->MaQuyen == 2)
                                <span class="badge bg-warning text-dark">Nhân Viên</span>
                            @else
                                <span class="badge bg-secondary">Khách Hàng</span>
                            @endif
                        </td>
                        <td>{{ $item->NgayTao ? \Carbon\Carbon::parse($item->NgayTao)->format('d/m/Y') : '' }}</td>
                        <td>
                            <a href="{{ route('quanlytaikhoan.edit', ['id' => $item->MaTaiKhoan]) }}" class="btn btn-sm btn-outline-warning">
                                <i class="fa-solid fa-pen"></i> Sửa
                            </a>
                            @if ($item->MaTaiKhoan !== Auth::id())
                                <a href="{{ route('quanlytaikhoan.delete', ['id' => $item->MaTaiKhoan]) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-0">@include('partials._empty', ['message' => 'Không tìm thấy tài khoản nào.'])</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('partials._pagination', ['items' => $danhSach])
</div>
@endsection
