@extends('layouts.app')

@section('title', 'Cổng thanh toán MoMo (Môi trường Sandbox) - Shop Cầu Lông')

@section('content')
<div class="container py-4 my-2" style="max-width: 680px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        
        <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #a50064 0%, #d82d8b 100%);">
            <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle p-2 shadow-sm mb-2" style="width: 60px; height: 60px;">
                <img src="{{ asset('images/momo-logo.png') }}" alt="MoMo Logo" style="width: 44px; height: 44px; object-fit: contain;">
            </div>
            <h4 class="fw-bold mb-1">CỔNG THANH TOÁN VÍ MOMO</h4>
            <span class="badge bg-white text-dark px-3 py-1 rounded-pill fw-semibold" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                MÔI TRƯỜNG GIẢ LẬP SANDBOX (DEMO)
            </span>
        </div>

        <div class="card-body p-4 p-md-5">
            
            <div class="bg-light p-3 rounded-3 mb-4 border">
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="text-muted">Đơn vị nhận:</span>
                    <strong class="text-dark">Shop Cầu Lông</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="text-muted">Mã đơn hàng:</span>
                    <span class="badge bg-secondary">#{{ $order->MaDonHang }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-1">
                    <span class="text-muted">Người nhận:</span>
                    <span class="fw-medium">{{ $order->khachHang->HoTen ?? 'Khách hàng' }} - {{ $order->SoDienThoaiNhanHang }}</span>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center pt-1">
                    <span class="fw-bold fs-5 text-dark">Số tiền cần thanh toán:</span>
                    <span class="fw-bold fs-4" style="color: #a50064;">{{ number_format($order->TongTien, 0, ',', '.') }} đ</span>
                </div>
            </div>

            
            <div class="text-center p-3 mb-4 rounded-3 border bg-white" style="border: 2px dashed #d82d8b !important;">
                <div class="mb-2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=2|99|0901234567|Shop%20Cau%20Long|{{ (int)$order->TongTien }}|0|0|{{ $order->MaDonHang }}" 
                         alt="Mã QR thanh toán MoMo" 
                         class="img-fluid rounded shadow-sm border p-2 bg-white" 
                         style="width: 170px; height: 170px;"
                         onerror="this.parentElement.innerHTML='<div class=\'py-4 text-muted\'><i class=\'fa-solid fa-qrcode fa-5x text-secondary\'></i><p class=\'mt-2 mb-0 small\'>Mã QR thanh toán</p></div>'">
                </div>
                <small class="text-muted d-block">
                    <i class="fa-solid fa-mobile-screen-button me-1"></i> Quét mã bằng ứng dụng MoMo trên điện thoại hoặc chọn thao tác mô phỏng bên dưới
                </small>
            </div>

            
            <div class="d-flex flex-column gap-2">
                <form action="{{ route('donhang.momo_mock_process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="orderId" value="{{ $order->MaDonHang }}">
                    <input type="hidden" name="action" value="success">
                    <button type="submit" class="btn w-100 py-3 text-white fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #a50064 0%, #d82d8b 100%); border: none; border-radius: 12px; font-size: 1.05rem;">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                        <span>Xác nhận Đã thanh toán thành công (Mô phỏng)</span>
                    </button>
                </form>

                <form action="{{ route('donhang.momo_mock_process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="orderId" value="{{ $order->MaDonHang }}">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn btn-outline-secondary w-100 py-2 fw-semibold border-0 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.95rem;">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Hủy giao dịch thanh toán</span>
                    </button>
                </form>
            </div>

            <div class="alert alert-info py-2 px-3 mt-4 mb-0 small text-center" style="background-color: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                <i class="fa-solid fa-circle-info me-1"></i>
                Chế độ giả lập Sandbox giúp đảm bảo đồ án luôn hoạt động thông suốt khi demo / chấm thi mà không phụ thuộc vào kết nối API bên thứ 3.
            </div>
        </div>
    </div>
</div>
@endsection

