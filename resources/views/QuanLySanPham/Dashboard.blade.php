@extends('layouts.admin')

@section('title', 'Dashboard Thống Kê Doanh Thu - Admin Shop')

@section('content')
<div class="dashboard">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold d-flex align-items-center gap-2"><i class="fa-solid fa-chart-pie text-primary"></i> Dashboard Thống Kê & Báo Cáo</h2>
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

    
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card is-green">
                <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                <div>
                    <div class="stat-label">Doanh thu {{ $namHienTai }}</div>
                    <div class="stat-value">{{ number_format($tongDoanhThuNam, 0, ',', '.') }} đ</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card is-blue">
                <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <div>
                    <div class="stat-label">Đơn hàng</div>
                    <div class="stat-value">{{ $soLuongDonNam }} đơn</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card is-amber">
                <div class="stat-icon"><i class="fa-solid fa-box-archive"></i></div>
                <div>
                    <div class="stat-label">Sản phẩm</div>
                    <div class="stat-value">{{ $tongSanPham }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('quanlythongbao.thongbao') }}" class="stat-card is-red text-decoration-none text-reset">
                <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <div class="stat-label">Cảnh báo đánh giá</div>
                    <div class="stat-value">{{ $soCanhBao }}</div>
                </div>
            </a>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border shadow-sm p-3">
                <h5 class="mb-3"><i class="fa-solid fa-chart-column text-primary"></i> Doanh thu 12 tháng (Năm {{ $namHienTai }})</h5>
                <canvas id="doanhThuThangChart" height="150"></canvas>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie text-success"></i> Doanh thu 4 Quý (Năm {{ $namHienTai }})</h5>
                <div style="max-width: 260px; margin: 0 auto;"><canvas id="doanhThuQuyChart"></canvas></div>
            </div>
        </div>
    </div>

    
    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-fire text-price"></i> Top 10 Sản phẩm bán chạy</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($sanPhamBanChay as $idx => $sp)
                                <tr>
                                    <td><span class="rank-badge {{ $idx < 3 ? 'top' : '' }}">{{ $idx + 1 }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ asset('images/' . ($sp->HinhAnhDaiDien ?: 'default-product.jpg')) }}" width="40" height="40" class="rounded border bg-white p-1" style="object-fit: contain;" />
                                            <strong>{{ $sp->TenSanPham }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center fw-bold text-success">{{ $sp->TongSoLuongBan }}</td>
                                    <td class="text-end fw-bold text-price text-nowrap">{{ number_format($sp->TongDoanhThu, 0, ',', '.') }} đ</td>
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

        <div class="col-xl-5">
            <div class="card border shadow-sm p-3">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-calendar-days text-primary"></i> Chi tiết theo quý</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.color = '#66756e';

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
                backgroundColor: '#148f5f',
                hoverBackgroundColor: '#0f7550',
                borderWidth: 0,
                borderRadius: 6,
                maxBarThickness: 36
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#eef2f0' },
                    border: { display: false },
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

    const ctxQuy = document.getElementById('doanhThuQuyChart').getContext('2d');
    const quyLabels = @json(collect($doanhThuQuy)->map(fn($q) => 'Quý ' . $q->Quy));
    const quyData = @json(collect($doanhThuQuy)->pluck('TongDoanhThu'));

    new Chart(ctxQuy, {
        type: 'doughnut',
        data: {
            labels: quyLabels,
            datasets: [{
                data: quyData,
                backgroundColor: ['#0b3b2b', '#148f5f', '#6cc7a1', '#c7e8d9'],
                borderColor: '#ffffff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
            }
        }
    });
</script>
@endsection
