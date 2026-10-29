<?php

use App\Http\Controllers\BangTinController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\QuanLyBangTinController;
use App\Http\Controllers\QuanLyDonHangController;
use App\Http\Controllers\QuanLyKhuyenMaiController;
use App\Http\Controllers\QuanLySanPhamController;
use App\Http\Controllers\QuanLyTaiKhoanController;
use App\Http\Controllers\QuanLyThongBaoController;
use App\Http\Controllers\QuanLyTranDauController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThemPhanHoiSPController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| KHU VỰC NGƯỜI DÙNG (công khai + khách hàng đã đăng nhập)
|--------------------------------------------------------------------------
*/

// 1. Trang chủ
// Mặc định vào trang chủ là landing page; trang danh mục sản phẩm tại /Home/Index
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/Home/Index', [HomeController::class, 'index'])->name('home.cuahang');
Route::get('/Home/Contact', [HomeController::class, 'contact'])->name('home.contact');
Route::get('/Home/ChiTietSanPham/{id}', [HomeController::class, 'chiTietSanPham'])->whereNumber('id')->name('home.chitietsanpham');

// 1b. Landing page giới thiệu (tự chuyển về trang chủ)
Route::redirect('/Landing', '/')->name('landing');

// 1c. Bảng tin cầu lông (công khai): tin sản phẩm hot/mới, kết quả trận đấu, tuyển thủ, giải đấu
Route::get('/BangTin', [BangTinController::class, 'index'])->name('bangtin.index');
Route::get('/BangTin/{id}', [BangTinController::class, 'show'])->whereNumber('id')->name('bangtin.show');

// 2. Sản phẩm - Tìm kiếm (có phân trang) - Khuyến mãi
Route::get('/SanPham/Index', [SanPhamController::class, 'index'])->name('sanpham.index');
Route::get('/SanPham/ViewAllProduct', [SanPhamController::class, 'index'])->name('sanpham.viewallproduct');
Route::get('/SanPham/TimKiem', [SanPhamController::class, 'timKiem'])->name('sanpham.timkiem');
Route::get('/SanPham/KhuyenMai', [SanPhamController::class, 'khuyenMai'])->name('sanpham.khuyenmai');
Route::get('/KhuyenMai/Index', [SanPhamController::class, 'khuyenMai'])->name('khuyenmai.index');

// 3. Giỏ hàng: đếm số lượng (AJAX) + thêm vào giỏ (controller tự chuyển về trang đăng nhập nếu là khách)
Route::get('/GioHang/GetCartCount', [GioHangController::class, 'getCartCount'])->name('giohang.getcartcount');
Route::post('/Home/AddToCart', [HomeController::class, 'addToCart'])->name('home.addtocart');

// 4. Tài khoản: đăng nhập / đăng ký (chỉ khách chưa đăng nhập)
Route::get('/TaiKhoan/DangNhap', [TaiKhoanController::class, 'dangNhap'])->name('taikhoan.dangnhap');
Route::post('/TaiKhoan/DangNhap', [TaiKhoanController::class, 'dangNhapPost'])->name('taikhoan.dangnhap.post');
Route::get('/TaiKhoan/DangKy', [TaiKhoanController::class, 'dangKy'])->name('taikhoan.dangky');
Route::post('/TaiKhoan/DangKy', [TaiKhoanController::class, 'dangKyPost'])->name('taikhoan.dangky.post');

// 5. Thanh toán MoMo: MoMo gọi về (không có session đăng nhập)
Route::get('/DonHang/ThanhToanMomoSuccess', [DonHangController::class, 'thanhToanMomoSuccess'])->name('donhang.momosuccess');
Route::post('/DonHang/SavePayment', [DonHangController::class, 'savePayment'])->name('donhang.savepayment');

// 6. AI Chatbot (Google Gemini) - AJAX
Route::post('/Chatbot/Ask', [ChatbotController::class, 'ask'])->middleware('throttle:20,1')->name('chatbot.ask');

// 7. Các chức năng yêu cầu đăng nhập (Middleware check.login)
Route::middleware('check.login')->group(function () {
    // Tài khoản cá nhân
    Route::post('/TaiKhoan/DangXuat', [TaiKhoanController::class, 'dangXuat'])->name('taikhoan.dangxuat');
    Route::get('/TaiKhoan/DoiMatKhau', [TaiKhoanController::class, 'doiMatKhau'])->name('taikhoan.doimatkhau');
    Route::post('/TaiKhoan/DoiMatKhau', [TaiKhoanController::class, 'doiMatKhauPost'])->name('taikhoan.doimatkhau.post');
    Route::get('/TaiKhoan/ThongTin', [TaiKhoanController::class, 'thongTin'])->name('taikhoan.thongtin');

    // Giỏ hàng
    Route::get('/Home/ViewCart', [HomeController::class, 'viewCart'])->name('home.viewcart');
    Route::post('/Home/RemoveFromCart/{maGioHang}', [HomeController::class, 'removeFromCart'])->whereNumber('maGioHang')->name('home.removefromcart');

    // Đơn hàng & Thanh toán
    Route::get('/DonHang/Checkout', [DonHangController::class, 'checkout'])->name('donhang.checkout');
    Route::post('/DonHang/Checkout', [DonHangController::class, 'checkoutPost'])->name('donhang.checkout.post');
    Route::post('/DonHang/PaymentMoMo', [DonHangController::class, 'paymentMoMo'])->name('donhang.payment.momo');
    Route::get('/DonHang/Index', [DonHangController::class, 'index'])->name('donhang.index');
    Route::get('/DonHang/Details/{id}', [DonHangController::class, 'details'])->whereNumber('id')->name('donhang.details');
    Route::post('/DonHang/HuyDonHang/{id}', [DonHangController::class, 'huyDonHang'])->whereNumber('id')->name('donhang.huydonhang');

    // Đánh giá sản phẩm
    Route::get('/ThemPhanHoiSP/ReviewForm/{maSP}', [ThemPhanHoiSPController::class, 'reviewForm'])->whereNumber('maSP')->name('themphanhoisp.reviewform');
    Route::post('/ThemPhanHoiSP/SubmitReview', [ThemPhanHoiSPController::class, 'submitReview'])->name('themphanhoisp.submitreview');
});

/*
|--------------------------------------------------------------------------
| KHU VỰC QUẢN TRỊ: /admin
|--------------------------------------------------------------------------
| - role:admin,nhanvien : Quản trị viên + Nhân viên (sản phẩm, đơn hàng, thống kê, cảnh báo)
| - role:admin          : chỉ Quản trị viên (tài khoản, khuyến mãi)
*/
// Khu vực quản trị: URL /admin
// - Chưa đăng nhập: hiển thị trang đăng nhập admin đơn giản tại /admin
// - Đã đăng nhập (admin / nhân viên): hiển thị Dashboard thống kê
Route::get('/admin', [QuanLySanPhamController::class, 'dashboard'])->name('quanlysanpham.dashboard');
Route::post('/admin', [TaiKhoanController::class, 'adminDangNhapPost'])->name('admin.login.post');
Route::redirect('/admin/login', '/admin');

Route::prefix('admin')->middleware('role:admin,nhanvien')->group(function () {

    // Quản lý sản phẩm (CRUD + tìm kiếm, phân trang)
    Route::prefix('san-pham')->whereNumber('id')->group(function () {
        Route::get('/', [QuanLySanPhamController::class, 'index'])->name('quanlysanpham.index');
        Route::get('/create', [QuanLySanPhamController::class, 'create'])->name('quanlysanpham.create');
        Route::post('/create', [QuanLySanPhamController::class, 'createPost'])->name('quanlysanpham.create.post');
        Route::get('/{id}', [QuanLySanPhamController::class, 'details'])->name('quanlysanpham.details');
        Route::get('/{id}/edit', [QuanLySanPhamController::class, 'edit'])->name('quanlysanpham.edit');
        Route::post('/{id}/edit', [QuanLySanPhamController::class, 'editPost'])->name('quanlysanpham.edit.post');
        Route::get('/{id}/delete', [QuanLySanPhamController::class, 'delete'])->name('quanlysanpham.delete');
        Route::post('/{id}/delete', [QuanLySanPhamController::class, 'deleteConfirmed'])->name('quanlysanpham.delete.post');

        // Biến thể sản phẩm (ChiTietSanPham: màu, size, giá bán, tồn kho)
        Route::post('/{id}/bien-the', [QuanLySanPhamController::class, 'bienTheStore'])->name('quanlysanpham.bienthe.store');
    });
    Route::prefix('bien-the')->whereNumber('maChiTiet')->group(function () {
        Route::get('/{maChiTiet}/edit', [QuanLySanPhamController::class, 'bienTheEdit'])->name('quanlysanpham.bienthe.edit');
        Route::post('/{maChiTiet}/edit', [QuanLySanPhamController::class, 'bienTheUpdate'])->name('quanlysanpham.bienthe.update');
        Route::post('/{maChiTiet}/delete', [QuanLySanPhamController::class, 'bienTheDestroy'])->name('quanlysanpham.bienthe.destroy');
    });

    // Quản lý đơn hàng
    Route::prefix('don-hang')->whereNumber('id')->group(function () {
        Route::get('/', [QuanLyDonHangController::class, 'index'])->name('quanlydonhang.index');
        Route::get('/{id}', [QuanLyDonHangController::class, 'details'])->name('quanlydonhang.details');
        Route::post('/{id}/trang-thai', [QuanLyDonHangController::class, 'setTrangThai'])->name('quanlydonhang.settrangthai');
    });

    // Quản lý bảng tin: bài viết (xóa mềm) + kết quả trận đấu
    Route::prefix('ban-tin')->whereNumber('id')->group(function () {
        Route::get('/', [QuanLyBangTinController::class, 'index'])->name('quanlybantin.index');
        Route::get('/create', [QuanLyBangTinController::class, 'create'])->name('quanlybantin.create');
        Route::post('/create', [QuanLyBangTinController::class, 'createPost'])->name('quanlybantin.create.post');
        Route::get('/{id}/edit', [QuanLyBangTinController::class, 'edit'])->name('quanlybantin.edit');
        Route::post('/{id}/edit', [QuanLyBangTinController::class, 'editPost'])->name('quanlybantin.edit.post');
        Route::get('/{id}/delete', [QuanLyBangTinController::class, 'delete'])->name('quanlybantin.delete');
        Route::post('/{id}/delete', [QuanLyBangTinController::class, 'deleteConfirmed'])->name('quanlybantin.delete.post');
    });
    Route::prefix('tran-dau')->whereNumber('id')->group(function () {
        Route::get('/', [QuanLyTranDauController::class, 'index'])->name('quanlytrandau.index');
        Route::get('/create', [QuanLyTranDauController::class, 'create'])->name('quanlytrandau.create');
        Route::post('/create', [QuanLyTranDauController::class, 'createPost'])->name('quanlytrandau.create.post');
        Route::get('/{id}/edit', [QuanLyTranDauController::class, 'edit'])->name('quanlytrandau.edit');
        Route::post('/{id}/edit', [QuanLyTranDauController::class, 'editPost'])->name('quanlytrandau.edit.post');
        Route::get('/{id}/delete', [QuanLyTranDauController::class, 'delete'])->name('quanlytrandau.delete');
        Route::post('/{id}/delete', [QuanLyTranDauController::class, 'deleteConfirmed'])->name('quanlytrandau.delete.post');
    });

    // Cảnh báo đánh giá thấp
    Route::get('/thong-bao', [QuanLyThongBaoController::class, 'thongBao'])->name('quanlythongbao.thongbao');
    Route::get('/thong-bao/so-luong', [QuanLyThongBaoController::class, 'soThongBao'])->name('quanlythongbao.sothongbao');

    // ---- Chỉ dành cho Quản trị viên ----
    Route::middleware('role:admin')->group(function () {
        // Quản lý tài khoản
        Route::prefix('tai-khoan')->whereNumber('id')->group(function () {
            Route::get('/', [QuanLyTaiKhoanController::class, 'index'])->name('quanlytaikhoan.index');
            Route::get('/create', [QuanLyTaiKhoanController::class, 'create'])->name('quanlytaikhoan.create');
            Route::post('/create', [QuanLyTaiKhoanController::class, 'createPost'])->name('quanlytaikhoan.create.post');
            Route::get('/{id}/edit', [QuanLyTaiKhoanController::class, 'edit'])->name('quanlytaikhoan.edit');
            Route::post('/{id}/edit', [QuanLyTaiKhoanController::class, 'editPost'])->name('quanlytaikhoan.edit.post');
            Route::get('/{id}/delete', [QuanLyTaiKhoanController::class, 'delete'])->name('quanlytaikhoan.delete');
            Route::post('/{id}/delete', [QuanLyTaiKhoanController::class, 'deleteConfirmed'])->name('quanlytaikhoan.delete.post');
        });

        // Quản lý khuyến mãi
        Route::prefix('khuyen-mai')->whereNumber('id')->group(function () {
            Route::get('/', [QuanLyKhuyenMaiController::class, 'index'])->name('quanlykhuyenmai.index');
            Route::get('/create', [QuanLyKhuyenMaiController::class, 'create'])->name('quanlykhuyenmai.create');
            Route::post('/create', [QuanLyKhuyenMaiController::class, 'createPost'])->name('quanlykhuyenmai.create.post');
            Route::get('/{id}/edit', [QuanLyKhuyenMaiController::class, 'edit'])->name('quanlykhuyenmai.edit');
            Route::post('/{id}/edit', [QuanLyKhuyenMaiController::class, 'editPost'])->name('quanlykhuyenmai.edit.post');
            Route::get('/{id}/delete', [QuanLyKhuyenMaiController::class, 'delete'])->name('quanlykhuyenmai.delete');
            Route::post('/{id}/delete', [QuanLyKhuyenMaiController::class, 'deleteConfirmed'])->name('quanlykhuyenmai.delete.post');
        });
    });
});
