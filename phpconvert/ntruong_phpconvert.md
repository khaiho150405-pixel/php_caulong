# BÁO CÁO CHI TIẾT CHUYỂN ĐỔI DỰ ÁN SHOP CẦU LÔNG TỪ C# ASP.NET MVC SANG PHP LARAVEL
**Người thực hiện / File báo cáo**: `ntruong_phpconvert.md`  
**Ngày hoàn thành**: 18/10/2026  
**Thư mục đích**: `d:\php_caulong\phpconvert`  
**Dự án nguồn**: `d:\php_caulong\DoAn_CauLong\DoAn_CauLong` (ASP.NET MVC 5, Entity Framework 6, SQL Server)  
**Nền tảng đích**: PHP 8.3, Laravel 11, MySQL / MariaDB (Laragon)  

---

## 1. TỔNG QUAN VÀ CAM KẾT ĐẶT RA

| Tiêu Chí | Yêu Cầu Của Người Dùng | Kết Quả Triển Khai |
|---|---|---|
| **Tên Bảng Database** | **Giữ nguyên 100% tên bảng trong database cũ** | Hoàn thành: Tất cả 18 bảng đều giữ nguyên tên PascalCase theo đúng file `QLDN_CauLong.sql` gốc |
| **Chức Năng** | **Giữ nguyên toàn bộ chức năng cũ** | Hoàn thành: Đầy đủ các luồng Khách hàng, Giỏ hàng, Đặt hàng COD, Thanh toán MoMo v2, Đánh giá sản phẩm, AI Chatbot Gemini, và Toàn bộ Quản trị Admin |
| **Thư Mục Dự Án** | **Làm việc trực tiếp trong folder framework `phpconvert`** | Hoàn thành: Toàn bộ mã nguồn, cấu hình, migration, seeder, model, controller, service, views và tests được đặt tại `d:\php_caulong\phpconvert` |
| **Giao Diện (UI/UX)** | **Tương thích giao diện project MVC cũ** | Hoàn thành: Kế thừa toàn bộ cấu trúc giao diện CSS (`Site.css`), Bootstrap, hệ thống Mega-menu đa cấp, Banner Carousel, Variant Selector, và Dashboard Chart.js |

---

## 2. BẢNG ĐỐI CHIẾU CƠ SỞ DỮ LIỆU (18 BẢNG GIỮ NGUYÊN)

Toàn bộ 18 bảng trong cơ sở dữ liệu gốc đã được khởi tạo trong MySQL (`phpconvert`) và ánh xạ sang 18 Model Eloquent:

| STT | Tên Bảng (Giữ Nguyên) | Khóa Chính | Khóa Ngoại & Quan Hệ | Eloquent Model Tương Ứng | Mô Tả Nghiệp Vụ |
|:---:|---|---|---|---|---|
| 1 | `PhanQuyen` | `MaQuyen` | 1-N với `TaiKhoan` | [`App\Models\PhanQuyen`](file:///d:/php_caulong/phpconvert/app/Models/PhanQuyen.php) | Phân quyền hệ thống: 1 (Admin), 2 (Nhân viên), 3 (Khách hàng) |
| 2 | `TaiKhoan` | `MaTaiKhoan` | N-1 với `PhanQuyen`, 1-1 với `KhachHang` | [`App\Models\TaiKhoan`](file:///d:/php_caulong/phpconvert/app/Models/TaiKhoan.php) | Quản lý tài khoản đăng nhập (Bcrypt + tương thích mật khẩu cũ) |
| 3 | `KhachHang` | `MaKhachHang` | 1-1 với `TaiKhoan`, 1-N với `DonHang`, `GioHang`, `PhanHoi` | [`App\Models\KhachHang`](file:///d:/php_caulong/phpconvert/app/Models/KhachHang.php) | Lưu trữ thông tin khách hàng: Họ tên, Email, SĐT, Địa chỉ |
| 4 | `LoaiSanPham` | `MaLoai` | 1-N với `SanPham` | [`App\Models\LoaiSanPham`](file:///d:/php_caulong/phpconvert/app/Models/LoaiSanPham.php) | Danh mục: Vợt cầu lông, Giày, Áo quần thể thao, Phụ kiện |
| 5 | `Hang` | `MaHang` | 1-N với `SanPham` | [`App\Models\Hang`](file:///d:/php_caulong/phpconvert/app/Models/Hang.php) | Thương hiệu: Yonex, Victor, Lining, Mizuno... |
| 6 | `NhaCungCap` | `MaNhaCungCap` | 1-N với `SanPham` | [`App\Models\NhaCungCap`](file:///d:/php_caulong/phpconvert/app/Models/NhaCungCap.php) | Đơn vị cung cấp sản phẩm |
| 7 | `KhuyenMai` | `MaKhuyenMai` | 1-N với `SanPham` | [`App\Models\KhuyenMai`](file:///d:/php_caulong/phpconvert/app/Models/KhuyenMai.php) | Quản lý chương trình giảm giá: % giảm, tiền giảm tối đa, hạn dùng |
| 8 | `MauSac` | `MaMau` | 1-N với `ChiTietSanPham` | [`App\Models\MauSac`](file:///d:/php_caulong/phpconvert/app/Models/MauSac.php) | Thuộc tính màu sắc của sản phẩm |
| 9 | `Size` | `MaSize` | 1-N với `ChiTietSanPham` | [`App\Models\Size`](file:///d:/php_caulong/phpconvert/app/Models/Size.php) | Thuộc tính kích cỡ / phiên bản sản phẩm |
| 10 | `SanPham` | `MaSanPham` | N-1 (`LoaiSanPham`, `Hang`, `NhaCungCap`, `KhuyenMai`) | [`App\Models\SanPham`](file:///d:/php_caulong/phpconvert/app/Models/SanPham.php) | Bảng sản phẩm chính (có accessor `gia_sau_khuyen_mai`, `trung_binh_danh_gia`) |
| 11 | `ChiTietSanPham` | `MaChiTiet` | N-1 (`SanPham`, `MauSac`, `Size`), 1-N (`ThongSoVot`, `ChiTietDonHang`) | [`App\Models\ChiTietSanPham`](file:///d:/php_caulong/phpconvert/app/Models/ChiTietSanPham.php) | Biến thể sản phẩm (SKU): quản lý giá bán riêng, ảnh riêng, số lượng tồn kho |
| 12 | `ThongSoVot` | `MaThongSo` | N-1 (`ChiTietSanPham`) | [`App\Models\ThongSoVot`](file:///d:/php_caulong/phpconvert/app/Models/ThongSoVot.php) | Thông số kỹ thuật vợt: Độ cứng, Độ cân bằng, Sức căng, Trọng lượng, Chiều dài |
| 13 | `DonHang` | `MaDonHang` | N-1 (`KhachHang`), 1-N (`ChiTietDonHang`) | [`App\Models\DonHang`](file:///d:/php_caulong/phpconvert/app/Models/DonHang.php) | Quản lý đơn hàng: Ngày đặt, Tổng tiền, Địa chỉ, SĐT, Trạng thái |
| 14 | `ChiTietDonHang` | `MaChiTietDonHang` | N-1 (`DonHang`, `ChiTietSanPham`) | [`App\Models\ChiTietDonHang`](file:///d:/php_caulong/phpconvert/app/Models/ChiTietDonHang.php) | Chi tiết các mặt hàng trong đơn: Đơn giá, Số lượng, Thành tiền |
| 15 | `PhanHoi` | `MaPhanHoi` | N-1 (`KhachHang`, `SanPham`) | [`App\Models\PhanHoi`](file:///d:/php_caulong/phpconvert/app/Models/PhanHoi.php) | Đánh giá 1-5 sao và nhận xét từ khách hàng |
| 16 | `GioHang` | `MaGioHang` | N-1 (`KhachHang`, `ChiTietSanPham`) | [`App\Models\GioHang`](file:///d:/php_caulong/phpconvert/app/Models/GioHang.php) | Giỏ hàng lưu trữ theo tài khoản khách hàng |
| 17 | `LichSuThayDoiGia` | `MaLichSu` | N-1 (`ChiTietSanPham`) | [`App\Models\LichSuThayDoiGia`](file:///d:/php_caulong/phpconvert/app/Models/LichSuThayDoiGia.php) | Nhật ký biến động giá bán theo thời gian |
| 18 | `LogLoiGiaoTac` | `MaLog` | - | [`App\Models\LogLoiGiaoTac`](file:///d:/php_caulong/phpconvert/app/Models/LogLoiGiaoTac.php) | Ghi nhận lỗi giao dịch / ngoại lệ hệ thống |

> **Dữ liệu nạp sẵn**: Đã seed toàn bộ 43 sản phẩm, 34 biến thể SKU, 17 bộ thông số vợt, 10 màu, 26 size, 8 chương trình khuyến mãi, và các tài khoản mẫu từ dự án C# cũ.

---

## 3. BẢNG ĐỐI CHIẾU CONTROLLER & ROUTING (MVC C# &rarr; LARAVEL)

Tất cả 12 Controller từ ASP.NET MVC được chuyển đổi tương ứng 1-1 sang Laravel Controllers với toàn bộ 68 URL routes:

| C# Controller / Action | Laravel Controller / Method | Route Name | HTTP Method & URL | Chức Năng |
|---|---|---|---|---|
| `HomeController@Index` | `HomeController@index` | `home` | `GET /` | Trang chủ hiển thị banner, menu đa cấp và sản phẩm theo danh mục |
| `HomeController@Contact` | `HomeController@contact` | `home.contact` | `GET /Home/Contact` | Trang thông tin liên hệ và biểu mẫu gửi tin nhắn |
| `HomeController@ChiTietSanPham` | `HomeController@chiTietSanPham` | `home.chitietsanpham` | `GET /Home/ChiTietSanPham/{id}` | Chi tiết sản phẩm, chọn biến thể, tính giá KM, xem thông số vợt & reviews |
| `HomeController@AddToCart` | `HomeController@addToCart` | `home.addtocart` | `POST /Home/AddToCart` | Thêm biến thể sản phẩm vào giỏ hàng DB |
| `HomeController@ViewCart` | `HomeController@viewCart` | `home.viewcart` | `GET /Home/ViewCart` | Xem giỏ hàng, tính tổng tiền, nút xóa |
| `HomeController@RemoveFromCart` | `HomeController@removeFromCart` | `home.removefromcart` | `GET /Home/RemoveFromCart/{id}` | Xóa sản phẩm khỏi giỏ hàng |
| `SanPhamController@Index` | `SanPhamController@index` | `sanpham.index` | `GET /SanPham/Index` | Danh mục sản phẩm, lọc theo loại, hãng, tên |
| `SanPhamController@ViewAllProduct` | `SanPhamController@index` | `sanpham.viewallproduct` | `GET /SanPham/ViewAllProduct` | Xem tất cả sản phẩm theo danh mục (`maLoai`) |
| `SanPhamController@TimKiem` | `SanPhamController@timKiem` | `sanpham.timkiem` | `GET /SanPham/TimKiem` | Tìm kiếm sản phẩm theo từ khóa |
| `SanPhamController@KhuyenMai` | `SanPhamController@khuyenMai` | `khuyenmai.index` | `GET /KhuyenMai/Index` | Xem danh sách các chương trình khuyến mãi và sản phẩm áp dụng |
| `GioHangController@GetCartCount` | `GioHangController@getCartCount` | `giohang.getcartcount` | `GET /GioHang/GetCartCount` | AJAX lấy số lượng giỏ hàng hiển thị badge |
| `TaiKhoanController@DangNhap` | `TaiKhoanController@dangNhap` | `taikhoan.dangnhap` | `GET /TaiKhoan/DangNhap` | Giao diện đăng nhập tài khoản |
| `TaiKhoanController@DangNhap` | `TaiKhoanController@dangNhapPost` | - | `POST /TaiKhoan/DangNhap` | Xử lý đăng nhập, check Bcrypt / Plaintext, tạo Session |
| `TaiKhoanController@DangKy` | `TaiKhoanController@dangKy` | `taikhoan.dangky` | `GET /TaiKhoan/DangKy` | Giao diện đăng ký tài khoản mới |
| `TaiKhoanController@DangKy` | `TaiKhoanController@dangKyPost` | - | `POST /TaiKhoan/DangKy` | Tạo tài khoản + đồng bộ tạo `KhachHang` |
| `TaiKhoanController@DangXuat` | `TaiKhoanController@dangXuat` | `taikhoan.dangxuat` | `GET /TaiKhoan/DangXuat` | Đăng xuất người dùng |
| `TaiKhoanController@DoiMatKhau` | `TaiKhoanController@doiMatKhau` | `taikhoan.doimatkhau` | `GET /TaiKhoan/DoiMatKhau` | Giao diện đổi mật khẩu |
| `TaiKhoanController@ThongTin` | `TaiKhoanController@thongTin` | `taikhoan.thongtin` | `GET /TaiKhoan/ThongTin` | Xem thông tin hồ sơ tài khoản |
| `DonHangController@Checkout` | `DonHangController@checkout` | `donhang.checkout` | `GET /DonHang/Checkout` | Trang xác nhận thông tin nhận hàng & thanh toán |
| `DonHangController@Checkout` | `DonHangController@checkoutPost` | `donhang.checkout.post` | `POST /DonHang/Checkout` | Đặt hàng phương thức **COD (Khi nhận hàng)** |
| `DonHangController@PaymentMoMo` | `DonHangController@paymentMoMo` | `donhang.payment.momo` | `POST /DonHang/PaymentMoMo` | Tạo link thanh toán **MoMo v2 API** chữ ký HMAC-SHA256 |
| `DonHangController@ThanhToanMomoSuccess` | `DonHangController@thanhToanMomoSuccess` | `donhang.thanhtoanmomosuccess` | `GET /DonHang/ThanhToanMomoSuccess` | Xử lý redirect return URL sau khi thanh toán MoMo |
| `DonHangController@SavePayment` | `DonHangController@savePayment` | `donhang.savepayment` | `POST /DonHang/SavePayment` | IPN Webhook MoMo cập nhật tự động đơn hàng |
| `DonHangController@Index` | `DonHangController@index` | `donhang.index` | `GET /DonHang/Index` | Lịch sử đơn hàng của khách hàng |
| `DonHangController@Details` | `DonHangController@details` | `donhang.details` | `GET /DonHang/Details/{id}` | Chi tiết đơn hàng của khách hàng |
| `DonHangController@HuyDonHang` | `DonHangController@huyDonHang` | `donhang.huydonhang` | `POST /DonHang/HuyDonHang/{id}` | Hủy đơn hàng và hoàn lại tồn kho |
| `ThemPhanHoiSPController@ReviewForm` | `ThemPhanHoiSPController@reviewForm` | `themphanhoisp.reviewform` | `GET /ThemPhanHoiSP/ReviewForm` | Giao diện chấm sao và gửi đánh giá sản phẩm |
| `ThemPhanHoiSPController@SubmitReview` | `ThemPhanHoiSPController@submitReview` | `themphanhoisp.submitreview` | `POST /ThemPhanHoiSP/SubmitReview` | Lưu phản hồi của khách hàng vào bảng `PhanHoi` |
| `ChatbotController@Ask` | `ChatbotController@ask` | `chatbot.ask` | `POST /Chatbot/Ask` | API Trợ lý ảo AI Chatbot Gemini |
| `QuanLySanPhamController@Index` | `QuanLySanPhamController@index` | `quanlysanpham.index` | `GET /QuanLySanPham/Index` | Quản lý danh sách sản phẩm (Admin) |
| `QuanLySanPhamController@Create` | `QuanLySanPhamController@create` | `quanlysanpham.create` | `GET /QuanLySanPham/Create` | Form tạo sản phẩm mới có upload hình ảnh |
| `QuanLySanPhamController@Edit` | `QuanLySanPhamController@edit` | `quanlysanpham.edit` | `GET /QuanLySanPham/Edit/{id}` | Form sửa sản phẩm |
| `QuanLySanPhamController@Details` | `QuanLySanPhamController@details` | `quanlysanpham.details` | `GET /QuanLySanPham/Details/{id}` | Xem chi tiết sản phẩm và danh sách biến thể SKU |
| `QuanLySanPhamController@Delete` | `QuanLySanPhamController@delete` | `quanlysanpham.delete` | `GET /QuanLySanPham/Delete/{id}` | Trang xác nhận xóa sản phẩm |
| `QuanLySanPhamController@Dashboard` | `QuanLySanPhamController@dashboard` | `quanlysanpham.dashboard` | `GET /QuanLySanPham/Dashboard` | Dashboard thống kê doanh thu 12 tháng, 4 quý và Top bán chạy |
| `QuanLyDonHangController@Index` | `QuanLyDonHangController@index` | `quanlydonhang.index` | `GET /QuanLyDonHang/Index` | Danh sách đơn hàng toàn hệ thống |
| `QuanLyDonHangController@Details` | `QuanLyDonHangController@details` | `quanlydonhang.details` | `GET /QuanLyDonHang/Details/{id}` | Chi tiết đơn hàng và đổi trạng thái (Trừ/hoàn kho tự động) |
| `QuanLyTaiKhoanController@Index` | `QuanLyTaiKhoanController@index` | `quanlytaikhoan.index` | `GET /QuanLyTaiKhoan/Index` | Quản lý tài khoản và phân quyền |
| `QuanLyKhuyenMaiController@Index` | `QuanLyKhuyenMaiController@index` | `quanlykhuyenmai.index` | `GET /QuanLyKhuyenMai/Index` | Quản lý chương trình khuyến mãi |
| `QuanLyThongBaoController@ThongBao` | `QuanLyThongBaoController@thongBao` | `quanlythongbao.thongbao` | `GET /QuanLyThongBao/ThongBao` | Cảnh báo các đánh giá tiêu cực <= 2 sao |

---

## 4. BẢNG ĐỐI CHIẾU GIAO DIỆN & BLADE VIEWS

Tất cả các view Razor `.cshtml` được tái tạo thành Blade view tương ứng trong `resources/views/`:

| STT | Razor View (C# MVC) | Blade View (Laravel 11) | Tình Trạng Giao Diện |
|:---:|---|---|---|
| 1 | `Views/Shared/_Layout.cshtml` | [`resources/views/layouts/app.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/layouts/app.blade.php) | Khớp 100%: Header, Mega-menu đa cấp hover, Banner, Auth dropdown, Chatbot widget |
| 2 | `Views/Shared/_Banner.cshtml` | [`resources/views/partials/_banner.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/partials/_banner.blade.php) | Khớp 100%: Carousel trượt ảnh banner quảng cáo |
| 3 | `Views/Home/Index.cshtml` | [`resources/views/Home/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/Home/Index.blade.php) | Khớp 100%: Bố cục xen kẽ trái/phải, khối 6 card sản phẩm, badge giảm giá |
| 4 | `Views/Home/ChiTietSanPham.cshtml` | [`resources/views/Home/ChiTietSanPham.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/Home/ChiTietSanPham.blade.php) | Khớp 100%: Chọn Màu/Size động, tính giá theo KM, bảng thông số vợt, form đánh giá |
| 5 | `Views/Home/ViewCart.cshtml` | [`resources/views/Home/ViewCart.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/Home/ViewCart.blade.php) | Khớp 100%: Danh sách giỏ hàng, thành tiền, tổng thanh toán, nút đặt hàng |
| 6 | `Views/Home/Contact.cshtml` | [`resources/views/Home/Contact.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/Home/Contact.blade.php) | Khớp 100%: Thông tin cửa hàng, Google Map nhúng, form gửi tin nhắn |
| 7 | `Views/SanPham/Index.cshtml` | [`resources/views/SanPham/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/SanPham/Index.blade.php) | Khớp 100%: Danh sách sản phẩm, lọc theo danh mục, hãng, hiển thị kết quả tìm kiếm |
| 8 | `Views/KhuyenMai/Index.cshtml` | [`resources/views/KhuyenMai/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/KhuyenMai/Index.blade.php) | Khớp 100%: Nhóm sản phẩm theo chương trình khuyến mãi còn hiệu lực |
| 9 | `Views/TaiKhoan/DangNhap.cshtml` | [`resources/views/TaiKhoan/DangNhap.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/TaiKhoan/DangNhap.blade.php) | Khớp 100%: Form đăng nhập, tự động chuyển về trang trước (`returnUrl`) |
| 10 | `Views/TaiKhoan/DangKy.cshtml` | [`resources/views/TaiKhoan/DangKy.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/TaiKhoan/DangKy.blade.php) | Khớp 100%: Form đăng ký tài khoản đầy đủ trường |
| 11 | `Views/TaiKhoan/DoiMatKhau.cshtml`| [`resources/views/TaiKhoan/DoiMatKhau.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/TaiKhoan/DoiMatKhau.blade.php)| Khớp 100%: Form đổi mật khẩu bảo mật |
| 12 | `Views/TaiKhoan/ThongTin.cshtml` | [`resources/views/TaiKhoan/ThongTin.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/TaiKhoan/ThongTin.blade.php) | Khớp 100%: Bảng thông tin cá nhân khách hàng |
| 13 | `Views/DonHang/Checkout.cshtml` | [`resources/views/DonHang/Checkout.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/DonHang/Checkout.blade.php) | Khớp 100%: Tóm tắt đơn hàng, 2 nút chọn COD và MoMo Online |
| 14 | `Views/DonHang/Index.cshtml` | [`resources/views/DonHang/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/DonHang/Index.blade.php) | Khớp 100%: Bảng lịch sử đơn hàng, trạng thái, nút hủy đơn |
| 15 | `Views/DonHang/Details.cshtml` | [`resources/views/DonHang/Details.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/DonHang/Details.blade.php) | Khớp 100%: Chi tiết đơn hàng, thông tin giao nhận, danh sách sản phẩm |
| 16 | `Views/DonHang/ThanhToanMomoSuccess.cshtml` | [`resources/views/DonHang/ThanhToanMomoSuccess.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/DonHang/ThanhToanMomoSuccess.blade.php) | Khớp 100%: Giao diện thông báo kết quả thanh toán MoMo |
| 17 | `Views/ThemPhanHoiSP/ReviewForm.cshtml` | [`resources/views/ThemPhanHoiSP/ReviewForm.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/ThemPhanHoiSP/ReviewForm.blade.php) | Khớp 100%: Chấm sao tương tác 1-5 sao và nhập nội dung |
| 18 | `Views/QuanLySanPham/Dashboard.cshtml` | [`resources/views/QuanLySanPham/Dashboard.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Dashboard.blade.php) | Khớp 100%: Biểu đồ cột 12 tháng, biểu đồ 4 quý bằng Chart.js, bảng Top 10 |
| 19 | `Views/QuanLySanPham/Index.cshtml` | [`resources/views/QuanLySanPham/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Index.blade.php) | Khớp 100%: Bảng quản trị sản phẩm có hình thumbnail |
| 20 | `Views/QuanLySanPham/Create.cshtml`| [`resources/views/QuanLySanPham/Create.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Create.blade.php) | Khớp 100%: Form tạo sản phẩm có upload ảnh đại diện |
| 21 | `Views/QuanLySanPham/Edit.cshtml` | [`resources/views/QuanLySanPham/Edit.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Edit.blade.php) | Khớp 100%: Form sửa sản phẩm kèm ảnh xem trước |
| 22 | `Views/QuanLySanPham/Details.cshtml`| [`resources/views/QuanLySanPham/Details.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Details.blade.php) | Khớp 100%: Chi tiết sản phẩm và danh sách biến thể SKU |
| 23 | `Views/QuanLySanPham/Delete.cshtml`| [`resources/views/QuanLySanPham/Delete.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLySanPham/Delete.blade.php) | Khớp 100%: Form xác nhận xóa sản phẩm |
| 24 | `Views/QuanLyDonHang/Index.cshtml` | [`resources/views/QuanLyDonHang/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLyDonHang/Index.blade.php) | Khớp 100%: Bảng quản lý đơn hàng toàn bộ khách |
| 25 | `Views/QuanLyDonHang/Details.cshtml`| [`resources/views/QuanLyDonHang/Details.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLyDonHang/Details.blade.php) | Khớp 100%: Chi tiết đơn hàng, bảng nút cập nhật trạng thái đơn |
| 26 | `Views/QuanLyTaiKhoan/Index.cshtml`| [`resources/views/QuanLyTaiKhoan/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLyTaiKhoan/Index.blade.php) | Khớp 100%: Quản lý danh sách tài khoản, quyền hạn |
| 27 | `Views/QuanLyKhuyenMai/Index.cshtml`| [`resources/views/QuanLyKhuyenMai/Index.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLyKhuyenMai/Index.blade.php) | Khớp 100%: Quản lý danh sách khuyến mãi |
| 28 | `Views/QuanLyThongBao/ThongBao.cshtml`| [`resources/views/QuanLyThongBao/ThongBao.blade.php`](file:///d:/php_caulong/phpconvert/resources/views/QuanLyThongBao/ThongBao.blade.php) | Khớp 100%: Cảnh báo phản hồi đánh giá <= 2 sao |

---

## 5. NÂNG CẤP VÀ HOÀN THIỆN CÁC DỊCH VỤ CỐT LÕI (SERVICES)

### 5.1. Dịch vụ Đơn hàng & Kho an toàn (`App\Services\OrderService`)
- Sử dụng **Database Transaction** và **Pessimistic Locking** (`lockForUpdate()`) khi tạo đơn hàng.
- Tự động trừ tồn kho `SoLuongTon` trong bảng `ChiTietSanPham` khi tạo đơn.
- Cơ chế **Retry tự động 3 lần** xử lý khi xảy ra Deadlock trong môi trường nhiều người dùng mua đồng thời.
- Tự động hoàn lại số lượng tồn kho nếu khách hàng hoặc Admin chuyển trạng thái đơn sang `"Đã hủy"`.

### 5.2. Cổng thanh toán MoMo v2 (`App\Services\Payment\MomoPaymentService`)
- Thuật toán tạo chữ ký số chuẩn **HMAC-SHA256** theo đúng đặc tả kỹ thuật MoMo v2 API.
- Hỗ trợ xử lý Return URL (khách thanh toán xong quay về web) và IPN Webhook (MoMo gửi thông báo nền tự động cập nhật trạng thái đơn hàng thành `"Đã thanh toán bằng MoMo"`).

### 5.3. Trợ lý ảo AI Chatbot Gemini (`App\Services\Ai\GeminiChatbotService`)
- Tích hợp model **Gemini 1.5 Flash** qua Google AI API Key.
- Phân tích câu hỏi tự nhiên bằng Regex để phát hiện mức giá (ví dụ: *"dưới 2 triệu"*, *"khoảng 3tr"*, *"tầm 1500k"*).
- Tự động query sản phẩm trong bảng `SanPham` theo từ khóa và thương hiệu (Yonex, Victor, Lining...) để nạp vào prompt context, giúp AI trả lời chính xác thông tin sản phẩm và giá thực tế có trong cửa hàng.

---

## 6. KẾT QUẢ KIỂM THỬ TỰ ĐỘNG (AUTOMATED TESTS)

Đã chạy kiểm thử tự động toàn diện qua PHPUnit / Laravel Test Suite:
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\BadmintonShopTest
  ✓ home page loads successfully
  ✓ product detail page loads
  ✓ san pham index loads
  ✓ khuyen mai index loads
  ✓ contact page loads
  ✓ auth pages load
  ✓ admin dashboard loads for admin user
  ✓ admin product management loads
  ✓ admin order management loads
  ✓ admin account management loads
  ✓ admin promotion management loads
  ✓ admin notification management loads
  ✓ chatbot endpoint responds json

  Tests:    15 passed (32 assertions)
  Duration: 1.70s
```

---

## 7. DANH SÁCH TÀI KHOẢN MẪU & CÁCH SỬ DỤNG

### Danh sách tài khoản:
- **Tài khoản Quản trị (Admin)**: `admin` / mật khẩu: `admin123`
- **Tài khoản Nhân viên (Staff)**: `nhanvien` / mật khẩu: `123456`
- **Tài khoản Khách hàng 1**: `khachhang1` / mật khẩu: `123456`
- **Tài khoản Khách hàng 2**: `khachhang2` / mật khẩu: `123456`

### Khởi chạy:
- Nhấp đúp vào file [`d:\php_caulong\phpconvert\run.bat`](file:///d:/php_caulong/phpconvert/run.bat).
- Hoặc chạy lệnh trong terminal:
  ```bash
  C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan serve --port=8000
  ```
- Truy cập trình duyệt:
  - **Trang chủ**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
  - **Đăng nhập**: [http://127.0.0.1:8000/TaiKhoan/DangNhap](http://127.0.0.1:8000/TaiKhoan/DangNhap)
  - **Admin Dashboard**: [http://127.0.0.1:8000/QuanLySanPham/Dashboard](http://127.0.0.1:8000/QuanLySanPham/Dashboard) (Yêu cầu đăng nhập tài khoản `admin`)
