<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ChiTietSanPhamRequest;
use App\Http\Requests\Admin\SanPhamRequest;
use App\Models\ChiTietDonHang;
use App\Models\ChiTietSanPham;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\Hang;
use App\Models\KhuyenMai;
use App\Models\LoaiSanPham;
use App\Models\MauSac;
use App\Models\NhaCungCap;
use App\Models\PhanHoi;
use App\Models\SanPham;
use App\Models\Size;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuanLySanPhamController extends Controller
{
    /** Ảnh mặc định khi sản phẩm chưa có hình */
    private const ANH_MAC_DINH = 'default-product.jpg';

    /** Thư mục con (trong disk "images") chứa ảnh upload */
    private const THU_MUC_UPLOAD = 'uploads';

    /**
     * Danh sách sản phẩm: tìm kiếm theo từ khóa + lọc theo loại/hãng + phân trang (giữ query string).
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $maLoai = $request->query('maLoai');
        $maHang = $request->query('maHang');

        $sanPhams = SanPham::with(['loaiSanPham', 'hang'])
            ->withSum('chiTietSanPhams as tong_ton_kho', 'SoLuongTon')
            ->timKiem($keyword)
            ->when($maLoai, fn ($q) => $q->where('MaLoai', $maLoai))
            ->when($maHang, fn ($q) => $q->where('MaHang', $maHang))
            ->orderByDesc('NgayTao')
            ->orderByDesc('MaSanPham')
            ->paginate(10)
            ->withQueryString();

        $loaiSanPhams = LoaiSanPham::orderBy('TenLoai')->get();
        $hangs = Hang::orderBy('TenHang')->get();

        return view('QuanLySanPham.Index', compact('sanPhams', 'keyword', 'maLoai', 'maHang', 'loaiSanPhams', 'hangs'));
    }

    public function details($id)
    {
        $sanPham = SanPham::with([
            'hang',
            'loaiSanPham',
            'nhaCungCap',
            'khuyenMai',
            'chiTietSanPhams.mauSac',
            'chiTietSanPhams.size',
        ])->findOrFail($id);

        $mauSacs = MauSac::orderBy('TenMau')->get();
        $sizes = Size::orderBy('LoaiSize')->orderBy('MaSize')->get();

        return view('QuanLySanPham.Details', compact('sanPham', 'mauSacs', 'sizes'));
    }

    public function create()
    {
        return view('QuanLySanPham.Create', $this->duLieuForm());
    }

    public function createPost(SanPhamRequest $request)
    {
        $data = $this->duLieuSanPham($request);
        $data['HinhAnhDaiDien'] = $request->hasFile('HinhAnhUpload')
            ? $this->luuAnh($request->file('HinhAnhUpload'))
            : self::ANH_MAC_DINH;
        $data['NgayTao'] = now();

        $sanPham = SanPham::create($data);

        return redirect()->route('quanlysanpham.details', ['id' => $sanPham->MaSanPham])
            ->with('message', "Thêm sản phẩm \"{$sanPham->TenSanPham}\" thành công! Hãy thêm biến thể (màu/size, tồn kho) để khách có thể đặt mua.");
    }

    public function edit($id)
    {
        $sanPham = SanPham::findOrFail($id);

        return view('QuanLySanPham.Edit', array_merge(['sanPham' => $sanPham], $this->duLieuForm()));
    }

    public function editPost(SanPhamRequest $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);
        $data = $this->duLieuSanPham($request);

        if ($request->hasFile('HinhAnhUpload')) {
            $anhCu = $sanPham->HinhAnhDaiDien;
            $data['HinhAnhDaiDien'] = $this->luuAnh($request->file('HinhAnhUpload'));
            $this->xoaAnhUpload($anhCu);
        }

        $sanPham->update($data);

        return redirect()->route('quanlysanpham.index')->with('message', 'Cập nhật sản phẩm thành công!');
    }

    public function delete($id)
    {
        $sanPham = SanPham::with(['loaiSanPham', 'hang', 'nhaCungCap'])->findOrFail($id);

        return view('QuanLySanPham.Delete', compact('sanPham'));
    }

    /**
     * Xóa mềm (Soft Delete): sản phẩm ẩn khỏi cửa hàng nhưng đơn hàng cũ vẫn giữ nguyên thông tin.
     */
    public function deleteConfirmed($id)
    {
        $sanPham = SanPham::findOrFail($id);

        DB::transaction(function () use ($sanPham) {
            // Bỏ sản phẩm khỏi giỏ hàng của khách
            GioHang::whereIn('MaChiTietSanPham', $sanPham->chiTietSanPhams()->pluck('MaChiTiet'))->delete();
            $sanPham->delete();
        });

        return redirect()->route('quanlysanpham.index')
            ->with('message', "Đã xóa sản phẩm \"{$sanPham->TenSanPham}\" thành công!");
    }

    /* ==================== BIẾN THỂ SẢN PHẨM (ChiTietSanPham) ==================== */

    public function bienTheStore(ChiTietSanPhamRequest $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);

        $data = $request->safe()->only(['MaMau', 'MaSize', 'GiaBan', 'SoLuongTon', 'SKU']);
        if ($request->hasFile('HinhAnhUpload')) {
            $data['HinhAnh'] = $this->luuAnh($request->file('HinhAnhUpload'));
        }

        $sanPham->chiTietSanPhams()->create($data);

        return redirect()->route('quanlysanpham.details', ['id' => $sanPham->MaSanPham])
            ->with('message', "Đã thêm biến thể SKU \"{$data['SKU']}\".");
    }

    public function bienTheEdit($maChiTiet)
    {
        $bienThe = ChiTietSanPham::with('sanPham')->findOrFail($maChiTiet);
        $mauSacs = MauSac::orderBy('TenMau')->get();
        $sizes = Size::orderBy('LoaiSize')->orderBy('MaSize')->get();

        return view('QuanLySanPham.EditBienThe', compact('bienThe', 'mauSacs', 'sizes'));
    }

    public function bienTheUpdate(ChiTietSanPhamRequest $request, $maChiTiet)
    {
        $bienThe = ChiTietSanPham::findOrFail($maChiTiet);

        $data = $request->safe()->only(['MaMau', 'MaSize', 'GiaBan', 'SoLuongTon', 'SKU']);
        if ($request->hasFile('HinhAnhUpload')) {
            $anhCu = $bienThe->HinhAnh;
            $data['HinhAnh'] = $this->luuAnh($request->file('HinhAnhUpload'));
            $this->xoaAnhUpload($anhCu);
        }

        $bienThe->update($data);

        return redirect()->route('quanlysanpham.details', ['id' => $bienThe->MaSanPham])
            ->with('message', "Cập nhật biến thể SKU \"{$bienThe->SKU}\" thành công!");
    }

    public function bienTheDestroy($maChiTiet)
    {
        $bienThe = ChiTietSanPham::findOrFail($maChiTiet);

        // Biến thể đã có trong đơn hàng thì không xóa để giữ lịch sử, chỉ cho phép đặt tồn kho = 0
        if (ChiTietDonHang::where('MaChiTietSanPham', $bienThe->MaChiTiet)->exists()) {
            return redirect()->route('quanlysanpham.details', ['id' => $bienThe->MaSanPham])
                ->with('error', "Biến thể SKU \"{$bienThe->SKU}\" đã phát sinh đơn hàng nên không thể xóa. Hãy sửa tồn kho về 0 để ngừng bán.");
        }

        $maSanPham = $bienThe->MaSanPham;
        $sku = $bienThe->SKU;
        $anh = $bienThe->HinhAnh;
        $bienThe->delete();
        $this->xoaAnhUpload($anh);

        return redirect()->route('quanlysanpham.details', ['id' => $maSanPham])
            ->with('message', "Đã xóa biến thể SKU \"{$sku}\".");
    }

    /* ==================== DASHBOARD THỐNG KÊ ==================== */

    public function dashboard(Request $request)
    {
        if (! Auth::check()) {
            $returnUrl = $request->query('returnUrl');

            return view('TaiKhoan.AdminDangNhap', compact('returnUrl'));
        }

        $user = Auth::user();
        if (! $user->hasAdminAccess()) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập vào trang quản trị.');
        }

        $namHienTai = (int) $request->query('nam', date('Y'));
        if ($namHienTai < 2000 || $namHienTai > 2100) {
            $namHienTai = (int) date('Y');
        }

        $trangThaiLoaiTru = ['Đã hủy', 'Đã hủy (Thanh toán thất bại)'];

        $thongKe = function (Carbon $tu, Carbon $den) use ($trangThaiLoaiTru) {
            return DonHang::whereBetween('NgayDat', [$tu, $den])
                ->whereNotIn('TrangThai', $trangThaiLoaiTru)
                ->selectRaw('COUNT(*) as SoLuongDon, COALESCE(SUM(TongTienSauGiam), 0) as TongDoanhThu')
                ->first();
        };

        // 1. Doanh thu theo năm
        $dauNam = Carbon::create($namHienTai, 1, 1)->startOfDay();
        $doanhThuNamData = $thongKe($dauNam, $dauNam->copy()->endOfYear());
        $tongDoanhThuNam = (float) ($doanhThuNamData->TongDoanhThu ?? 0);
        $soLuongDonNam = (int) ($doanhThuNamData->SoLuongDon ?? 0);

        // 2. Doanh thu theo quý
        $doanhThuQuy = [];
        for ($q = 1; $q <= 4; $q++) {
            $tu = Carbon::create($namHienTai, ($q - 1) * 3 + 1, 1)->startOfDay();
            $res = $thongKe($tu, $tu->copy()->addMonths(2)->endOfMonth());
            $doanhThuQuy[] = (object) [
                'Quy' => $q,
                'Nam' => $namHienTai,
                'SoLuongDon' => (int) ($res->SoLuongDon ?? 0),
                'TongDoanhThu' => (float) ($res->TongDoanhThu ?? 0),
            ];
        }

        // 3. Doanh thu theo tháng
        $doanhThuThang = [];
        for ($m = 1; $m <= 12; $m++) {
            $tu = Carbon::create($namHienTai, $m, 1)->startOfDay();
            $res = $thongKe($tu, $tu->copy()->endOfMonth());
            $doanhThuThang[] = (object) [
                'Thang' => $m,
                'Nam' => $namHienTai,
                'SoLuongDon' => (int) ($res->SoLuongDon ?? 0),
                'TongDoanhThu' => (float) ($res->TongDoanhThu ?? 0),
            ];
        }

        // 4. Sản phẩm bán chạy (Top 10) trong năm, không tính đơn đã hủy
        $sanPhamBanChay = ChiTietDonHang::join('DonHang', 'ChiTietDonHang.MaDonHang', '=', 'DonHang.MaDonHang')
            ->join('ChiTietSanPham', 'ChiTietDonHang.MaChiTietSanPham', '=', 'ChiTietSanPham.MaChiTiet')
            ->join('SanPham', 'ChiTietSanPham.MaSanPham', '=', 'SanPham.MaSanPham')
            ->whereBetween('DonHang.NgayDat', [$dauNam, $dauNam->copy()->endOfYear()])
            ->whereNotIn('DonHang.TrangThai', $trangThaiLoaiTru)
            ->select(
                'SanPham.MaSanPham',
                'SanPham.TenSanPham',
                'SanPham.HinhAnhDaiDien',
                DB::raw('SUM(ChiTietDonHang.SoLuong) as TongSoLuongBan'),
                DB::raw('SUM(ChiTietDonHang.ThanhTien) as TongDoanhThu')
            )
            ->groupBy('SanPham.MaSanPham', 'SanPham.TenSanPham', 'SanPham.HinhAnhDaiDien')
            ->orderByDesc('TongSoLuongBan')
            ->take(10)
            ->get();

        $tongSanPham = SanPham::count();
        $soCanhBao = PhanHoi::where('DanhGia', '<=', 2)->count();

        return view('QuanLySanPham.Dashboard', compact(
            'tongSanPham',
            'soCanhBao',
            'namHienTai',
            'tongDoanhThuNam',
            'soLuongDonNam',
            'doanhThuQuy',
            'doanhThuThang',
            'sanPhamBanChay'
        ));
    }

    /* ==================== HÀM HỖ TRỢ ==================== */

    private function duLieuForm(): array
    {
        return [
            'loaiSanPhams' => LoaiSanPham::orderBy('TenLoai')->get(),
            'nhaCungCaps' => NhaCungCap::orderBy('TenNhaCungCap')->get(),
            'hangs' => Hang::orderBy('TenHang')->get(),
            'khuyenMais' => KhuyenMai::orderByDesc('NgayBatDau')->get(),
        ];
    }

    private function duLieuSanPham(SanPhamRequest $request): array
    {
        $data = $request->safe()->only([
            'TenSanPham', 'MoTa', 'GiaGoc', 'MaLoai', 'MaNhaCungCap', 'MaHang', 'MaKhuyenMai',
        ]);
        $data['MaKhuyenMai'] = $data['MaKhuyenMai'] ?? null;
        $data['CoSize'] = $request->boolean('CoSize');
        $data['CoMau'] = $request->boolean('CoMau');

        return $data;
    }

    /**
     * Lưu ảnh qua Laravel Storage (disk "images" = public/images), trả về đường dẫn tương đối.
     */
    private function luuAnh(UploadedFile $file): string
    {
        return $file->store(self::THU_MUC_UPLOAD, 'images');
    }

    /**
     * Chỉ xóa ảnh do Admin upload (thư mục uploads/), không xóa ảnh mẫu của dự án.
     */
    private function xoaAnhUpload(?string $duongDan): void
    {
        if ($duongDan && str_starts_with($duongDan, self::THU_MUC_UPLOAD.'/')) {
            Storage::disk('images')->delete($duongDan);
        }
    }
}
