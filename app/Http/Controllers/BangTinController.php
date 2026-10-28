<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Bảng tin cầu lông (phía khách hàng): tin sản phẩm hot/mới, kết quả trận đấu, tuyển thủ, giải đấu.
 */
class BangTinController extends Controller
{
    public function index(Request $request)
    {
        $loai = array_key_exists((string) $request->query('loai'), TinTuc::LOAI) ? $request->query('loai') : null;
        $keyword = trim((string) $request->query('keyword', ''));

        $query = TinTuc::with(['sanPham', 'tranDau'])->daDang()->loai($loai)->timKiem($keyword);

        // Tin nổi bật chỉ hiện ở trang đầu khi chưa lọc/tìm kiếm
        $noiBat = null;
        if (!$loai && $keyword === '' && (int) $request->query('page', 1) === 1) {
            $noiBat = TinTuc::with(['sanPham', 'tranDau'])->daDang()->where('NoiBat', true)->orderByDesc('NgayDang')->first();
            if ($noiBat) {
                $query->where('MaTin', '!=', $noiBat->MaTin);
            }
        }

        $tins = $query->orderByDesc('NgayDang')->orderByDesc('MaTin')->paginate(8)->withQueryString();

        $demLoai = TinTuc::daDang()->selectRaw('LoaiTin, COUNT(*) AS so')->groupBy('LoaiTin')->pluck('so', 'LoaiTin');
        $tong = [
            'tin' => (int) $demLoai->sum(),
            'tran' => TranDau::count(),
            'luotXem' => number_format((int) TinTuc::daDang()->sum('LuotXem'), 0, ',', '.'),
        ];

        return view('BangTin.Index', [
            'tins' => $tins,
            'noiBat' => $noiBat,
            'loai' => $loai,
            'keyword' => $keyword,
            'demLoai' => $demLoai,
            'tong' => $tong,
            'tickerTran' => $this->tranDauMoi(8),
        ] + $this->cotPhu());
    }

    public function show($id)
    {
        $tin = TinTuc::with(['sanPham.hang', 'sanPham.khuyenMai', 'tranDau', 'tacGia'])->daDang()->findOrFail($id);

        // Tăng lượt xem (không cập nhật nếu người dùng vừa xem bài này trong phiên)
        $key = 'da_xem_tin_' . $tin->MaTin;
        if (!session()->has($key)) {
            TinTuc::whereKey($tin->MaTin)->increment('LuotXem');
            $tin->LuotXem++;
            session()->put($key, true);
        }

        $lienQuan = TinTuc::with(['sanPham', 'tranDau'])->daDang()
            ->where('LoaiTin', $tin->LoaiTin)->where('MaTin', '!=', $tin->MaTin)
            ->orderByDesc('NgayDang')->take(3)->get();

        return view('BangTin.Show', compact('tin', 'lienQuan') + $this->cotPhu());
    }

    /** Dữ liệu cột bên: kết quả trận mới, sản phẩm bán chạy, sản phẩm mới. */
    private function cotPhu(): array
    {
        return [
            'tranDauMoi' => $this->tranDauMoi(5),
            'spHot' => $this->sanPhamHot(4),
            'spMoi' => SanPham::with(['hang', 'khuyenMai'])->orderByDesc('NgayTao')->orderByDesc('MaSanPham')->take(4)->get(),
        ];
    }

    /** Trận đấu mới nhất, kèm bài viết liên quan (để bảng điểm dẫn sang bài). */
    private function tranDauMoi(int $soLuong)
    {
        return TranDau::with(['tinTucs' => fn ($q) => $q->daDang()->orderByDesc('NgayDang')])
            ->orderByDesc('NgayThiDau')->orderByDesc('MaTranDau')->take($soLuong)->get();
    }

    /**
     * Sản phẩm hot = bán chạy nhất theo số lượng trong các đơn hàng không bị hủy;
     * thiếu dữ liệu bán hàng thì bổ sung bằng sản phẩm đang giảm giá.
     */
    private function sanPhamHot(int $soLuong)
    {
        $ids = DB::table('ChiTietDonHang')
            ->join('DonHang', 'ChiTietDonHang.MaDonHang', '=', 'DonHang.MaDonHang')
            ->join('ChiTietSanPham', 'ChiTietDonHang.MaChiTietSanPham', '=', 'ChiTietSanPham.MaChiTiet')
            ->whereNotIn('DonHang.TrangThai', ['Đã hủy', 'Đã hủy (Thanh toán thất bại)'])
            ->groupBy('ChiTietSanPham.MaSanPham')
            ->orderByRaw('SUM(ChiTietDonHang.SoLuong) DESC')
            ->limit($soLuong)
            ->pluck('ChiTietSanPham.MaSanPham');

        $hot = SanPham::with(['hang', 'khuyenMai'])->whereIn('MaSanPham', $ids)->get()
            ->sortBy(fn ($sp) => $ids->search($sp->MaSanPham))->values();

        if ($hot->count() < $soLuong) {
            $them = SanPham::with(['hang', 'khuyenMai'])->dangGiamGia()
                ->whereNotIn('MaSanPham', $hot->pluck('MaSanPham'))
                ->take($soLuong - $hot->count())->get();
            $hot = $hot->concat($them)->values();
        }

        return $hot;
    }
}
