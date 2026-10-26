@extends('layouts.app')

@section('title', 'Kết quả thanh toán MoMo - Cửa Hàng Cầu Lông')

@section('content')
<div class="container" style="padding: 60px 15px; text-align: center;">
    @if ($isSuccess)
        <div class="alert alert-success" style="display: inline-block; padding: 40px; border-radius: 10px; max-width: 600px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <i class="fa-solid fa-circle-check" style="font-size: 60px; color: #28a745; margin-bottom: 20px;"></i>
            <h3 style="font-weight: 700; margin-bottom: 10px;">{{ $message }}</h3>
            <p style="font-size: 16px;">Mã đơn hàng: <strong>#{{ $maDonHang }}</strong></p>
            <p style="color: #666;">Cảm ơn quý khách đã mua sắm và thanh toán thành công qua MoMo.</p>

            <div style="margin-top: 25px; display: flex; gap: 15px; justify-content: center;">
                <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: var(--brand); border-color: var(--brand); font-weight: bold; padding: 10px 20px;">
                    Tiếp tục mua sắm
                </a>
                <a href="{{ route('donhang.index') }}" class="btn btn-default" style="font-weight: bold; padding: 10px 20px;">
                    Lịch sử đơn hàng
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-danger" style="display: inline-block; padding: 40px; border-radius: 10px; max-width: 600px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
            <i class="fa-solid fa-circle-xmark" style="font-size: 60px; color: #dc3545; margin-bottom: 20px;"></i>
            <h3 style="font-weight: 700; margin-bottom: 10px;">{{ $message }}</h3>
            <p style="color: #666;">{{ $description }}</p>
            <p>Đơn hàng đã được lưu với trạng thái chưa thanh toán hoặc đã hủy.</p>

            <div style="margin-top: 25px; display: flex; gap: 15px; justify-content: center;">
                <a href="{{ route('home') }}" class="btn btn-primary" style="background-color: var(--brand); border-color: var(--brand); font-weight: bold; padding: 10px 20px;">
                    Về trang chủ
                </a>
                <a href="{{ route('donhang.checkout') }}" class="btn btn-warning" style="font-weight: bold; padding: 10px 20px;">
                    Thử thanh toán lại
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
