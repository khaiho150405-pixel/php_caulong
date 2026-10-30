<?php

namespace App\Http\Controllers;

use App\Models\Hang;
use App\Models\KhuyenMai;
use App\Models\LoaiSanPham;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $query = SanPham::with(['loaiSanPham', 'hang', 'khuyenMai']);

        $loai = $request->query('loai') ?: $request->query('maLoai');
        $hang = $request->query('hang');
        $ten = $request->query('ten');

        if (! empty($loai)) {
            if (is_numeric($loai)) {
                $query->where('MaLoai', (int) $loai);
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

        if (! empty($hang)) {
            $hangKey = trim($hang);
            $query->whereHas('hang', function ($q) use ($hangKey) {
                $q->where('TenHang', 'LIKE', "%{$hangKey}%");
            });
        }

        if (! empty($ten)) {
            $tenKey = trim($ten);
            $query->where('TenSanPham', 'LIKE', "%{$tenKey}%");
        }

        $this->apDungBoLoc($query, $request);

        $result = $query->paginate(12)->withQueryString();

        return view('SanPham.Index', [
            'sanPhams' => $result,
            'title' => 'Danh sách sản phẩm',
        ] + $this->duLieuBoLoc($request));
    }

    public function timKiem(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        if (empty($keyword)) {
            return redirect()->route('sanpham.index');
        }

        $query = SanPham::with(['loaiSanPham', 'hang', 'khuyenMai'])->timKiem($keyword);
        $this->apDungBoLoc($query, $request);
        $results = $query->paginate(12)->withQueryString();

        return view('SanPham.Index', [
            'sanPhams' => $results,
            'title' => "Kết quả tìm kiếm cho '{$keyword}'",
            'keyword' => $keyword,
        ] + $this->duLieuBoLoc($request));
    }

    private const KHOANG_GIA = [
        '0-1000000' => [0, 1000000],
        '1000000-2000000' => [1000000, 2000000],
        '2000000-3000000' => [2000000, 3000000],
        '3000000-5000000' => [3000000, 5000000],
        '5000000-' => [5000000, null],
    ];

    private function khoangGia(Request $request): array
    {
        $tu = $request->query('gia_tu');
        $den = $request->query('gia_den');
        $tu = is_numeric($tu) && $tu >= 0 ? (float) $tu : null;
        $den = is_numeric($den) && $den > 0 ? (float) $den : null;

        if ($tu === null && $den === null) {
            $preset = self::KHOANG_GIA[$request->query('khoang')] ?? null;
            if ($preset) {
                [$tu, $den] = [(float) $preset[0], $preset[1] !== null ? (float) $preset[1] : null];
            }
        }

        if ($tu !== null && $den !== null && $tu > $den) {
            [$tu, $den] = [$den, $tu];
        }

        return [$tu, $den];
    }

    private function apDungBoLoc($query, Request $request): void
    {
        [$tu, $den] = $this->khoangGia($request);
        $query->locTheoGia($tu, $den);

        $hangIds = array_filter(array_map('intval', (array) $request->query('hangs', [])));
        if ($hangIds) {
            $query->whereIn('MaHang', $hangIds);
        }

        if ($request->boolean('giam_gia')) {
            $query->dangGiamGia();
        }
        if ($request->boolean('con_hang')) {
            $query->conHang();
        }

        $query->sapXep($request->query('sap_xep'));
    }

    private function duLieuBoLoc(Request $request): array
    {
        [$tu, $den] = $this->khoangGia($request);
        $hangDangChon = array_map('intval', (array) $request->query('hangs', []));
        $hangTheoTen = trim((string) $request->query('hang', ''));

        $dsHang = Hang::orderBy('TenHang')->get();

        if (! $hangDangChon && $hangTheoTen !== '') {
            $hangDangChon = $dsHang->filter(fn ($h) => stripos($h->TenHang, $hangTheoTen) !== false)->pluck('MaHang')->all();
        }

        $dangLoc = $tu !== null || $den !== null || $hangDangChon
            || $request->boolean('giam_gia') || $request->boolean('con_hang');

        return [
            'dsHang' => $dsHang,
            'dsLoai' => LoaiSanPham::orderBy('MaLoai')->get(),
            'khoangGiaGoiY' => array_keys(self::KHOANG_GIA),
            'locGiaTu' => $tu,
            'locGiaDen' => $den,
            'locHang' => $hangDangChon,
            'locKhoang' => $request->query('khoang'),
            'sapXep' => $request->query('sap_xep', 'moi'),
            'dangLoc' => $dangLoc,
        ];
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
