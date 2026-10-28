<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Phân quyền theo vai trò: role:admin | role:admin,nhanvien | role:khachhang
            'role' => \App\Http\Middleware\CheckRole::class,
            'check.login' => \App\Http\Middleware\CheckLogin::class,
        ]);

        // IPN của MoMo gọi server-to-server nên không có CSRF token (đã xác thực bằng chữ ký HMAC)
        $middleware->validateCsrfTokens(except: ['DonHang/SavePayment']);

        // Middleware 'auth' mặc định của Laravel chuyển khách về trang đăng nhập của dự án
        $middleware->redirectGuestsTo(fn () => route('taikhoan.dangnhap'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
