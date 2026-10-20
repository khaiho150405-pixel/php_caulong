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

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

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
                @foreach ($danhSach as $item)
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
                            <a href="{{ route('quanlytaikhoan.delete', ['id' => $item->MaTaiKhoan]) }}" class="btn btn-sm btn-outline-danger">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
