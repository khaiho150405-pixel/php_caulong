<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản Trị Hệ Thống - Shop Cầu Lông')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap&subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('styles')
</head>
<body class="admin-body">
    @php
        $adminUser = Auth::user();
        $adminName = session('HoTen', $adminUser->TenDangNhap ?? 'Admin');
        $adminRole = $adminUser?->isAdmin() ? 'Quản trị viên' : 'Nhân viên';
        $adminTitle = match (true) {
            request()->routeIs('quanlysanpham.dashboard') => 'Thống kê & báo cáo',
            request()->is('admin/san-pham*', 'admin/bien-the*') => 'Quản lý sản phẩm',
            request()->is('admin/don-hang*') => 'Quản lý đơn hàng',
            request()->is('admin/khuyen-mai*') => 'Quản lý khuyến mãi',
            request()->is('admin/ban-tin*') => 'Quản lý bảng tin',
            request()->is('admin/tran-dau*') => 'Kết quả trận đấu',
            request()->is('admin/tai-khoan*') => 'Quản lý tài khoản',
            request()->is('admin/thong-bao*') => 'Cảnh báo đánh giá',
            request()->routeIs('taikhoan.doimatkhau') => 'Đổi mật khẩu',
            default => 'Bảng điều khiển',
        };
    @endphp

    <aside class="admin-sidebar" id="adminSidebar">
        <a href="{{ route('quanlysanpham.dashboard') }}" class="admin-brand">
            <img src="{{ asset('images/HUIT.jpg') }}" alt="Logo">
            <span>
                <span class="brand-name d-block">Shop Cầu Lông</span>
                <span class="brand-sub d-block">Trang quản trị</span>
            </span>
        </a>

        <nav class="admin-nav">
            <div class="admin-nav-label">Tổng quan</div>
            <a href="{{ route('quanlysanpham.dashboard') }}" class="admin-link {{ request()->routeIs('quanlysanpham.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Thống kê
            </a>

            <div class="admin-nav-label">Bán hàng</div>
            <a href="{{ route('quanlysanpham.index') }}" class="admin-link {{ request()->is('admin/san-pham*', 'admin/bien-the*') ? 'active' : '' }}">
                <i class="fa-solid fa-box-archive"></i> Sản phẩm
            </a>
            <a href="{{ route('quanlydonhang.index') }}" class="admin-link {{ request()->is('admin/don-hang*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar"></i> Đơn hàng
            </a>
            @if (Auth::user()?->isAdmin())
                <a href="{{ route('quanlykhuyenmai.index') }}" class="admin-link {{ request()->is('admin/khuyen-mai*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i> Khuyến mãi
                </a>
            @endif

            <div class="admin-nav-label">Nội dung</div>
            <a href="{{ route('quanlybantin.index') }}" class="admin-link {{ request()->is('admin/ban-tin*') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i> Bảng tin
            </a>
            <a href="{{ route('quanlytrandau.index') }}" class="admin-link {{ request()->is('admin/tran-dau*') ? 'active' : '' }}">
                <i class="fa-solid fa-table-tennis-paddle-ball"></i> Kết quả trận đấu
            </a>

            <div class="admin-nav-label">Hệ thống</div>
            @if (Auth::user()?->isAdmin())
                <a href="{{ route('quanlytaikhoan.index') }}" class="admin-link {{ request()->is('admin/tai-khoan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i> Tài khoản
                </a>
            @endif
            <a href="{{ route('quanlythongbao.thongbao') }}" class="admin-link {{ request()->is('admin/thong-bao*') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo đánh giá
            </a>
        </nav>

        <div class="admin-sidebar-foot">
            @if ($adminUser?->isAdmin())
                <a href="{{ route('taikhoan.doimatkhau') }}" class="admin-link {{ request()->routeIs('taikhoan.doimatkhau') ? 'active' : '' }}">
                    <i class="fa-solid fa-key"></i> Đổi mật khẩu
                </a>
            @else
                <a href="{{ route('home') }}" class="admin-link">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem trang khách hàng
                </a>
            @endif
            <form action="{{ route('taikhoan.dangxuat') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="admin-link danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>
    <div class="admin-backdrop" id="adminBackdrop"></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="admin-toggle" id="adminToggle" aria-label="Mở menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1>@yield('page-title', $adminTitle)</h1>
            </div>
            <div class="admin-user">
                <div class="meta text-end">
                    <div class="name">{{ $adminName }}</div>
                    <div class="role">{{ $adminRole }}</div>
                </div>
                <div class="admin-avatar">{{ mb_strtoupper(mb_substr($adminName, 0, 1)) }}</div>
            </div>
        </header>

        <main class="admin-content">
            @include('partials._flash')

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        (function () {
            var sidebar = document.getElementById('adminSidebar');
            var backdrop = document.getElementById('adminBackdrop');
            function toggle(open) {
                sidebar.classList.toggle('open', open);
                backdrop.classList.toggle('show', open);
            }
            document.getElementById('adminToggle').addEventListener('click', function () {
                toggle(!sidebar.classList.contains('open'));
            });
            backdrop.addEventListener('click', function () { toggle(false); });
        })();
    </script>
    @yield('scripts')
</body>
</html>
