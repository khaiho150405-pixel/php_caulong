<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectAdminToDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin()
            && ! $request->is('admin', 'admin/*')
            && ! $request->routeIs('taikhoan.dangxuat', 'taikhoan.doimatkhau', 'taikhoan.doimatkhau.post')) {
            return redirect()->route('quanlysanpham.dashboard');
        }

        return $next($request);
    }
}
