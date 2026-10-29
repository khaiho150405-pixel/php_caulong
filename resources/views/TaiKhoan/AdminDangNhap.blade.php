<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Đăng nhập Quản trị - Hệ Thống Quản Lý Cầu Lông</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body.admin-login-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0f172a 100%);
            margin: 0;
            padding: 24px 16px;
            font-family: var(--font, system-ui, -apple-system, sans-serif);
            color: #1e293b;
        }
        .admin-login-card {
            width: 100%;
            max-width: 410px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
            padding: 36px 32px;
        }
        .admin-login-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: #eef8f3;
            color: #148f5f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 16px;
        }
        .admin-login-title {
            font-size: 1.35rem;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .admin-login-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 24px;
        }
        .admin-login-card .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .admin-login-card .input-group-text {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
        }
        .admin-login-card .form-control {
            border-color: #cbd5e1;
            font-size: 0.92rem;
            padding: 10px 14px;
        }
        .admin-login-card .form-control:focus {
            border-color: #148f5f;
            box-shadow: 0 0 0 3px rgba(20, 143, 95, 0.15);
        }
        .btn-admin-submit {
            background: #148f5f;
            border-color: #148f5f;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 11px 16px;
            border-radius: 10px;
            width: 100%;
            transition: all 0.2s ease;
        }
        .btn-admin-submit:hover {
            background: #0f7550;
            border-color: #0f7550;
            color: #ffffff;
        }
        .admin-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
            margin-top: 20px;
        }
        .admin-back-link:hover {
            color: #e2e8f0;
        }
    </style>
</head>
<body class="admin-login-body">
    <div class="d-flex flex-column align-items-center w-100">
        <div class="admin-login-card">
            <div class="admin-login-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h1 class="admin-login-title">Đăng nhập Quản trị</h1>
            <p class="admin-login-subtitle">Hệ thống quản lý cửa hàng cầu lông</p>

            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('message'))
                <div class="alert alert-success py-2 px-3 small rounded-3 mb-3">
                    {{ session('message') }}
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                @if(!empty($returnUrl))
                    <input type="hidden" name="returnUrl" value="{{ $returnUrl }}">
                @endif

                <div class="mb-3">
                    <label for="TenDangNhap" class="form-label">Tên đăng nhập</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control" value="{{ old('TenDangNhap') }}" placeholder="Nhập tài khoản admin / nhân viên" required autofocus autocomplete="username" />
                    </div>
                </div>

                <div class="mb-4">
                    <label for="adminMatKhau" class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="adminMatKhau" name="MatKhau" class="form-control" placeholder="Nhập mật khẩu" required autocomplete="current-password" />
                        <button type="button" id="toggleAdminPassword" class="btn btn-outline-secondary" style="min-width: 46px;" aria-label="Hiện mật khẩu" aria-controls="adminMatKhau">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-admin-submit">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng nhập
                </button>
            </form>
        </div>

        <a href="{{ route('home') }}" class="admin-back-link">
            <i class="fa-solid fa-arrow-left"></i> Quay lại trang chủ cửa hàng
        </a>
    </div>

    <script>
        document.getElementById('toggleAdminPassword').addEventListener('click', function () {
            const password = document.getElementById('adminMatKhau');
            const showPassword = password.type === 'password';
            password.type = showPassword ? 'text' : 'password';
            this.setAttribute('aria-label', showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            this.querySelector('i').classList.toggle('fa-eye', !showPassword);
            this.querySelector('i').classList.toggle('fa-eye-slash', showPassword);
        });
    </script>
</body>
</html>
