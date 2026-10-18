<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản Trị Hệ Thống - Shop Cầu Lông')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
        }
        .sidebar .nav-link {
            color: #c2c7d0;
            padding: 12px 20px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #494e53;
            border-left: 4px solid #007bff;
        }
        .admin-header {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 12px 24px;
        }
        .content-wrapper {
            padding: 24px;
        }
        .card-custom {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3 text-center border-bottom border-secondary">
                    <a href="{{ route('home') }}" class="text-white text-decoration-none d-flex align-items-center justify-content-center gap-2">
                        <img src="{{ asset('images/HUIT.jpg') }}" alt="Logo" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                        <span class="fs-5 fw-bold">ADMIN SHOP</span>
                    </a>
                </div>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a href="{{ route('quanlysanpham.dashboard') }}" class="nav-link {{ request()->is('*Dashboard*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie"></i> Dashboard Thống Kê
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('quanlysanpham.index') }}" class="nav-link {{ request()->is('QuanLySanPham*') && !request()->is('*Dashboard*') ? 'active' : '' }}">
                            <i class="fa-solid fa-box-archive"></i> Quản Lý Sản Phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('quanlydonhang.index') }}" class="nav-link {{ request()->is('QuanLyDonHang*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Quản Lý Đơn Hàng
                        </a>
                    </li>
                    @if ((int)(Auth::user()->MaQuyen ?? 0) === 1)
                        <li class="nav-item">
                            <a href="{{ route('quanlytaikhoan.index') }}" class="nav-link {{ request()->is('QuanLyTaiKhoan*') ? 'active' : '' }}">
                                <i class="fa-solid fa-users-gear"></i> Quản Lý Tài Khoản
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('quanlykhuyenmai.index') }}" class="nav-link {{ request()->is('QuanLyKhuyenMai*') ? 'active' : '' }}">
                                <i class="fa-solid fa-tags"></i> Quản Lý Khuyến Mãi
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('quanlythongbao.thongbao') }}" class="nav-link {{ request()->is('QuanLyThongBao*') ? 'active' : '' }}">
                            <i class="fa-solid fa-triangle-exclamation text-warning"></i> Cảnh Báo Đánh Giá
                        </a>
                    </li>
                    <li class="nav-item mt-4 border-top border-secondary pt-3">
                        <a href="{{ route('home') }}" class="nav-link">
                            <i class="fa-solid fa-globe"></i> Xem Trang Khách Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('taikhoan.dangxuat') }}" class="nav-link text-danger">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Content -->
            <div class="col-md-9 col-lg-10">
                <header class="admin-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">@yield('page-title', 'Bảng Điều Khiển')</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-muted"><i class="fa-solid fa-user-shield"></i> {{ session('HoTen', Auth::user()->TenDangNhap ?? 'Admin') }}</span>
                    </div>
                </header>

                <div class="content-wrapper">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
