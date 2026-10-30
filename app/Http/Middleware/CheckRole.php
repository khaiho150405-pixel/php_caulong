<?php

namespace App\Http\Middleware;

use App\Models\TaiKhoan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            if ($request->is('admin', 'admin/*')) {
                return redirect()->route('quanlysanpham.dashboard', ['returnUrl' => $request->fullUrl()])
                    ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
            }

            return redirect()->route('taikhoan.dangnhap', ['returnUrl' => $request->fullUrl()])
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();

        $allowedIds = array_map(fn (string $role) => TaiKhoan::roleId($role), $roles);

        if (in_array((int) $user->MaQuyen, $allowedIds, true)) {
            return $next($request);
        }

        if ($user->hasAdminAccess()) {
            return redirect()->route('quanlysanpham.dashboard')
                ->with('error', 'Bạn không có quyền truy cập chức năng này (chỉ dành cho Quản trị viên).');
        }

        return redirect()->route('home')
            ->with('error', 'Bạn không có quyền truy cập vào trang quản trị.');
    }
}
