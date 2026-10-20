<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\ThemPhanHoiSPController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\QuanLySanPhamController;
use App\Http\Controllers\QuanLyDonHangController;
use App\Http\Controllers\QuanLyTaiKhoanController;
use App\Http\Controllers\QuanLyKhuyenMaiController;
use App\Http\Controllers\QuanLyThongBaoController;

// 1. Home / Khách hàng
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/Home/Index', [HomeController::class, 'index']);
Route::get('/Home/Contact', [HomeController::class, 'contact'])->name('home.contact');
Route::get('/Home/ChiTietSanPham/{id}', [HomeController::class, 'chiTietSanPham'])->name('home.chitietsanpham');
Route::post('/Home/AddToCart', [HomeController::class, 'addToCart'])->name('home.addtocart');
Route::get('/Home/ViewCart', [HomeController::class, 'viewCart'])->name('home.viewcart');
Route::get('/Home/RemoveFromCart/{maGioHang}', [HomeController::class, 'removeFromCart'])->name('home.removefromcart');

// 2. Sản phẩm & Tìm kiếm & Khuyến mãi
Route::get('/SanPham/Index', [SanPhamController::class, 'index'])->name('sanpham.index');
Route::get('/SanPham/ViewAllProduct', [SanPhamController::class, 'index'])->name('sanpham.viewallproduct');
Route::get('/SanPham/TimKiem', [SanPhamController::class, 'timKiem'])->name('sanpham.timkiem');
Route::get('/SanPham/KhuyenMai', [SanPhamController::class, 'khuyenMai'])->name('sanpham.khuyenmai');
Route::get('/KhuyenMai/Index', [SanPhamController::class, 'khuyenMai'])->name('khuyenmai.index');

// 3. Giỏ hàng AJAX
Route::get('/GioHang/GetCartCount', [GioHangController::class, 'getCartCount'])->name('giohang.getcartcount');
Route::get('/GioHang/RemoveFromCart/{chiTietId}', [GioHangController::class, 'removeFromCart'])->name('giohang.removefromcart');

// 4. Tài khoản
Route::get('/TaiKhoan/DangNhap', [TaiKhoanController::class, 'dangNhap'])->name('taikhoan.dangnhap');
Route::post('/TaiKhoan/DangNhap', [TaiKhoanController::class, 'dangNhapPost']);
Route::get('/TaiKhoan/DangKy', [TaiKhoanController::class, 'dangKy'])->name('taikhoan.dangky');
Route::post('/TaiKhoan/DangKy', [TaiKhoanController::class, 'dangKyPost']);
Route::get('/TaiKhoan/DangXuat', [TaiKhoanController::class, 'dangXuat'])->name('taikhoan.dangxuat');

Route::middleware('check.login')->group(function () {
    Route::get('/TaiKhoan/DoiMatKhau', [TaiKhoanController::class, 'doiMatKhau'])->name('taikhoan.doimatkhau');
    Route::post('/TaiKhoan/DoiMatKhau', [TaiKhoanController::class, 'doiMatKhauPost']);
    Route::get('/TaiKhoan/ThongTin', [TaiKhoanController::class, 'thongTin'])->name('taikhoan.thongtin');
});

// 5. Đơn hàng & Thanh toán
Route::middleware('check.login')->group(function () {
    Route::get('/DonHang/Checkout', [DonHangController::class, 'checkout'])->name('donhang.checkout');
    Route::post('/DonHang/Checkout', [DonHangController::class, 'checkoutPost']);
    Route::post('/DonHang/PaymentMoMo', [DonHangController::class, 'paymentMoMo'])->name('donhang.paymentmomo');
    Route::get('/DonHang/Index', [DonHangController::class, 'index'])->name('donhang.index');
    Route::get('/DonHang/Details/{id}', [DonHangController::class, 'details'])->name('donhang.details');
    Route::post('/DonHang/HuyDonHang/{id}', [DonHangController::class, 'huyDonHang'])->name('donhang.huydonhang');
});

Route::get('/DonHang/ThanhToanMomoSuccess', [DonHangController::class, 'thanhToanMomoSuccess'])->name('donhang.momosuccess');
Route::post('/DonHang/SavePayment', [DonHangController::class, 'savePayment'])->name('donhang.savepayment');

// 6. Đánh giá sản phẩm
Route::get('/ThemPhanHoiSP/ReviewForm/{maSP}', [ThemPhanHoiSPController::class, 'reviewForm'])->name('themphanhoisp.reviewform');
Route::post('/ThemPhanHoiSP/SubmitReview', [ThemPhanHoiSPController::class, 'submitReview'])->name('themphanhoisp.submitreview');

// 7. AI Chatbot (Google Gemini)
Route::post('/Chatbot/Ask', [ChatbotController::class, 'ask'])->name('chatbot.ask');

// 8. Quản trị Admin / Nhân viên
Route::middleware('check.admin')->prefix('QuanLySanPham')->group(function () {
    Route::get('/', [QuanLySanPhamController::class, 'index']);
    Route::get('/Index', [QuanLySanPhamController::class, 'index'])->name('quanlysanpham.index');
    Route::get('/Create', [QuanLySanPhamController::class, 'create'])->name('quanlysanpham.create');
    Route::post('/Create', [QuanLySanPhamController::class, 'createPost']);
    Route::get('/Edit/{id}', [QuanLySanPhamController::class, 'edit'])->name('quanlysanpham.edit');
    Route::post('/Edit/{id}', [QuanLySanPhamController::class, 'editPost']);
    Route::get('/Details/{id}', [QuanLySanPhamController::class, 'details'])->name('quanlysanpham.details');
    Route::get('/Delete/{id}', [QuanLySanPhamController::class, 'delete'])->name('quanlysanpham.delete');
    Route::post('/Delete/{id}', [QuanLySanPhamController::class, 'deleteConfirmed']);
    Route::get('/Dashboard', [QuanLySanPhamController::class, 'dashboard'])->name('quanlysanpham.dashboard');
});

Route::middleware('check.admin')->prefix('QuanLyDonHang')->group(function () {
    Route::get('/', [QuanLyDonHangController::class, 'index']);
    Route::get('/Index', [QuanLyDonHangController::class, 'index'])->name('quanlydonhang.index');
    Route::get('/Details/{id}', [QuanLyDonHangController::class, 'details'])->name('quanlydonhang.details');
    Route::post('/SetTrangThai/{id}', [QuanLyDonHangController::class, 'setTrangThai'])->name('quanlydonhang.settrangthai');
    Route::post('/SetTrangThai', function (Illuminate\Http\Request $request, App\Services\OrderService $orderService) {
        $id = $request->input('id');
        return app(QuanLyDonHangController::class)->setTrangThai($request, $id, $orderService);
    });
});

Route::middleware('check.admin')->prefix('QuanLyTaiKhoan')->group(function () {
    Route::get('/', [QuanLyTaiKhoanController::class, 'index']);
    Route::get('/Index', [QuanLyTaiKhoanController::class, 'index'])->name('quanlytaikhoan.index');
    Route::get('/Create', [QuanLyTaiKhoanController::class, 'create'])->name('quanlytaikhoan.create');
    Route::post('/Create', [QuanLyTaiKhoanController::class, 'createPost']);
    Route::get('/Edit/{id}', [QuanLyTaiKhoanController::class, 'edit'])->name('quanlytaikhoan.edit');
    Route::post('/Edit/{id}', [QuanLyTaiKhoanController::class, 'editPost']);
    Route::get('/Delete/{id}', [QuanLyTaiKhoanController::class, 'delete'])->name('quanlytaikhoan.delete');
    Route::post('/Delete/{id}', [QuanLyTaiKhoanController::class, 'deleteConfirmed']);
});

Route::middleware('check.admin')->prefix('QuanLyKhuyenMai')->group(function () {
    Route::get('/', [QuanLyKhuyenMaiController::class, 'index']);
    Route::get('/Index', [QuanLyKhuyenMaiController::class, 'index'])->name('quanlykhuyenmai.index');
    Route::get('/Create', [QuanLyKhuyenMaiController::class, 'create'])->name('quanlykhuyenmai.create');
    Route::post('/Create', [QuanLyKhuyenMaiController::class, 'createPost']);
    Route::get('/Edit/{id}', [QuanLyKhuyenMaiController::class, 'edit'])->name('quanlykhuyenmai.edit');
    Route::post('/Edit/{id}', [QuanLyKhuyenMaiController::class, 'editPost']);
    Route::get('/Delete/{id}', [QuanLyKhuyenMaiController::class, 'delete'])->name('quanlykhuyenmai.delete');
    Route::post('/Delete/{id}', [QuanLyKhuyenMaiController::class, 'deleteConfirmed']);
});

Route::middleware('check.admin')->prefix('QuanLyThongBao')->group(function () {
    Route::get('/ThongBao', [QuanLyThongBaoController::class, 'thongBao'])->name('quanlythongbao.thongbao');
    Route::get('/SoThongBao', [QuanLyThongBaoController::class, 'soThongBao'])->name('quanlythongbao.sothongbao');
});
