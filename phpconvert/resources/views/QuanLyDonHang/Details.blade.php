@extends('layouts.admin')

@section('title', 'Chi tiết Đơn hàng #' . $order->MaDonHang . ' - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-file-invoice text-primary"></i> Chi tiết Đơn hàng #{{ $order->MaDonHang }}</h2>
        <a href="{{ route('quanlydonhang.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Quay lại danh sách
        </a>
    </div>
    <hr />


    <div class="row g-4">
        <div class="col-lg-8">
            <h5 class="fw-bold mb-3">Thông Tin Đơn Hàng</h5>
            <table class="table table-bordered">
                <tr>
                    <th style="width: 25%; background: #f9f9f9;">Mã đơn:</th>
                    <td class="fw-bold">#{{ $order->MaDonHang }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Ngày đặt:</th>
                    <td>{{ $order->NgayDat ? \Carbon\Carbon::parse($order->NgayDat)->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Trạng thái hiện tại:</th>
                    <td>
                        <span class="badge bg-primary fs-6">{{ $order->TrangThai }}</span>
                    </td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Người đặt:</th>
                    <td>{{ $order->khachHang->HoTen ?? 'Khách lẻ' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Số điện thoại:</th>
                    <td>{{ $order->SoDienThoaiNhanHang }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Địa chỉ giao:</th>
                    <td>{{ $order->DiaChiGiaoHang }}</td>
                </tr>
                @if ($order->GhiChu)
                    <tr>
                        <th style="background: #f9f9f9;">Ghi chú:</th>
                        <td>{{ $order->GhiChu }}</td>
                    </tr>
                @endif
                <tr>
                    <th style="background: #f9f9f9;">Tổng tiền đơn hàng:</th>
                    <td class="fw-bold text-price fs-5">{{ number_format($order->TongTien, 0, ',', '.') }} VNĐ</td>
                </tr>
            </table>

            <h5 class="fw-bold mt-4 mb-3">Danh Sách Sản Phẩm ({{ $order->chiTietDonHangs->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th colspan="2">Sản phẩm</th>
                            <th>Phân loại</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->chiTietDonHangs as $item)
                            @php
                                $variant = $item->chiTietSanPham;
                                $sp = $variant ? $variant->sanPham : null;
                                $tenSP = $sp ? $sp->TenSanPham : 'Sản phẩm';
                                $img = $variant->HinhAnh ?: ($sp->HinhAnhDaiDien ?: 'default-product.jpg');
                            @endphp
                            <tr>
                                <td style="width: 65px;" class="text-center">
                                    <img src="{{ asset('images/' . $img) }}"
                                         width="50" height="50"
                                         style="object-fit: contain;"
                                         class="rounded border"
                                         onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
                                </td>
                                <td>
                                    <strong>{{ $tenSP }}</strong>
                                </td>
                                <td>
                                    @if ($variant && $variant->mauSac)
                                        <span>Màu: {{ $variant->mauSac->TenMau }}</span><br />
                                    @endif
                                    @if ($variant && $variant->size)
                                        <span>Size: {{ $variant->size->TenSize }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($item->DonGia, 0, ',', '.') }} VNĐ</td>
                                <td class="text-center fw-bold">{{ $item->SoLuong }}</td>
                                <td class="fw-bold text-price">{{ number_format($item->ThanhTien, 0, ',', '.') }} VNĐ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card p-3 border shadow-sm bg-light">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-sliders text-primary"></i> Cập Nhật Trạng Thái</h5>
                <p>Trạng thái hiện tại: <strong class="text-primary">{{ $order->TrangThai }}</strong></p>
                <hr />

                {{-- Nút chuyển sang "Đang xử lý" --}}
                @if ($order->TrangThai == 'Chờ xác nhận' || $order->TrangThai == 'Chờ thanh toán MoMo')
                    <form action="{{ route('quanlydonhang.settrangthai', ['id' => $order->MaDonHang]) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="trangThaiMoi" value="Đang xử lý">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-check"></i> Xác nhận (Chuyển sang Đang xử lý)
                        </button>
                    </form>
                @endif

                {{-- Nút chuyển sang "Đang giao hàng" --}}
                @if ($order->TrangThai == 'Đang xử lý' || $order->TrangThai == 'Đã thanh toán bằng MoMo')
                    <form action="{{ route('quanlydonhang.settrangthai', ['id' => $order->MaDonHang]) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="trangThaiMoi" value="Đang giao hàng">
                        <button type="submit" class="btn btn-info w-100 py-2 fw-semibold text-white">
                            <i class="fa-solid fa-truck-fast"></i> Chuyển sang Đang giao hàng
                        </button>
                    </form>
                @endif

                {{-- Nút chuyển sang "Đã hoàn thành" --}}
                @if ($order->TrangThai == 'Đang giao hàng')
                    <form action="{{ route('quanlydonhang.settrangthai', ['id' => $order->MaDonHang]) }}" method="POST" class="mb-2">
                        @csrf
                        <input type="hidden" name="trangThaiMoi" value="Đã hoàn thành">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-circle-check"></i> Đã hoàn thành (Giao thành công)
                        </button>
                    </form>
                @endif

                {{-- Nút Hủy đơn --}}
                @if ($order->TrangThai != 'Đã hoàn thành' && !str_contains($order->TrangThai, 'hủy') && !str_contains($order->TrangThai, 'Hủy'))
                    <form action="{{ route('quanlydonhang.settrangthai', ['id' => $order->MaDonHang]) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="trangThaiMoi" value="Đã hủy">
                        <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng tồn kho sẽ được hoàn lại tự động!');">
                            <i class="fa-solid fa-ban"></i> HỦY ĐƠN HÀNG
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
