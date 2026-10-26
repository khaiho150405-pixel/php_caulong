# WEBSITE BÁN DỤNG CỤ CẦU LÔNG ONLINE — LARAVEL

Đồ án cuối môn **Lập trình mã nguồn mở** (Laravel Framework) — Đề tài gợi ý số 3: *Website Bán hàng Online*.
Hệ thống được chuyển đổi từ dự án ASP.NET MVC 5 (C#) sang **PHP Laravel 13**, giữ nguyên tên bảng của CSDL gốc `QLDN_CauLong`.

---

## 1. Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| Backend | PHP ≥ 8.3, **Laravel 13** (Eloquent ORM, Migration, Seeder/Factory, Form Request, Middleware, Storage) |
| Cơ sở dữ liệu | **MySQL / MariaDB** (Laragon) |
| Giao diện | Blade Template + **Bootstrap 5**, jQuery, Font Awesome, Chart.js |
| Tích hợp ngoài | Cổng thanh toán **MoMo v2** (sandbox), **Google Gemini API** (chatbot tư vấn) |
| Kiểm thử | PHPUnit (Feature Test) |

---

## 2. Vai trò người dùng & tài khoản mẫu

| Tên đăng nhập | Mật khẩu | Vai trò | Quyền hạn |
|---|---|---|---|
| `admin` | `admin@123` | Quản trị viên | Toàn quyền khu vực `/admin`: sản phẩm, biến thể, đơn hàng, **tài khoản**, **khuyến mãi**, thống kê |
| `nhanvien_banhang` | `nv@123` | Nhân viên | `/admin`: sản phẩm, biến thể, đơn hàng, thống kê, cảnh báo đánh giá (không quản lý tài khoản/khuyến mãi) |
| `nguyenvana` | `vana@123` | Khách hàng | Giỏ hàng, đặt hàng COD/MoMo, lịch sử & hủy đơn, đánh giá sản phẩm |
| `tranthib` | `thib@123` | Khách hàng | Như trên |

Khách vãng lai (chưa đăng nhập) được xem, tìm kiếm sản phẩm, xem khuyến mãi; có thể đăng ký tài khoản mới.

Phân quyền dùng **Laravel Auth** + Middleware tùy chỉnh `role` (`app/Http/Middleware/CheckRole.php`):

```php
Route::prefix('admin')->middleware('role:admin,nhanvien')->group(...);   // Admin + Nhân viên
Route::middleware('role:admin')->group(...);                             // chỉ Admin
Route::middleware('check.login')->group(...);                            // người dùng đã đăng nhập
```

---

## 3. Hướng dẫn cài đặt & chạy trên máy khác

### Yêu cầu
- PHP ≥ 8.3 (bật các extension `pdo_mysql`, `mbstring`, `fileinfo`, `gd`; `pdo_sqlite` nếu muốn chạy test)
- Composer 2.x
- MySQL / MariaDB (khuyến nghị dùng **Laragon**)

### Các bước

```bash
# 1. Vào thư mục dự án
cd phpconvert

# 2. Cài thư viện PHP
composer install

# 3. Tạo file cấu hình môi trường và khóa ứng dụng
copy .env.example .env        # Windows  (Linux/macOS: cp .env.example .env)
php artisan key:generate

# 4. Tạo database rỗng tên "phpconvert" (utf8mb4_unicode_ci) trong MySQL
#    (Laragon > Database, hoặc: mysql -u root -e "CREATE DATABASE phpconvert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
#    Sửa DB_USERNAME / DB_PASSWORD trong .env nếu MySQL có mật khẩu.

# 5. Tạo bảng (Migration) + dữ liệu mẫu (Seeder)
php artisan migrate:fresh --seed

# 6. Chạy server
php artisan serve --port=8000      # hoặc chạy file run.bat
```

> Tùy chọn: điền `GEMINI_API_KEY` trong `.env` để chatbot trả lời bằng AI. Nếu để trống, chatbot vẫn hoạt động bằng dữ liệu tra cứu trong kho.

### Địa chỉ truy cập
| Trang | URL |
|---|---|
| Trang chủ (landing page) | http://127.0.0.1:8000 |
| Trang cửa hàng (danh mục + sản phẩm) | http://127.0.0.1:8000/Home/Index |
| Đăng nhập | http://127.0.0.1:8000/TaiKhoan/DangNhap |
| **Khu vực quản trị** | http://127.0.0.1:8000/admin |

### Chạy kiểm thử tự động
```bash
php artisan test
```
Test dùng SQLite in-memory (cấu hình trong `phpunit.xml`) nên **không ảnh hưởng dữ liệu MySQL**. Nếu báo thiếu driver, bật `extension=pdo_sqlite` trong `php.ini` (Laragon > PHP > php.ini).

---

## 4. Đối chiếu yêu cầu đồ án

| Yêu cầu | Cách đáp ứng trong dự án |
|---|---|
| Migration | `database/migrations/` — 18 bảng nghiệp vụ + cột `deleted_at` (Soft Delete) |
| Seeder / Factory | `BadmintonSeeder`, `DonHangMauSeeder`; `SanPhamFactory`, `KhuyenMaiFactory`, `TaiKhoanFactory` |
| Tách giao diện người dùng / quản trị | `layouts/app.blade.php` (khách hàng) và `layouts/admin.blade.php` (khu vực `/admin`) |
| ≥ 2 role + Middleware | Admin, Nhân viên, Khách hàng — middleware `role:` và `check.login` |
| CRUD ≥ 2 bảng có quan hệ | **SanPham** (belongsTo LoaiSanPham/Hang/NhaCungCap/KhuyenMai) — **ChiTietSanPham** (SanPham hasMany biến thể) — **KhuyenMai** (hasMany SanPham) — **TaiKhoan** (hasOne KhachHang) |
| Tìm kiếm + phân trang (giữ từ khóa) | Trang `/SanPham/TimKiem`, `/SanPham/Index`; admin: sản phẩm, đơn hàng, tài khoản, khuyến mãi — dùng `paginate()->withQueryString()` |
| Validation + thông báo lỗi rõ ràng | Form Request trong `app/Http/Requests/` + thông báo tiếng Việt `lang/vi/validation.php` |
| Flash message sau thêm/sửa/xóa | `session('message')` / `session('error')` hiển thị ở 2 layout |
| Mở rộng: Upload file | Ảnh sản phẩm/biến thể lưu qua Laravel Storage (disk `images` → `public/images/uploads`) |
| Mở rộng: AJAX | Chatbot, đếm số lượng giỏ hàng |
| Mở rộng: API bên ngoài | MoMo Payment v2 (xác thực chữ ký HMAC-SHA256), Google Gemini |
| Mở rộng: Soft Delete, Role Middleware tùy chỉnh, Responsive | `SanPham` dùng `SoftDeletes`; `CheckRole`; Bootstrap 5 + CSS media query |

---

## 5. Chức năng chính

**Khách hàng:** trang chủ & mega-menu theo loại/hãng; tìm kiếm có phân trang; chi tiết sản phẩm (chọn màu/size, giá khuyến mãi, tồn kho, thông số vợt); giỏ hàng; đặt hàng COD hoặc MoMo; lịch sử & hủy đơn (tự hoàn kho); đánh giá 1–5 sao; chatbot AI.

**Trang chủ / Landing page (`/`):** mặc định khi vào website. Do Laravel render (route `home` → `LandingController` → view `Landing/Index.blade.php`), mọi nội dung đều lấy từ CSDL qua Eloquent: ảnh sản phẩm thật ở hero, số liệu cửa hàng, số sản phẩm theo từng mức ngân sách (dùng lại bộ lọc giá), danh mục, sản phẩm đang giảm giá nhiều nhất, đánh giá tích cực gần đây. Thiết kế nhẹ: không thư viện cuộn/3D, chỉ CSS và một IntersectionObserver; ảnh dùng WebP đã nén (`public/images/landing/`, `public/images/thumbs/`). Đường dẫn cũ `/Landing` tự chuyển về `/`.

**Bảng tin cầu lông (`/BangTin`):** tin tức về môn cầu lông gồm 5 loại: sản phẩm hot, sản phẩm mới, trận đấu, tuyển thủ, giải đấu. Giao diện kiểu bảng tin: đầu trang "BẢNG TIN" kèm số liệu, dải tin nhanh chạy ngang, thanh chuyên mục có số lượng tin, dòng thời gian nhóm theo tháng (ngày đăng ở bên trái, màu theo loại tin), bảng điểm hiển thị tỉ số từng ván. Có tìm kiếm + lọc theo loại + phân trang (giữ từ khóa khi chuyển trang), tin nổi bật, tăng lượt xem (mỗi phiên một lần), tin hẹn giờ đăng, cột bên "Bảng điểm", "Sản phẩm đang hot" (xếp hạng 1-4) (bán chạy nhất theo đơn hàng, tính từ `ChiTietDonHang`) và "Hàng mới về". Mỗi bài đều có nguồn tham khảo; dữ liệu mẫu là kết quả thật của Olympic Paris 2024 và giải vô địch thế giới 2025, 2026, đã đối chiếu từ nhiều nguồn công khai. Bài có thể gắn với một sản phẩm (hiện thẻ dẫn sang trang mua hàng) hoặc một trận đấu (hiện thẻ tỉ số). Quản trị (Admin và Nhân viên): `/admin/ban-tin` (CRUD bài viết, xóa mềm, hẹn giờ) và `/admin/tran-dau` (CRUD kết quả trận đấu), có Form Request validation (tỉ số phải đúng dạng `21-11, 21-11`, hai bên phải khác nhau, có tỉ số thì phải chọn bên thắng...) và thông báo flash. Dữ liệu: bảng `TinTuc` (khóa ngoại `SanPham`, `TranDau`, `TaiKhoan`) và `TranDau`; seeder `BanTinSeeder` (chạy lại được nhiều lần). CSDL đã có từ trước thì chạy: `php artisan migrate` rồi `php artisan db:seed --class=BanTinSeeder`.

**Khuyến mãi (`/KhuyenMai/Index`):** mỗi chương trình là một khối riêng (mức giảm, giảm tối đa, thời hạn, số ngày còn lại, lưới sản phẩm đã tính giá sau khuyến mãi), có thanh chuyển nhanh giữa các chương trình.

**Phụ kiện cầu lông:** dữ liệu mẫu trong `database/seeders/PhuKienSeeder.php` (cầu lông, dây cuốn cán...), kèm ảnh thật. Nếu CSDL đã seed từ trước, chạy thêm: `php artisan db:seed --class=PhuKienSeeder` (chạy lại nhiều lần không bị trùng).

**Quản trị (`/admin`):** dashboard doanh thu theo tháng/quý/năm + top 10 bán chạy; CRUD sản phẩm (upload ảnh, xóa mềm) và biến thể (màu/size/giá/tồn kho/SKU); quản lý đơn hàng (chuyển trạng thái hợp lệ, hủy đơn hoàn kho); CRUD tài khoản (chỉ Admin, không thể tự xóa/tự hạ quyền); CRUD khuyến mãi (chỉ Admin); cảnh báo đánh giá ≤ 2 sao.

---

## 6. Cấu trúc thư mục chính

```
app/Http/Controllers/     Controller khu vực khách hàng + QuanLy* (quản trị)
app/Http/Middleware/      CheckRole (phân quyền), CheckLogin
app/Http/Requests/        Form Request validation (Admin/, Auth/, Checkout, PhanHoi)
app/Models/               18 Model Eloquent
app/Services/             OrderService (đặt/hủy đơn, khóa tồn kho), MoMo, Gemini
database/migrations/      Cấu trúc bảng
database/seeders/         Dữ liệu mẫu
database/factories/       Factory
resources/views/          Blade views (layouts/, Landing/, KhuyenMai/, BangTin/, QuanLy*/ cho admin)
public/js/landing/        landing.js (hiện dần khi cuộn, chỉ ~1 KB)
public/images/landing/    ảnh WebP đã nén cho landing; thumbs/ = ảnh sản phẩm thu nhỏ; brands/ = logo hãng
routes/web.php            Định tuyến (nhóm /admin)
tests/Feature/            Kiểm thử chức năng
```

---

## 7. Thư viện và nguồn ảnh bên thứ ba

- **Logo hãng** (`public/images/brands/*.svg`) lấy từ Wikimedia Commons (Logo-Yonex.svg, Li-Ning logo red.svg, VICTOR Badminton logo.svg, MIZUNO logo.svg). Logo vẫn là nhãn hiệu của các hãng, chỉ dùng cho mục đích học tập.
- **Ảnh phụ kiện** (`public/images/pk_*.jpg`) lấy từ Wikimedia Commons, ghi công đầy đủ trong `public/images/CREDITS.txt`: Stanislas Perrin (Public domain), Tim Reckmann (CC BY 2.0, 2 ảnh), 14harryl (CC BY 3.0).
- **Nội dung bảng tin:** kết quả và số liệu trận đấu tham khảo từ các trang công khai (Wikipedia, Badminton Planet, Badminton Asia, ESPN, CGTN, Korea JoongAng Daily, Outlook India); đường dẫn cụ thể nằm trong cột `LienKetNguon` của từng bài/trận trong CSDL và hiển thị ở cuối mỗi bài. Nội dung bài do nhóm tự diễn đạt lại, không sao chép nguyên văn; bảng tin không dùng ảnh của các trang này.
- Ảnh vợt, giày, trang phục là ảnh sản phẩm có sẵn của dự án; các bản thu nhỏ trong `public/images/thumbs/` được tạo từ chính các ảnh đó.
