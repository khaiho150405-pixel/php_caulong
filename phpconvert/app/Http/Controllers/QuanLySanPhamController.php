<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use App\Models\ChiTietSanPham;
use App\Models\LoaiSanPham;
use App\Models\NhaCungCap;
use App\Models\Hang;
use App\Models\KhuyenMai;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class QuanLySanPhamController extends Controller
{
    public function index()
    {
        $sanPhams = SanPham::with(['loaiSanPham', 'hang'])
            ->orderByDesc('NgayTao')
            ->get();

        return view('QuanLySanPham.Index', compact('sanPhams'));
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
        ])->where('MaSanPham', $id)->firstOrFail();

        return view('QuanLySanPham.Details', compact('sanPham'));
    }

    public function create()
    {
        $loaiSanPhams = LoaiSanPham::all();
        $nhaCungCaps = NhaCungCap::all();
        $hangs = Hang::all();
        $khuyenMais = KhuyenMai::all();

        return view('QuanLySanPham.Create', compact('loaiSanPhams', 'nhaCungCaps', 'hangs', 'khuyenMais'));
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'TenSanPham' => 'required|string|max:100',
            'GiaGoc' => 'required|numeric|min:0',
            'HinhAnhUpload' => 'nullable|image|max:5120',
        ]);

        $fileName = 'default-product.jpg';
        if ($request->hasFile('HinhAnhUpload')) {
            $file = $request->file('HinhAnhUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
        }

        $sanPham = SanPham::create([
            'TenSanPham' => $request->input('TenSanPham'),
            'MoTa' => $request->input('MoTa'),
            'GiaGoc' => $request->input('GiaGoc'),
            'HinhAnhDaiDien' => $fileName,
            'MaLoai' => $request->input('MaLoai'),
            'MaNhaCungCap' => $request->input('MaNhaCungCap'),
            'MaHang' => $request->input('MaHang'),
            'MaKhuyenMai' => $request->input('MaKhuyenMai') ?: null,
            'CoSize' => $request->has('CoSize') ? 1 : 0,
            'CoMau' => $request->has('CoMau') ? 1 : 0,
            'NgayTao' => now(),
        ]);

        return redirect()->route('quanlysanpham.index')->with('message', 'Thêm sản phẩm thành công!');
    }

    public function edit($id)
    {
        $sanPham = SanPham::findOrFail($id);
        $loaiSanPhams = LoaiSanPham::all();
        $nhaCungCaps = NhaCungCap::all();
        $hangs = Hang::all();
        $khuyenMais = KhuyenMai::all();

        return view('QuanLySanPham.Edit', compact('sanPham', 'loaiSanPhams', 'nhaCungCaps', 'hangs', 'khuyenMais'));
    }

    public function editPost(Request $request, $id)
    {
        $sanPham = SanPham::findOrFail($id);

        $request->validate([
            'TenSanPham' => 'required|string|max:100',
            'GiaGoc' => 'required|numeric|min:0',
            'HinhAnhUpload' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('HinhAnhUpload')) {
            $file = $request->file('HinhAnhUpload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);

            // Xóa ảnh cũ nếu có
            if ($sanPham->HinhAnhDaiDien && $sanPham->HinhAnhDaiDien !== 'default-product.jpg') {
                $oldPath = public_path('images/' . $sanPham->HinhAnhDaiDien);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            $sanPham->HinhAnhDaiDien = $fileName;
        }

        $sanPham->TenSanPham = $request->input('TenSanPham');
        $sanPham->MoTa = $request->input('MoTa');
        $sanPham->GiaGoc = $request->input('GiaGoc');
        $sanPham->MaLoai = $request->input('MaLoai');
        $sanPham->MaNhaCungCap = $request->input('MaNhaCungCap');
        $sanPham->MaHang = $request->input('MaHang');
        $sanPham->MaKhuyenMai = $request->input('MaKhuyenMai') ?: null;
        $sanPham->CoSize = $request->has('CoSize') ? 1 : 0;
        $sanPham->CoMau = $request->has('CoMau') ? 1 : 0;
        $sanPham->save();

        return redirect()->route('quanlysanpham.index')->with('message', 'Cập nhật sản phẩm thành công!');
    }

    public function delete($id)
    {
        $sanPham = SanPham::with(['loaiSanPham', 'hang', 'nhaCungCap'])->findOrFail($id);
        return view('QuanLySanPham.Delete', compact('sanPham'));
    }

    public function deleteConfirmed($id)
    {
        $sanPham = SanPham::findOrFail($id);

        if ($sanPham->HinhAnhDaiDien && $sanPham->HinhAnhDaiDien !== 'default-product.jpg') {
            $path = public_path('images/' . $sanPham->HinhAnhDaiDien);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $sanPham->delete();
        return redirect()->route('quanlysanpham.index')->with('message', 'Đã xóa sản phẩm thành công!');
    }

    public function dashboard(Request $request)
    {
        $namHienTai = (int)$request->query('nam', date('Y'));

        // 1. Doanh thu theo năm
        $doanhThuNamData = DonHang::whereYear('NgayDat', $namHienTai)
            ->whereNotIn('TrangThai', ['Đã hủy', 'Đã hủy (Thanh toán thất bại)'])
            ->selectRaw('COUNT(*) as SoLuongDon, COALESCE(SUM(TongTienSauGiam), 0) as TongDoanhThu')
            ->first();

        $tongDoanhThuNam = (float)($doanhThuNamData->TongDoanhThu ?? 0);
        $soLuongDonNam = (int)($doanhThuNamData->SoLuongDon ?? 0);

        // 2. Doanh thu theo quý
        $doanhThuQuy = [];
        for ($q = 1; $q <= 4; $q++) {
            $quarterMonths = match ($q) {
                1 => [1, 2, 3],
                2 => [4, 5, 6],
                3 => [7, 8, 9],
                4 => [10, 11, 12],
            };
            $res = DonHang::whereYear('NgayDat', $namHienTai)
                ->whereIn(DB::raw('MONTH(NgayDat)'), $quarterMonths)
                ->whereNotIn('TrangThai', ['Đã hủy', 'Đã hủy (Thanh toán thất bại)'])
                ->selectRaw('COUNT(*) as SoLuongDon, COALESCE(SUM(TongTienSauGiam), 0) as TongDoanhThu')
                ->first();

            $doanhThuQuy[] = (object)[
                'Quy' => $q,
                'Nam' => $namHienTai,
                'SoLuongDon' => (int)($res->SoLuongDon ?? 0),
                'TongDoanhThu' => (float)($res->TongDoanhThu ?? 0),
            ];
        }

        // 3. Doanh thu theo tháng
        $doanhThuThang = [];
        for ($m = 1; $m <= 12; $m++) {
            $res = DonHang::whereYear('NgayDat', $namHienTai)
                ->whereMonth('NgayDat', $m)
                ->whereNotIn('TrangThai', ['Đã hủy', 'Đã hủy (Thanh toán thất bại)'])
                ->selectRaw('COUNT(*) as SoLuongDon, COALESCE(SUM(TongTienSauGiam), 0) as TongDoanhThu')
                ->first();

            $doanhThuThang[] = (object)[
                'Thang' => $m,
                'Nam' => $namHienTai,
                'SoLuongDon' => (int)($res->SoLuongDon ?? 0),
                'TongDoanhThu' => (float)($res->TongDoanhThu ?? 0),
            ];
        }

        // 4. Sản phẩm bán chạy (Top 10)
        $sanPhamBanChay = ChiTietDonHang::join('ChiTietSanPham', 'ChiTietDonHang.MaChiTietSanPham', '=', 'ChiTietSanPham.MaChiTiet')
            ->join('SanPham', 'ChiTietSanPham.MaSanPham', '=', 'SanPham.MaSanPham')
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

        return view('QuanLySanPham.Dashboard', compact(
            'namHienTai',
            'tongDoanhThuNam',
            'soLuongDonNam',
            'doanhThuQuy',
            'doanhThuThang',
            'sanPhamBanChay'
        ));
    }
}
