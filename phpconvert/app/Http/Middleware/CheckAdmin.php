<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('taikhoan.dangnhap', ['returnUrl' => $request->fullUrl()]);
        }

        $user = Auth::user();

        // Chỉ cho phép MaQuyen = 1 (Quản trị) hoặc 2 (Nhân viên)
        if ($user && in_array((int)$user->MaQuyen, [1, 2])) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang quản trị.');
    }
}
