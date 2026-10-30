<?php

namespace App\Http\Controllers;

use App\Models\Hang;
use App\Models\KhuyenMai;
use App\Models\LoaiSanPham;
use App\Models\PhanHoi;
use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;

class LandingController extends Controller
{
    private const NGAN_SACH = [
        '0-1000000' => ['Dưới 1 triệu', 0, 1000000],
        '1000000-2000000' => ['1 – 2 triệu', 1000000, 2000000],
        '2000000-3000000' => ['2 – 3 triệu', 2000000, 3000000],
        '3000000-5000000' => ['3 – 5 triệu', 3000000, 5000000],
        '5000000-' => ['Trên 5 triệu', 5000000, null],
    ];

    public function index()
    {
        $today = now()->toDateString();

        $thongKe = [
            'sanPham' => SanPham::count(),
            'hang' => Hang::count(),
            'uuDai' => KhuyenMai::where(fn ($q) => $q->whereNull('NgayBatDau')->orWhere('NgayBatDau', '<=', $today))
                ->where(fn ($q) => $q->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', $today))
                ->count(),
        ];

        $nganSach = collect(self::NGAN_SACH)->map(fn ($m, $khoa) => [
            'khoa' => $khoa,
            'nhan' => $m[0],
            'soLuong' => SanPham::locTheoGia($m[1], $m[2])->count(),
        ])->values();

        $danhMuc = LoaiSanPham::withCount('sanPhams')->orderBy('MaLoai')->get();

        $uuDai = SanPham::with(['hang', 'khuyenMai'])->dangGiamGia()->get()
            ->sortByDesc(fn ($sp) => $sp->GiaGoc > 0 ? 1 - ($sp->gia_sau_khuyen_mai / $sp->GiaGoc) : 0)
            ->take(8)
            ->values();
        if ($uuDai->count() < 4) {
            $them = SanPham::with(['hang', 'khuyenMai'])->whereNotIn('MaSanPham', $uuDai->pluck('MaSanPham'))
                ->orderByDesc('NgayTao')->take(8 - $uuDai->count())->get();
            $uuDai = $uuDai->concat($them)->values();
        }

        $heroSp = $uuDai->unique('MaLoai')->take(3);
        if ($heroSp->count() < 3) {
            $heroSp = $heroSp->concat($uuDai->whereNotIn('MaSanPham', $heroSp->pluck('MaSanPham')))->take(3);
        }
        $heroSp = $heroSp->values();
        $giamToiDa = (int) $uuDai->map(fn ($sp) => $sp->GiaGoc > 0 ? round((1 - $sp->gia_sau_khuyen_mai / $sp->GiaGoc) * 100) : 0)->max();

        $danhGia = PhanHoi::with(['khachHang', 'sanPham'])
            ->where('DanhGia', '>=', 4)
            ->whereNotNull('NoiDung')
            ->orderByDesc('NgayPhanHoi')
            ->take(3)
            ->get();

        $dsNoiBat = TinTuc::with(['sanPham', 'tranDau'])
            ->daDang()
            ->where('NoiBat', true)
            ->orderBy('ThuTu')
            ->orderByDesc('NgayDang')
            ->orderByDesc('MaTin')
            ->get();

        $tinMoiQuery = TinTuc::with(['sanPham', 'tranDau'])->daDang();
        if ($dsNoiBat->isNotEmpty()) {
            $tinMoiQuery->whereNotIn('MaTin', $dsNoiBat->pluck('MaTin'));
        }
        $tinMoi = $tinMoiQuery->orderByDesc('NgayDang')->orderByDesc('MaTin')->take(3)->get();
        $tranMoi = TranDau::with(['tinTucs' => fn ($q) => $q->daDang()->orderByDesc('NgayDang')])->orderByDesc('NgayThiDau')->orderByDesc('MaTranDau')->take(4)->get();

        return view('Landing.Index', compact('thongKe', 'nganSach', 'danhMuc', 'uuDai', 'danhGia', 'heroSp', 'giamToiDa', 'dsNoiBat', 'tinMoi', 'tranMoi'));
    }
}
