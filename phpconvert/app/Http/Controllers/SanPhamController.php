<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\Hang;
use App\Models\LoaiSanPham;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $query = SanPham::with(['loaiSanPham', 'hang', 'khuyenMai']);

        $loai = $request->query('loai') ?: $request->query('maLoai');
        $hang = $request->query('hang');
        $ten = $request->query('ten');

        // Lọc theo loại sản phẩm
        if (!empty($loai)) {
            if (is_numeric($loai)) {
                $query->where('MaLoai', (int)$loai);
            } else {
                $key = mb_strtolower(trim($loai), 'UTF-8');
                if ($key == 'vot' || $key == 'votc' || str_contains($key, 'vot')) {
                    $query->whereHas('loaiSanPham', function ($q) {
                        $q->where('TenLoai', 'LIKE', '%Vợt%')->orWhere('TenLoai', 'LIKE', '%vot%');
                    });
                } elseif (str_contains($key, 'giay') || str_contains($key, 'giày')) {
                    $query->whereHas('loaiSanPham', function ($q) {
                        $q->where('TenLoai', 'LIKE', '%Giày%')->orWhere('TenLoai', 'LIKE', '%giay%');
                    });
                } elseif (str_contains($key, 'ao') || str_contains($key, 'áo') || str_contains($key, 'quan') || str_contains($key, 'quần')) {
                    $query->whereHas('loaiSanPham', function ($q) {
                        $q->where('TenLoai', 'LIKE', '%Áo%')->orWhere('TenLoai', 'LIKE', '%ao%');
                    });
                } elseif (str_contains($key, 'phu') || str_contains($key, 'phụ')) {
                    $query->whereHas('loaiSanPham', function ($q) {
                        $q->where('TenLoai', 'LIKE', '%Phụ kiện%')->orWhere('TenLoai', 'LIKE', '%phu%');
                    });
                } else {
                    $query->whereHas('loaiSanPham', function ($q) use ($key) {
                        $q->where('TenLoai', 'LIKE', "%{$key}%");
                    });
                }
            }
        }

        // Lọc theo Hãng
        if (!empty($hang)) {
            $hangKey = trim($hang);
            $query->whereHas('hang', function ($q) use ($hangKey) {
                $q->where('TenHang', 'LIKE', "%{$hangKey}%");
            });
        }

        // Lọc theo Tên
        if (!empty($ten)) {
            $tenKey = mb_strtolower(trim($ten), 'UTF-8');
            $query->where('TenSanPham', 'LIKE', "%{$tenKey}%");
        }

        $result = $query->orderByDesc('NgayTao')->get();

        return view('SanPham.Index', [
            'sanPhams' => $result,
            'title' => 'Danh sách sản phẩm',
        ]);
    }

    public function timKiem(Request $request)
    {
        $keyword = trim((string)$request->query('keyword', ''));
        if (empty($keyword)) {
            return redirect()->route('sanpham.index');
        }

        $key = mb_strtolower($keyword, 'UTF-8');

        $results = SanPham::with(['loaiSanPham', 'hang', 'khuyenMai'])
            ->where(function ($q) use ($key) {
                $q->where('TenSanPham', 'LIKE', "%{$key}%")
                  ->orWhereHas('hang', function ($hq) use ($key) {
                      $hq->where('TenHang', 'LIKE', "%{$key}%");
                  })
                  ->orWhereHas('loaiSanPham', function ($lq) use ($key) {
                      $lq->where('TenLoai', 'LIKE', "%{$key}%");
                  });
            })
            ->orderByDesc('NgayTao')
            ->get();

        return view('SanPham.Index', [
            'sanPhams' => $results,
            'title' => "Kết quả tìm kiếm cho '{$keyword}'",
            'keyword' => $keyword,
        ]);
    }

    public function khuyenMai()
    {
        $today = now()->toDateString();

        $promotions = KhuyenMai::where(function ($q) use ($today) {
            $q->whereNull('NgayBatDau')->orWhere('NgayBatDau', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', $today);
        })->get();

        $activePromoIds = $promotions->pluck('MaKhuyenMai');

        $products = SanPham::with(['khuyenMai', 'hang', 'loaiSanPham'])
            ->whereIn('MaKhuyenMai', $activePromoIds)
            ->get();

        $promoProducts = $products->groupBy('MaKhuyenMai');

        return view('KhuyenMai.Index', compact('promotions', 'promoProducts', 'products'));
    }
}
