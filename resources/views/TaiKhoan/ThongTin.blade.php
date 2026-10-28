@extends('layouts.app')

@section('title', 'Thông tin tài khoản - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 40px 15px; max-width: 650px;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
        <h2 style="font-size: 24px; font-weight: 700; color: #333; margin-top: 0;">Thông tin tài khoản</h2>
        <hr />

        <table class="table" style="font-size: 15px;">
            <tr>
                <th style="width: 35%; border: none; color: #555;">Tên đăng nhập:</th>
                <td style="border: none; font-weight: 600;">{{ $user->TenDangNhap }}</td>
            </tr>
            <tr>
                <th style="color: #555;">Họ và Tên:</th>
                <td style="font-weight: 600;">{{ $khachHang->HoTen ?? 'Chưa cập nhật' }}</td>
            </tr>
            <tr>
                <th style="color: #555;">Email:</th>
                <td>{{ $khachHang->Email ?? $user->Email ?? 'Chưa cập nhật' }}</td>
            </tr>
            <tr>
                <th style="color: #555;">Số điện thoại:</th>
                <td>{{ $khachHang->SoDienThoai ?? 'Chưa cập nhật' }}</td>
            </tr>
            <tr>
                <th style="color: #555;">Địa chỉ:</th>
                <td>{{ $khachHang->DiaChi ?? 'Chưa cập nhật' }}</td>
            </tr>
            <tr>
                <th style="color: #555;">Ngày đăng ký:</th>
                <td>{{ $user->NgayTao ? \Carbon\Carbon::parse($user->NgayTao)->format('d/m/Y H:i') : 'N/A' }}</td>
            </tr>
        </table>

        <div style="margin-top: 25px; display: flex; gap: 15px;">
            <a href="{{ route('taikhoan.doimatkhau') }}" class="btn btn-warning" style="font-weight: 600;">
                <i class="fa fa-key"></i> Đổi mật khẩu
            </a>
            <a href="{{ route('donhang.index') }}" class="btn btn-info" style="font-weight: 600;">
                <i class="fa fa-list"></i> Đơn hàng của tôi
            </a>
        </div>
    </div>
</div>
@endsection
