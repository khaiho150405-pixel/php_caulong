@extends('layouts.admin')

@section('title', 'Xóa Tài Khoản - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 600px; margin: 0 auto;">
    <h2 class="h4 mb-0 fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation"></i> Xác Nhận Xóa Tài Khoản</h2>
    <hr />

    <div class="alert alert-warning">
        Bạn có chắc chắn muốn xóa tài khoản <strong>"{{ $taiKhoan->TenDangNhap }}"</strong> ({{ $taiKhoan->khachHang->HoTen ?? 'Khách hàng' }}) không?
    </div>

    <table class="table table-bordered">
        <tr>
            <th style="width: 35%; background: #f9f9f9;">Mã tài khoản:</th>
            <td>#{{ $taiKhoan->MaTaiKhoan }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Tên đăng nhập:</th>
            <td class="fw-bold">{{ $taiKhoan->TenDangNhap }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Họ và tên:</th>
            <td>{{ $taiKhoan->khachHang->HoTen ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Email:</th>
            <td>{{ $taiKhoan->Email }}</td>
        </tr>
        <tr>
            <th style="background: #f9f9f9;">Phân quyền:</th>
            <td>{{ $taiKhoan->phanQuyen->TenQuyen ?? 'N/A' }}</td>
        </tr>
    </table>

    <form action="{{ route('quanlytaikhoan.delete', ['id' => $taiKhoan->MaTaiKhoan]) }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger px-4">
            <i class="fa-solid fa-trash"></i> Xác Nhận Xóa
        </button>
        <a href="{{ route('quanlytaikhoan.index') }}" class="btn btn-secondary px-4 ms-2">Hủy Bỏ</a>
    </form>
</div>
@endsection
