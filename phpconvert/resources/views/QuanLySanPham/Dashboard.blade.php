@extends('layouts.admin')

@section('title', 'Dashboard Thống Kê Doanh Thu - Admin Shop')

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold"><i class="fa-solid fa-chart-pie text-primary"></i> Dashboard Thống Kê & Báo Cáo</h2>
            <p class="text-muted mb-0">Thống kê doanh thu theo năm {{ $namHienTai }}, quý, tháng và top sản phẩm bán chạy</p>
        </div>
        <div>
            <form action="{{ route('quanlysanpham.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                <label for="nam" class="fw-semibold mb-0">Năm:</label>
                <select name="nam" id="nam" class="form-select form-select-sm" onchange="this.form.submit()">
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $namHienTai == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-primary text-white p-3 rounded-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Doanh Thu Năm {{ $namHienTai }}</div>
                        <div class="fs-4 fw-bold mt-1">{{ number_format($tongDoanhThuNam, 0, ',', '.') }} đ</div>
                    </div>
                    <i class="fa-solid fa-sack-dollar fs-1 text-white-50"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 bg-success text-white p-3 rounded-3 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Tổng Đơn Hàng</div>
                        <div class="fs-4 fw-bold mt-1">{{ $soLuongDonNam }} đơn</div>
                    </div>
                    <i class="fa-solid fa-cart-shopping fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-column text-primary"></i> Doanh thu 12 tháng (Năm {{ $namHienTai }})</h5>
                <canvas id="doanhThuThangChart" height="150"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie text-success"></i> Doanh thu 4 Quý (Năm {{ $namHienTai }})</h5>
                <canvas id="doanhThuQuyChart" height="150"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables row -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-fire text-danger"></i> Top 10 Sản phẩm bán chạy</h5>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng bán</th>
                                <th class="text-end">Doanh thu mang lại</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sanPhamBanChay as $idx => $sp)
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('images/' . ($sp->HinhAnhDaiDien ?: 'default-product.jpg')) }}" width="40" height="40" class="rounded border" style="object-fit: contain;" />
                                            <strong>{{ $sp->TenSanPham }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-success">{{ $sp->TongSoLuongBan }}</td>
                                    <td class="text-end fw-bold text-danger">{{ number_format($sp->TongDoanhThu, 0, ',', '.') }} đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Chưa có dữ liệu bán hàng.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-calendar-days text-info"></i> Chi tiết theo quý</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Quý</th>
                                <th class="text-center">Số đơn</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($doanhThuQuy as $q)
                                <tr>
                                    <td class="fw-bold">Quý {{ $q->Quy }}</td>
                                    <td class="text-center">{{ $q->SoLuongDon }}</td>
                                    <td class="text-end fw-bold text-primary">{{ number_format($q->TongDoanhThu, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Chart Doanh thu Tháng
    const ctxThang = document.getElementById('doanhThuThangChart').getContext('2d');
    const thangLabels = @json(collect($doanhThuThang)->pluck('Thang')->map(fn($m) => 'Tháng ' . $m));
    const thangData = @json(collect($doanhThuThang)->pluck('TongDoanhThu'));

    new Chart(ctxThang, {
        type: 'bar',
        data: {
            labels: thangLabels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: thangData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // 2. Chart Doanh thu Quý
    const ctxQuy = document.getElementById('doanhThuQuyChart').getContext('2d');
    const quyLabels = @json(collect($doanhThuQuy)->map(fn($q) => 'Quý ' . $q->Quy));
    const quyData = @json(collect($doanhThuQuy)->pluck('TongDoanhThu'));

    new Chart(ctxQuy, {
        type: 'doughnut',
        data: {
            labels: quyLabels,
            datasets: [{
                data: quyData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
