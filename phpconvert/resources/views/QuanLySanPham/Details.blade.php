@extends('layouts.admin')

@section('title', 'Chi tiết Sản phẩm - Admin Shop')

@section('content')
<div class="card card-custom p-4" style="max-width: 1000px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 fw-bold"><i class="fa-solid fa-circle-info text-info"></i> Chi tiết Sản phẩm #{{ $sanPham->MaSanPham }}</h2>
        <div>
            <a href="{{ route('quanlysanpham.edit', ['id' => $sanPham->MaSanPham]) }}" class="btn btn-warning btn-sm">
                <i class="fa-solid fa-pen-to-square"></i> Sửa
            </a>
            <a href="{{ route('quanlysanpham.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>
    <hr />

    <div class="row">
        <div class="col-md-4 text-center">
            <img src="{{ asset('images/' . ($sanPham->HinhAnhDaiDien ?: 'default-product.jpg')) }}"
                 class="img-fluid rounded border p-2"
                 style="max-height: 280px; object-fit: contain;"
                 alt="{{ $sanPham->TenSanPham }}"
                 onerror="this.src='{{ asset('images/default-product.jpg') }}'" />
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 30%; background: #f9f9f9;">Tên sản phẩm:</th>
                    <td class="fw-bold">{{ $sanPham->TenSanPham }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Giá gốc:</th>
                    <td class="fw-bold text-price">{{ number_format($sanPham->GiaGoc, 0, ',', '.') }} VNĐ</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Loại sản phẩm:</th>
                    <td>{{ $sanPham->loaiSanPham->TenLoai ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Hãng sản xuất:</th>
                    <td>{{ $sanPham->hang->TenHang ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Nhà cung cấp:</th>
                    <td>{{ $sanPham->nhaCungCap->TenNhaCungCap ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Khuyến mãi:</th>
                    <td>{{ $sanPham->khuyenMai->TenChuongTrinh ?? 'Không có' }}</td>
                </tr>
                <tr>
                    <th style="background: #f9f9f9;">Ngày tạo:</th>
                    <td>{{ $sanPham->NgayTao ? \Carbon\Carbon::parse($sanPham->NgayTao)->format('d/m/Y H:i') : 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if ($sanPham->MoTa)
        <div class="mt-3">
            <h5 class="fw-bold">Mô tả sản phẩm</h5>
            <div class="p-3 bg-light rounded border text-secondary" style="white-space: pre-line;">
                {{ $sanPham->MoTa }}
            </div>
        </div>
    @endif

    <div class="mt-4">
        <h5 class="fw-bold">Danh Sách Biến Thể Chi Tiết ({{ $sanPham->chiTietSanPhams->count() }})</h5>
        <p class="text-muted small mb-2">Mỗi biến thể (màu / size) có giá bán, tồn kho và mã SKU riêng. Khách chỉ đặt mua được khi sản phẩm có ít nhất 1 biến thể còn hàng.</p>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã</th>
                        <th>SKU</th>
                        <th>Hình Ảnh</th>
                        <th>Màu Sắc</th>
                        <th>Size</th>
                        <th>Giá Bán</th>
                        <th>Tồn Kho</th>
                        <th style="width: 150px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sanPham->chiTietSanPhams as $v)
                        <tr>
                            <td>#{{ $v->MaChiTiet }}</td>
                            <td><code>{{ $v->SKU }}</code></td>
                            <td>
                                @if ($v->HinhAnh)
                                    <img src="{{ asset('images/' . $v->HinhAnh) }}" width="45" height="45" style="object-fit: contain;" class="rounded border" alt="{{ $v->SKU }}" />
                                @else
                                    <span class="text-muted">Mặc định</span>
                                @endif
                            </td>
                            <td>{{ $v->mauSac->TenMau ?? 'Mặc định' }}</td>
                            <td>{{ $v->size->TenSize ?? 'Tiêu chuẩn' }}</td>
                            <td class="fw-bold">{{ number_format($v->GiaBan, 0, ',', '.') }} VNĐ</td>
                            <td>
                                @if ($v->SoLuongTon > 0)
                                    <span class="badge bg-success">{{ $v->SoLuongTon }}</span>
                                @else
                                    <span class="badge bg-danger">Hết hàng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('quanlysanpham.bienthe.edit', ['maChiTiet' => $v->MaChiTiet]) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </a>
                                <form action="{{ route('quanlysanpham.bienthe.destroy', ['maChiTiet' => $v->MaChiTiet]) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa biến thể {{ $v->SKU }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có biến thể chi tiết nào được cấu hình.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form thêm biến thể mới --}}
    <div class="mt-3 p-3 border rounded bg-light">
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-plus text-primary"></i> Thêm biến thể mới</h6>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quanlysanpham.bienthe.store', ['id' => $sanPham->MaSanPham]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('QuanLySanPham._form_bien_the', ['bienThe' => null])
            <button type="submit" class="btn btn-primary mt-3"><i class="fa-solid fa-save"></i> Thêm biến thể</button>
        </form>
    </div>
</div>
@endsection
