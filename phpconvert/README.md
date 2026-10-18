# DỰ ÁN SHOP BÁN CẦU LÔNG ONLINE (PHP LARAVEL 11)
> Dự án được chuyển đổi toàn diện từ **ASP.NET MVC 5 (C#)** sang **PHP Laravel 11**, giữ nguyên 100% cấu trúc tên bảng cơ sở dữ liệu (`QLDN_CauLong`) và bảo toàn toàn bộ nghiệp vụ gốc, tích hợp cổng thanh toán MoMo v2 và Trợ lý ảo AI Gemini.

---

## 1. Yêu Cầu Môi Trường
- **PHP**: >= 8.2 (đã kiểm thử và tối ưu trên PHP 8.3 / Laragon)
- **MySQL / MariaDB**: Cổng 3306 (Database name: `phpconvert`)
- **Composer**: Quản lý thư viện PHP

---

## 2. Cấu Trúc Cơ Sở Dữ Liệu (Giữ Nguyên Tên Bảng Gốc)
Tất cả 18 bảng trong SQL Server cũ được giữ nguyên tên và kiểu dữ liệu chuẩn MySQL:
1. `PhanQuyen`: Phân quyền người dùng (Admin, Nhân viên, Khách hàng)
2. `TaiKhoan`: Tài khoản người dùng (mật khẩu Bcrypt + hỗ trợ tài khoản cũ)
3. `KhachHang`: Thông tin khách hàng liên kết với `TaiKhoan`
4. `LoaiSanPham`: Phân loại vợt, giày, áo quần, phụ kiện
5. `Hang`: Hãng sản xuất (Yonex, Victor, Lining...)
6. `NhaCungCap`: Nhà cung cấp thiết bị
7. `KhuyenMai`: Chương trình giảm giá (% giảm, trần tối đa, ngày áp dụng)
8. `MauSac`: Bảng màu biến thể
9. `Size`: Bảng kích thước / phiên bản
10. `SanPham`: Bảng sản phẩm chính
11. `ChiTietSanPham`: Biến thể chi tiết (Màu, Size, Giá bán, Tồn kho)
12. `ThongSoVot`: Thông số kỹ thuật vợt (Độ cứng, Điểm cân bằng, Sức căng, Trọng lượng)
13. `DonHang`: Quản lý đơn hàng và trạng thái giao hàng
14. `ChiTietDonHang`: Chi tiết từng dòng sản phẩm trong đơn
15. `PhanHoi`: Đánh giá 1-5 sao và phản hồi của khách hàng
16. `GioHang`: Giỏ hàng lưu trữ theo tài khoản khách hàng
17. `LichSuThayDoiGia`: Nhật ký biến động giá
18. `LogLoiGiaoTac`: Nhật ký lỗi giao tác hệ thống

---

## 3. Tài Khoản Mặc Định

| Tên Đăng Nhập | Mật Khẩu | Vai Trò | Quyền Hạn |
|---|---|---|---|
| `admin` | `admin123` | **Quản Trị Viên (Admin)** | Toàn quyền hệ thống, Quản lý sản phẩm, tài khoản, khuyến mãi, đơn hàng, thống kê dashboard |
| `nhanvien` | `123456` | **Nhân Viên (Staff)** | Quản lý sản phẩm, đơn hàng, thông báo cảnh báo |
| `khachhang1` | `123456` | **Khách Hàng (Customer)** | Đặt hàng, giỏ hàng, thanh toán COD / MoMo, viết đánh giá |
| `khachhang2` | `123456` | **Khách Hàng (Customer)** | Đặt hàng, xem lịch sử đơn hàng |

---

## 4. Các Chức Năng Chính Đã Hoàn Thiện

### Phía Khách Hàng (Client):
- **Trang chủ & Menu đa cấp**: Banner slider, sản phẩm mới, lọc theo danh mục, hãng và tìm kiếm từ khóa.
- **Chi tiết sản phẩm**: Chọn Màu sắc / Size linh hoạt, cập nhật giá động theo khuyến mãi, kiểm tra tồn kho trực tiếp, hiển thị bảng thông số vợt (độ cứng, điểm cân bằng, sức căng, trọng lượng).
- **Giỏ hàng & Đặt hàng**: Lưu giỏ hàng theo tài khoản, tính tổng tiền, tùy chọn hình thức thanh toán COD hoặc qua MoMo.
- **Cổng thanh toán MoMo v2 API**: Tạo giao dịch MoMo trực tuyến với chữ ký HMAC-SHA256, chuyển hướng thanh toán và xử lý kết quả trả về (IPN / Return URL).
- **Đánh giá & Phản hồi**: Khách hàng đã đăng nhập có thể chấm sao (1-5 sao) và để lại nhận xét.
- **AI Chatbot Gemini**: Widget chat nổi góc màn hình hỗ trợ tra cứu sản phẩm thông minh, gợi ý theo tầm giá và thương hiệu.

### Phía Quản Trị (Admin):
- **Dashboard Thống kê Doanh thu**: Biểu đồ doanh thu 12 tháng, 4 quý trong năm bằng Chart.js, bảng xếp hạng Top 10 sản phẩm bán chạy nhất.
- **Quản lý Sản phẩm**: Thêm mới kèm upload hình ảnh đại diện, chỉnh sửa, xem chi tiết các biến thể, xóa sản phẩm.
- **Quản lý Đơn hàng**: Xem danh sách đơn hàng, chi tiết đơn, chuyển đổi trạng thái (Chờ xác nhận -> Đang xử lý -> Đang giao hàng -> Đã hoàn thành hoặc Hủy đơn), tự động trừ kho và hoàn kho an toàn chống deadlock.
- **Quản lý Tài khoản**: Thêm tài khoản mới tự động đồng bộ sang bảng `KhachHang`, phân quyền Admin / Nhân viên / Khách hàng, đổi mật khẩu.
- **Quản lý Khuyến mãi**: Thiết lập chương trình giảm giá (% và hạn mức giảm), tự động áp dụng khi khách mua hàng.
- **Cảnh báo Đánh giá thấp**: Tự động lọc các phản hồi <= 2 sao để đội ngũ CSKH xử lý khiếu nại kịp thời.

---

## 5. Hướng Dẫn Chạy Dự Án

### Bước 1: Khởi động MySQL trong Laragon
Mở ứng dụng Laragon và nhấn **Start All** (đảm bảo dịch vụ MySQL đang chạy trên cổng 3306).

### Bước 2: Chạy Web Server
Mở Terminal hoặc Command Prompt tại thư mục `d:\php_caulong\phpconvert` và thực hiện lệnh:
```bash
C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan serve --port=8000
```
*(Nếu PHP đã có trong biến môi trường PATH của Windows, bạn chỉ cần gõ `php artisan serve`)*

### Bước 3: Truy Cập Trình Duyệt
- **Trang chủ khách hàng**: [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Đăng nhập**: [http://127.0.0.1:8000/TaiKhoan/DangNhap](http://127.0.0.1:8000/TaiKhoan/DangNhap)
- **Trang Quản trị Admin**: [http://127.0.0.1:8000/QuanLySanPham/Dashboard](http://127.0.0.1:8000/QuanLySanPham/Dashboard) (Yêu cầu đăng nhập tài khoản `admin`)
