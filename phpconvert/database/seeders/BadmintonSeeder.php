<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class BadmintonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để seed dữ liệu ban đầu
        Schema::disableForeignKeyConstraints();

        DB::table('PhanHoi')->truncate();
        DB::table('ChiTietDonHang')->truncate();
        DB::table('DonHang')->truncate();
        DB::table('GioHang')->truncate();
        DB::table('ThongSoVot')->truncate();
        DB::table('ChiTietSanPham')->truncate();
        DB::table('SanPham')->truncate();
        DB::table('KhachHang')->truncate();
        DB::table('TaiKhoan')->truncate();
        DB::table('PhanQuyen')->truncate();
        DB::table('Size')->truncate();
        DB::table('MauSac')->truncate();
        DB::table('KhuyenMai')->truncate();
        DB::table('Hang')->truncate();
        DB::table('NhaCungCap')->truncate();
        DB::table('LoaiSanPham')->truncate();

        // 1. PhanQuyen
        DB::table('PhanQuyen')->insert([
            ['MaQuyen' => 1, 'TenQuyen' => 'Quản trị'],
            ['MaQuyen' => 2, 'TenQuyen' => 'Nhân viên'],
            ['MaQuyen' => 3, 'TenQuyen' => 'Khách hàng'],
        ]);

        // 2. LoaiSanPham
        DB::table('LoaiSanPham')->insert([
            ['MaLoai' => 1, 'TenLoai' => 'Vợt cầu lông', 'MoTa' => 'Dành cho người chơi chuyên nghiệp và phong trào'],
            ['MaLoai' => 2, 'TenLoai' => 'Giày cầu lông', 'MoTa' => 'Giày thi đấu và tập luyện'],
            ['MaLoai' => 3, 'TenLoai' => 'Áo quần thể thao', 'MoTa' => 'Trang phục cầu lông'],
            ['MaLoai' => 4, 'TenLoai' => 'Phụ kiện cầu lông', 'MoTa' => 'Túi, băng tay, quấn cán...'],
        ]);

        // 3. NhaCungCap
        DB::table('NhaCungCap')->insert([
            ['MaNhaCungCap' => 1, 'TenNhaCungCap' => 'Công ty TNHH Thể thao Đức Phát', 'DiaChi' => '25 Hoàng Hoa Thám, Q.Tân Bình, TP.HCM', 'SoDienThoai' => '0905123456', 'Email' => 'sales@ducphat.vn'],
            ['MaNhaCungCap' => 2, 'TenNhaCungCap' => 'Công ty TNHH Cầu Lông Sunrise Việt Nam', 'DiaChi' => '22 Nguyễn Văn Trỗi, Q.Phú Nhuận, TP.HCM', 'SoDienThoai' => '0908789123', 'Email' => 'info@sunrisevn.com'],
            ['MaNhaCungCap' => 3, 'TenNhaCungCap' => 'Công ty TNHH Thể thao Đại Hưng', 'DiaChi' => '19 Nguyễn Hữu Cảnh, Q.Bình Thạnh, TP.HCM', 'SoDienThoai' => '0903332121', 'Email' => 'contact@daihung.vn'],
            ['MaNhaCungCap' => 4, 'TenNhaCungCap' => 'Công ty TNHH SportPro Việt Nam', 'DiaChi' => '88 Trần Hưng Đạo, Q.1, TP.HCM', 'SoDienThoai' => '0918234567', 'Email' => 'support@sportpro.vn'],
            ['MaNhaCungCap' => 5, 'TenNhaCungCap' => 'Công ty TNHH Thể thao Phúc Gia', 'DiaChi' => '12 Cầu Giấy, Hà Nội', 'SoDienThoai' => '0934556677', 'Email' => 'info@phucgiasport.vn'],
        ]);

        // 4. Hang
        DB::table('Hang')->insert([
            ['MaHang' => 1, 'TenHang' => 'Yonex', 'QuocGia' => 'Nhật Bản'],
            ['MaHang' => 2, 'TenHang' => 'Lining', 'QuocGia' => 'Trung Quốc'],
            ['MaHang' => 3, 'TenHang' => 'Victor', 'QuocGia' => 'Đài Loan'],
            ['MaHang' => 4, 'TenHang' => 'Mizuno', 'QuocGia' => 'Nhật Bản'],
        ]);

        // 5. KhuyenMai
        DB::table('KhuyenMai')->insert([
            ['MaKhuyenMai' => 1, 'TenChuongTrinh' => 'Đại lễ 30/4 - 1/5', 'MoTa' => 'Mừng ngày giải phóng miền Nam và Quốc tế lao động', 'NgayBatDau' => '2025-04-25', 'NgayKetThuc' => '2027-05-05', 'PhanTramGiam' => 10, 'GiamToiDa' => 150000, 'SoLuongSuDung' => 200],
            ['MaKhuyenMai' => 2, 'TenChuongTrinh' => 'Chào Hè Sôi Động', 'MoTa' => 'Giảm giá dụng cụ thể thao mùa hè', 'NgayBatDau' => '2025-06-01', 'NgayKetThuc' => '2027-06-30', 'PhanTramGiam' => 12, 'GiamToiDa' => 200000, 'SoLuongSuDung' => 150],
            ['MaKhuyenMai' => 3, 'TenChuongTrinh' => 'Back To School', 'MoTa' => 'Đồng hành cùng học sinh sinh viên', 'NgayBatDau' => '2025-08-15', 'NgayKetThuc' => '2027-09-10', 'PhanTramGiam' => 10, 'GiamToiDa' => 100000, 'SoLuongSuDung' => 300],
            ['MaKhuyenMai' => 4, 'TenChuongTrinh' => 'Siêu Sale 9.9', 'MoTa' => 'Săn sale ngày đôi tháng 9', 'NgayBatDau' => '2025-09-09', 'NgayKetThuc' => '2027-09-11', 'PhanTramGiam' => 25, 'GiamToiDa' => 500000, 'SoLuongSuDung' => 50],
            ['MaKhuyenMai' => 5, 'TenChuongTrinh' => 'Black Friday 2026', 'MoTa' => 'Lễ hội mua sắm lớn nhất năm', 'NgayBatDau' => '2025-11-28', 'NgayKetThuc' => '2027-11-30', 'PhanTramGiam' => 40, 'GiamToiDa' => 1000000, 'SoLuongSuDung' => 100],
            ['MaKhuyenMai' => 6, 'TenChuongTrinh' => 'Giáng Sinh An Lành', 'MoTa' => 'Quà tặng giáng sinh cho lông thủ', 'NgayBatDau' => '2025-11-20', 'NgayKetThuc' => '2027-12-25', 'PhanTramGiam' => 15, 'GiamToiDa' => 200000, 'SoLuongSuDung' => 100],
            ['MaKhuyenMai' => 7, 'TenChuongTrinh' => 'Xả kho cuối năm', 'MoTa' => 'Thanh lý các mẫu vợt đời cũ', 'NgayBatDau' => '2025-12-26', 'NgayKetThuc' => '2027-12-31', 'PhanTramGiam' => 50, 'GiamToiDa' => null, 'SoLuongSuDung' => 20],
            ['MaKhuyenMai' => 8, 'TenChuongTrinh' => 'Khách hàng thân thiết', 'MoTa' => 'Tri ân khách hàng đã mua trên 5 triệu', 'NgayBatDau' => '2025-01-01', 'NgayKetThuc' => '2027-12-31', 'PhanTramGiam' => 5, 'GiamToiDa' => 500000, 'SoLuongSuDung' => null],
        ]);

        // 6. MauSac
        DB::table('MauSac')->insert([
            ['MaMau' => 1, 'TenMau' => 'Đen'],
            ['MaMau' => 2, 'TenMau' => 'Xanh dương'],
            ['MaMau' => 3, 'TenMau' => 'Đỏ'],
            ['MaMau' => 4, 'TenMau' => 'Vàng'],
            ['MaMau' => 5, 'TenMau' => 'Bạc'],
            ['MaMau' => 6, 'TenMau' => 'Trắng'],
            ['MaMau' => 7, 'TenMau' => 'Tím'],
            ['MaMau' => 8, 'TenMau' => 'Xanh lá'],
            ['MaMau' => 9, 'TenMau' => 'Cam'],
            ['MaMau' => 10, 'TenMau' => 'Hồng'],
        ]);

        // 7. Size
        $sizes = [
            ['MaSize' => 1, 'TenSize' => '3U', 'LoaiSize' => 'Vot'],
            ['MaSize' => 2, 'TenSize' => '4U', 'LoaiSize' => 'Vot'],
            ['MaSize' => 3, 'TenSize' => '5U', 'LoaiSize' => 'Vot'],
            ['MaSize' => 4, 'TenSize' => 'S', 'LoaiSize' => 'AoQuan'],
            ['MaSize' => 5, 'TenSize' => 'M', 'LoaiSize' => 'AoQuan'],
            ['MaSize' => 6, 'TenSize' => 'L', 'LoaiSize' => 'AoQuan'],
            ['MaSize' => 7, 'TenSize' => 'XL', 'LoaiSize' => 'AoQuan'],
            ['MaSize' => 8, 'TenSize' => '2XL', 'LoaiSize' => 'AoQuan'],
        ];
        for ($i = 30; $i <= 47; $i++) {
            $sizes[] = [
                'MaSize' => count($sizes) + 1,
                'TenSize' => (string)$i,
                'LoaiSize' => 'Giay',
            ];
        }
        DB::table('Size')->insert($sizes);

        // 8. TaiKhoan (Hỗ trợ mật khẩu cũ và bcrypt)
        DB::table('TaiKhoan')->insert([
            ['MaTaiKhoan' => 1, 'TenDangNhap' => 'admin', 'MatKhau' => Hash::make('admin@123'), 'Email' => 'admin@shopcaulong.com', 'MaQuyen' => 1, 'NgayTao' => now()],
            ['MaTaiKhoan' => 2, 'TenDangNhap' => 'nhanvien_banhang', 'MatKhau' => Hash::make('nv@123'), 'Email' => 'nhanvien1@shopcaulong.com', 'MaQuyen' => 2, 'NgayTao' => now()],
            ['MaTaiKhoan' => 3, 'TenDangNhap' => 'nguyenvana', 'MatKhau' => Hash::make('vana@123'), 'Email' => 'nguyenvana@gmail.com', 'MaQuyen' => 3, 'NgayTao' => now()],
            ['MaTaiKhoan' => 4, 'TenDangNhap' => 'tranthib', 'MatKhau' => Hash::make('thib@123'), 'Email' => 'tranthib@gmail.com', 'MaQuyen' => 3, 'NgayTao' => now()],
        ]);

        // 9. KhachHang
        DB::table('KhachHang')->insert([
            ['MaKhachHang' => 1, 'HoTen' => 'Admin Quản Trị', 'Email' => 'admin@shopcaulong.com', 'SoDienThoai' => '0901000001', 'DiaChi' => 'Tại cửa hàng', 'MaTaiKhoan' => 1, 'NgayTao' => now()],
            ['MaKhachHang' => 2, 'HoTen' => 'Nhân Viên Bán Hàng', 'Email' => 'nhanvien1@shopcaulong.com', 'SoDienThoai' => '0901000002', 'DiaChi' => 'Tại cửa hàng', 'MaTaiKhoan' => 2, 'NgayTao' => now()],
            ['MaKhachHang' => 3, 'HoTen' => 'Nguyễn Văn A', 'Email' => 'nguyenvana@gmail.com', 'SoDienThoai' => '0901234567', 'DiaChi' => '123 Nguyễn Trãi, Q.1, TP.HCM', 'MaTaiKhoan' => 3, 'NgayTao' => now()],
            ['MaKhachHang' => 4, 'HoTen' => 'Trần Thị B', 'Email' => 'tranthib@gmail.com', 'SoDienThoai' => '0909876543', 'DiaChi' => '456 Lê Lợi, Q.3, TP.HCM', 'MaTaiKhoan' => 4, 'NgayTao' => now()],
        ]);

        // 10. SanPham (43 sản phẩm)
        $products = [
            // Vợt (1-27)
            ['MaSanPham' => 1, 'TenSanPham' => 'Vợt Cầu Lông Yonex Astrox 100ZZ', 'MoTa' => 'Yonex Astrox 100ZZ Kurenai là phiên bản nâng cấp của Astrox 100ZZ, nổi bật với màu đỏ thẫm và công nghệ cải tiến cho lối chơi tấn công mạnh mẽ. Được nhiều vận động viên hàng đầu như Viktor Axelsen sử dụng.', 'GiaGoc' => 4500000, 'HinhAnhDaiDien' => 'astrox100zz.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 2, 'TenSanPham' => 'Vợt Cầu Lông Yonex Astrox 88D Pro 2024', 'MoTa' => 'Chuyên dụng cho đánh đôi, vị trí cầu sau, thiên về tấn công.', 'GiaGoc' => 4799000, 'HinhAnhDaiDien' => 'astrox88dpro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 3, 'TenSanPham' => 'Vợt Cầu Lông Yonex Astrox 88S Pro 2024', 'MoTa' => 'Chuyên dụng cho đánh đôi, vị trí cầu trước, thiên về tốc độ và kiểm soát.', 'GiaGoc' => 4799000, 'HinhAnhDaiDien' => 'astrox88spro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 4, 'TenSanPham' => 'Vợt Cầu Lông Yonex Astrox 99 Pro', 'MoTa' => 'Vợt nặng đầu, thiên công, dành cho VĐV chuyên nghiệp.', 'GiaGoc' => 4190000, 'HinhAnhDaiDien' => 'astrox99pro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 5, 'TenSanPham' => 'Vợt Cầu Lông Yonex Astrox 77 Pro', 'MoTa' => 'Vợt cân bằng, linh hoạt, dễ chơi, phù hợp cho người chơi phong trào.', 'GiaGoc' => 3900000, 'HinhAnhDaiDien' => 'astrox77pro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 6, 'TenSanPham' => 'Vợt Cầu Lông Yonex Nanoflare 1000Z', 'MoTa' => 'Dòng vợt nhẹ đầu, tốc độ nhanh nhất, phù hợp cho phản tạt.', 'GiaGoc' => 4600000, 'HinhAnhDaiDien' => 'nanoflare1000z.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 7, 'TenSanPham' => 'Vợt Cầu Lông Yonex Nanoflare 700 Pro', 'MoTa' => 'Vợt nhẹ đầu, tốc độ cao, hỗ trợ phản tạt nhanh.', 'GiaGoc' => 4489000, 'HinhAnhDaiDien' => 'nanoflare700pro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 8, 'TenSanPham' => 'Vợt Cầu Lông Yonex Arcsaber 11 Pro', 'MoTa' => 'Dòng vợt cân bằng, kiểm soát cầu tuyệt vời, huyền thoại trở lại.', 'GiaGoc' => 4100000, 'HinhAnhDaiDien' => 'arcsaber11pro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 9, 'TenSanPham' => 'Vợt Cầu Lông Lining Aeronaut 9000C', 'MoTa' => 'Dòng vợt tấn công, nặng đầu, khung vát gió.', 'GiaGoc' => 4300000, 'HinhAnhDaiDien' => 'lining_aero9000c.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 10, 'TenSanPham' => 'Vợt Cầu Lông Lining Aeronaut 9000I', 'MoTa' => 'Dòng vợt chuyên đánh tốc độ, phản tạt nhanh.', 'GiaGoc' => 4300000, 'HinhAnhDaiDien' => 'lining_aero9000i.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 11, 'TenSanPham' => 'Vợt Cầu Lông Lining Tectonic 7', 'MoTa' => 'Vợt công thủ toàn diện, dễ thuần.', 'GiaGoc' => 3200000, 'HinhAnhDaiDien' => 'lining_tectonic7.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 12, 'TenSanPham' => 'Vợt Cầu Lông Lining Axforce 90 Max', 'MoTa' => 'Vợt Rồng chuyên công, rất nặng đầu.', 'GiaGoc' => 4100000, 'HinhAnhDaiDien' => 'lining_axforce90max.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 13, 'TenSanPham' => 'Vợt Cầu Lông Lining Axforce 80', 'MoTa' => 'Vợt tấn công, trợ lực tốt, dễ chơi hơn Axforce 90.', 'GiaGoc' => 3900000, 'HinhAnhDaiDien' => 'lining_axforce80.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 14, 'TenSanPham' => 'Vợt Cầu Lông Lining Calibar 600', 'MoTa' => 'Vợt tầm trung, thiên công, giá cả phải chăng.', 'GiaGoc' => 3100000, 'HinhAnhDaiDien' => 'lining_calibar600.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 15, 'TenSanPham' => 'Vợt Cầu Lông Lining Bladex 800', 'MoTa' => 'Dòng vợt mới, khung sắc, tốc độ nhanh.', 'GiaGoc' => 4200000, 'HinhAnhDaiDien' => 'lining_bladex800.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 16, 'TenSanPham' => 'Vợt Cầu Lông Lining Tectonic 9', 'MoTa' => 'Vợt cao cấp, nặng đầu, tăng cường lực đập.', 'GiaGoc' => 3500000, 'HinhAnhDaiDien' => 'lining_tectonic9.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 17, 'TenSanPham' => 'Vợt Cầu Lông Victor Auraspeed 100X Ultra', 'MoTa' => 'Dòng vợt tốc độ, linh hoạt, công thủ toàn diện.', 'GiaGoc' => 4100000, 'HinhAnhDaiDien' => 'victor_as100x_ultra.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 18, 'TenSanPham' => 'Vợt Cầu Lông Victor TK Ryuga Metallic', 'MoTa' => 'Vợt chuyên công, thiết kế mạnh mẽ, đập cầu uy lực.', 'GiaGoc' => 3500000, 'HinhAnhDaiDien' => 'victor_ryuga_metallic.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 19, 'TenSanPham' => 'Vợt Cầu Lông Victor Brave Sword 12 Pro', 'MoTa' => 'Huyền thoại vợt Kiếm, khung vát kim cương, tốc độ cực nhanh.', 'GiaGoc' => 3200000, 'HinhAnhDaiDien' => 'victor_brs12_pro.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 20, 'TenSanPham' => 'Vợt Cầu Lông Victor Jetspeed S 10', 'MoTa' => 'Vợt tốc độ, nhẹ, phản tạt nhanh, dễ điều khiển.', 'GiaGoc' => 2850000, 'HinhAnhDaiDien' => 'victor_js10.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 21, 'TenSanPham' => 'Vợt Cầu Lông Victor DriveX 12', 'MoTa' => 'Vợt công thủ toàn diện, kiểm soát cầu tốt.', 'GiaGoc' => 3800000, 'HinhAnhDaiDien' => 'victor_dx12.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 22, 'TenSanPham' => 'Vợt Cầu Lông Victor Thruster F Claw', 'MoTa' => 'Vợt thiên công, nặng đầu, biệt danh Móng Vuốt.', 'GiaGoc' => 2150000, 'HinhAnhDaiDien' => 'victor_tfclaw.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 23, 'TenSanPham' => 'Vợt Cầu Lông Mizuno Fortius 10 Power', 'MoTa' => 'Vợt cao cấp, chuyên công, sản xuất tại Nhật Bản.', 'GiaGoc' => 4800000, 'HinhAnhDaiDien' => 'mizuno_f10power.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 24, 'TenSanPham' => 'Vợt Cầu Lông Mizuno Fortius 10 Quick', 'MoTa' => 'Vợt cao cấp, chuyên tốc độ, sản xuất tại Nhật Bản.', 'GiaGoc' => 4800000, 'HinhAnhDaiDien' => 'mizuno_f10quick.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 25, 'TenSanPham' => 'Vợt Cầu Lông Mizuno JPX Reserve Edition', 'MoTa' => 'Phiên bản đặc biệt, công thủ toàn diện.', 'GiaGoc' => 4990000, 'HinhAnhDaiDien' => 'mizuno_jpx_reserve.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => 8, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 26, 'TenSanPham' => 'Vợt Cầu Lông Mizuno Fortius 70', 'MoTa' => 'Vợt tầm trung, nặng đầu, hỗ trợ tấn công.', 'GiaGoc' => 3599000, 'HinhAnhDaiDien' => 'mizuno_fortius70.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 27, 'TenSanPham' => 'Vợt Cầu Lông Mizuno Altius 01 Speed Limited', 'MoTa' => 'Vợt cân bằng, linh hoạt, dễ điều khiển.', 'GiaGoc' => 4500000, 'HinhAnhDaiDien' => 'mizuno_altius01speedlimited.jpg', 'MaLoai' => 1, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],

            // Giày (28-37)
            ['MaSanPham' => 28, 'TenSanPham' => 'Giày Cầu Lông Yonex SHB 65Z4', 'MoTa' => 'Dòng giày toàn diện, êm ái, bám sân.', 'GiaGoc' => 2800000, 'HinhAnhDaiDien' => 'yonex_65z4.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 29, 'TenSanPham' => 'Giày Cầu Lông Yonex Eclipsion Z3', 'MoTa' => 'Tăng cường độ ổn định, chống lật cổ chân.', 'GiaGoc' => 3100000, 'HinhAnhDaiDien' => 'yonex_eclipsion_trang_z3.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => 1, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 30, 'TenSanPham' => 'Giày Cầu Lông Yonex Aerus Z2', 'MoTa' => 'Siêu nhẹ, tăng tốc độ di chuyển.', 'GiaGoc' => 3000000, 'HinhAnhDaiDien' => 'yonex_aerus_z2_den.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 31, 'TenSanPham' => 'Giày Cầu Lông Lining AYAS012-1', 'MoTa' => 'Dòng Lưỡi Dao Bóng Tối, bám sân, ổn định.', 'GiaGoc' => 3000000, 'HinhAnhDaiDien' => 'lining_ayas012_den.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 32, 'TenSanPham' => 'Giày Cầu Lông Lining AYZU017-1', 'MoTa' => 'Giày bền bỉ, công nghệ Bounse+.', 'GiaGoc' => 2160000, 'HinhAnhDaiDien' => 'lining_ayzu017.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 33, 'TenSanPham' => 'Giày Cầu Lông Lining AYAS018-6', 'MoTa' => 'Giày tầm trung, êm ái, thiết kế đẹp.', 'GiaGoc' => 2450000, 'HinhAnhDaiDien' => 'lining_ayas018.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => 3, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 34, 'TenSanPham' => 'Giày Cầu Lông Victor A970ACE', 'MoTa' => 'Phiên bản đặc biệt, ổn định, êm ái.', 'GiaGoc' => 2190000, 'HinhAnhDaiDien' => 'victor_a970.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => 5, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 35, 'TenSanPham' => 'Giày Cầu Lông Victor P9200III', 'MoTa' => 'Dòng P9200 huyền thoại, siêu bền.', 'GiaGoc' => 2140000, 'HinhAnhDaiDien' => 'victor_p9200iii.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 3, 'MaHang' => 3, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 36, 'TenSanPham' => 'Giày Cầu Lông Mizuno Wave Claw Neo 2', 'MoTa' => 'Tốc độ, êm ái, công nghệ Enerzy.', 'GiaGoc' => 2750000, 'HinhAnhDaiDien' => 'mizuno_waveclaw_neo2_xanh.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 37, 'TenSanPham' => 'Giày Cầu Lông Mizuno Wave Fang Pro', 'MoTa' => 'Dòng giày ổn định, da thật, sản xuất tại Nhật.', 'GiaGoc' => 3900000, 'HinhAnhDaiDien' => 'mizuno_fangpro.jpg', 'MaLoai' => 2, 'MaNhaCungCap' => 4, 'MaHang' => 4, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],

            // Áo / Quần (38-43)
            ['MaSanPham' => 38, 'TenSanPham' => 'Áo Cầu Lông Yonex TRM3089', 'MoTa' => 'Áo thi đấu đội tuyển, chất liệu poly thoáng mát.', 'GiaGoc' => 119000, 'HinhAnhDaiDien' => 'yonex_trm3089.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => 2, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 39, 'TenSanPham' => 'Áo Cầu Lông Yonex TRM3053', 'MoTa' => 'Áo thi đấu đội tuyển, chất liệu poly cao cấp.', 'GiaGoc' => 169000, 'HinhAnhDaiDien' => 'yonex_trm3053.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 40, 'TenSanPham' => 'Quần Cầu Lông Yonex TSM3085', 'MoTa' => 'Quần short thể thao, co giãn 4 chiều.', 'GiaGoc' => 139000, 'HinhAnhDaiDien' => 'yonex_tsm3085.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 1, 'MaHang' => 1, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 41, 'TenSanPham' => 'Áo Cầu Lông Lining A421', 'MoTa' => 'Áo thi đấu đội tuyển Trung Quốc 2024.', 'GiaGoc' => 130000, 'HinhAnhDaiDien' => 'lining_a421.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 42, 'TenSanPham' => 'Áo Cầu Lông Lining 3715', 'MoTa' => 'Áo thi đấu đội tuyển Trung Quốc 2024.', 'GiaGoc' => 160000, 'HinhAnhDaiDien' => 'lining_3715.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => null, 'CoSize' => 1, 'CoMau' => 1],
            ['MaSanPham' => 43, 'TenSanPham' => 'Quần Cầu Lông Lining Q21', 'MoTa' => 'Quần short thể thao, chất liệu thoáng khí.', 'GiaGoc' => 110000, 'HinhAnhDaiDien' => 'lining_q21.jpg', 'MaLoai' => 3, 'MaNhaCungCap' => 2, 'MaHang' => 2, 'MaKhuyenMai' => 2, 'CoSize' => 1, 'CoMau' => 1],
        ];
        DB::table('SanPham')->insert($products);

        // 11. ChiTietSanPham (Biến thể mẫu chính)
        $variants = [
            // Vợt
            ['MaChiTiet' => 1, 'MaSanPham' => 1, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4500000, 'SoLuongTon' => 50, 'SKU' => 'YN-AX100ZZ-3U', 'HinhAnh' => 'astrox100zz.jpg'],
            ['MaChiTiet' => 2, 'MaSanPham' => 1, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 4500000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX100ZZ-4U', 'HinhAnh' => 'astrox100zz.jpg'],
            ['MaChiTiet' => 3, 'MaSanPham' => 2, 'MaMau' => 2, 'MaSize' => 1, 'GiaBan' => 4799000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX88DPRO2024-3U', 'HinhAnh' => 'astrox88dpro.jpg'],
            ['MaChiTiet' => 4, 'MaSanPham' => 2, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 4799000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX88DPRO2024-4U', 'HinhAnh' => 'astrox88dpro.jpg'],
            ['MaChiTiet' => 5, 'MaSanPham' => 3, 'MaMau' => 2, 'MaSize' => 1, 'GiaBan' => 4799000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX88SPRO2024-3U', 'HinhAnh' => 'astrox88spro.jpg'],
            ['MaChiTiet' => 6, 'MaSanPham' => 3, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 4799000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX88SPRO2024-4U', 'HinhAnh' => 'astrox88spro.jpg'],
            ['MaChiTiet' => 7, 'MaSanPham' => 4, 'MaMau' => 3, 'MaSize' => 1, 'GiaBan' => 4190000, 'SoLuongTon' => 50, 'SKU' => 'YN-AX99PRO-DO-3U', 'HinhAnh' => 'astrox99pro.jpg'],
            ['MaChiTiet' => 8, 'MaSanPham' => 4, 'MaMau' => 3, 'MaSize' => 2, 'GiaBan' => 4190000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX99PRO-DO-4U', 'HinhAnh' => 'astrox99pro.jpg'],
            ['MaChiTiet' => 9, 'MaSanPham' => 4, 'MaMau' => 6, 'MaSize' => 1, 'GiaBan' => 4190000, 'SoLuongTon' => 50, 'SKU' => 'YN-AX99PRO-TRANG-3U', 'HinhAnh' => 'astrox99protrang.jpg'],
            ['MaChiTiet' => 10, 'MaSanPham' => 4, 'MaMau' => 6, 'MaSize' => 2, 'GiaBan' => 4190000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX99PRO-TRANG-4U', 'HinhAnh' => 'astrox99protrang.jpg'],
            ['MaChiTiet' => 11, 'MaSanPham' => 5, 'MaMau' => 3, 'MaSize' => 1, 'GiaBan' => 3900000, 'SoLuongTon' => 50, 'SKU' => 'YN-AX77PRO-DO-3U', 'HinhAnh' => 'astrox77pro.jpg'],
            ['MaChiTiet' => 12, 'MaSanPham' => 5, 'MaMau' => 3, 'MaSize' => 2, 'GiaBan' => 3900000, 'SoLuongTon' => 100, 'SKU' => 'YN-AX77PRO-DO-4U', 'HinhAnh' => 'astrox77pro.jpg'],
            ['MaChiTiet' => 13, 'MaSanPham' => 6, 'MaMau' => 4, 'MaSize' => 1, 'GiaBan' => 4600000, 'SoLuongTon' => 50, 'SKU' => 'YN-NNF1000Z-VANG-3U', 'HinhAnh' => 'nanoflare1000z.jpg'],
            ['MaChiTiet' => 14, 'MaSanPham' => 6, 'MaMau' => 4, 'MaSize' => 2, 'GiaBan' => 4600000, 'SoLuongTon' => 100, 'SKU' => 'YN-NNF1000Z-VANG-4U', 'HinhAnh' => 'nanoflare1000z.jpg'],
            ['MaChiTiet' => 15, 'MaSanPham' => 7, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 4489000, 'SoLuongTon' => 50, 'SKU' => 'YN-NNF700PRO-DEN-4U', 'HinhAnh' => 'nanoflare700pro.jpg'],
            ['MaChiTiet' => 16, 'MaSanPham' => 8, 'MaMau' => 3, 'MaSize' => 1, 'GiaBan' => 4100000, 'SoLuongTon' => 50, 'SKU' => 'YN-ARC11PRO-DO-3U', 'HinhAnh' => 'arcsaber11pro.jpg'],
            ['MaChiTiet' => 17, 'MaSanPham' => 8, 'MaMau' => 3, 'MaSize' => 2, 'GiaBan' => 4100000, 'SoLuongTon' => 100, 'SKU' => 'YN-ARC11PRO-DO-4U', 'HinhAnh' => 'arcsaber11pro.jpg'],
            ['MaChiTiet' => 18, 'MaSanPham' => 9, 'MaMau' => 2, 'MaSize' => 1, 'GiaBan' => 4300000, 'SoLuongTon' => 100, 'SKU' => 'LN-AER9000C-XANH-3U', 'HinhAnh' => 'lining_aero9000c.jpg'],
            ['MaChiTiet' => 19, 'MaSanPham' => 9, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 4300000, 'SoLuongTon' => 100, 'SKU' => 'LN-AER9000C-XANH-4U', 'HinhAnh' => 'lining_aero9000c.jpg'],
            ['MaChiTiet' => 20, 'MaSanPham' => 10, 'MaMau' => 7, 'MaSize' => 3, 'GiaBan' => 4300000, 'SoLuongTon' => 100, 'SKU' => 'LN-AER9000I-TIM-5U', 'HinhAnh' => 'lining_aero9000i.jpg'],
            ['MaChiTiet' => 21, 'MaSanPham' => 11, 'MaMau' => 6, 'MaSize' => 2, 'GiaBan' => 3200000, 'SoLuongTon' => 100, 'SKU' => 'LN-TEC7-TRANG-4U', 'HinhAnh' => 'lining_tectonic7.jpg'],
            ['MaChiTiet' => 22, 'MaSanPham' => 12, 'MaMau' => 2, 'MaSize' => 2, 'GiaBan' => 4100000, 'SoLuongTon' => 40, 'SKU' => 'LN-AX90-XANH-4U', 'HinhAnh' => 'lining_axforce90max.jpg'],
            ['MaChiTiet' => 23, 'MaSanPham' => 13, 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 3900000, 'SoLuongTon' => 40, 'SKU' => 'LN-AX80-DEN-3U', 'HinhAnh' => 'lining_axforce80.jpg'],
            ['MaChiTiet' => 24, 'MaSanPham' => 13, 'MaMau' => 1, 'MaSize' => 2, 'GiaBan' => 3900000, 'SoLuongTon' => 40, 'SKU' => 'LN-AX80-DEN-4U', 'HinhAnh' => 'lining_axforce80.jpg'],
            ['MaChiTiet' => 25, 'MaSanPham' => 13, 'MaMau' => 1, 'MaSize' => 3, 'GiaBan' => 3900000, 'SoLuongTon' => 40, 'SKU' => 'LN-AX80-DEN-5U', 'HinhAnh' => 'lining_axforce80.jpg'],

            // Giày
            ['MaChiTiet' => 26, 'MaSanPham' => 28, 'MaMau' => 6, 'MaSize' => 19, 'GiaBan' => 2800000, 'SoLuongTon' => 30, 'SKU' => 'YN-SHB65Z4-TRANG-40', 'HinhAnh' => 'yonex_65z4.jpg'],
            ['MaChiTiet' => 27, 'MaSanPham' => 28, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 2800000, 'SoLuongTon' => 30, 'SKU' => 'YN-SHB65Z4-TRANG-41', 'HinhAnh' => 'yonex_65z4.jpg'],
            ['MaChiTiet' => 28, 'MaSanPham' => 28, 'MaMau' => 6, 'MaSize' => 21, 'GiaBan' => 2800000, 'SoLuongTon' => 30, 'SKU' => 'YN-SHB65Z4-TRANG-42', 'HinhAnh' => 'yonex_65z4.jpg'],
            ['MaChiTiet' => 29, 'MaSanPham' => 29, 'MaMau' => 6, 'MaSize' => 20, 'GiaBan' => 3100000, 'SoLuongTon' => 30, 'SKU' => 'YN-ECLIPSONZ3-TRANG-41', 'HinhAnh' => 'yonex_eclipsion_trang_z3.jpg'],
            ['MaChiTiet' => 30, 'MaSanPham' => 30, 'MaMau' => 1, 'MaSize' => 21, 'GiaBan' => 3000000, 'SoLuongTon' => 30, 'SKU' => 'YN-AERUSZ2-DEN-42', 'HinhAnh' => 'yonex_aerus_z2_den.jpg'],

            // Áo / Quần
            ['MaChiTiet' => 31, 'MaSanPham' => 38, 'MaMau' => 6, 'MaSize' => 5, 'GiaBan' => 119000, 'SoLuongTon' => 25, 'SKU' => 'YN-TRM3089-TRANG-M', 'HinhAnh' => 'yonex_trm3089.jpg'],
            ['MaChiTiet' => 32, 'MaSanPham' => 38, 'MaMau' => 6, 'MaSize' => 6, 'GiaBan' => 119000, 'SoLuongTon' => 25, 'SKU' => 'YN-TRM3089-TRANG-L', 'HinhAnh' => 'yonex_trm3089.jpg'],
            ['MaChiTiet' => 33, 'MaSanPham' => 39, 'MaMau' => 8, 'MaSize' => 6, 'GiaBan' => 169000, 'SoLuongTon' => 20, 'SKU' => 'YN-TRM3053-XANHLA-L', 'HinhAnh' => 'yonex_trm3053.jpg'],
            ['MaChiTiet' => 34, 'MaSanPham' => 40, 'MaMau' => 1, 'MaSize' => 6, 'GiaBan' => 139000, 'SoLuongTon' => 20, 'SKU' => 'YN-TSM3085-DEN-L', 'HinhAnh' => 'yonex_tsm3085.jpg'],
        ];
        DB::table('ChiTietSanPham')->insert($variants);

        // 12. ThongSoVot
        $specs = [
            ['MaThongSo' => 1, 'MaChiTiet' => 1, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 2, 'MaChiTiet' => 2, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 3, 'MaChiTiet' => 3, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 4, 'MaChiTiet' => 4, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 5, 'MaChiTiet' => 5, 'DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '670mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 6, 'MaChiTiet' => 6, 'DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '670mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 7, 'MaChiTiet' => 7, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 8, 'MaChiTiet' => 8, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 9, 'MaChiTiet' => 9, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 10, 'MaChiTiet' => 10, 'DoCanBang' => 'Nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 11, 'MaChiTiet' => 11, 'DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 12, 'MaChiTiet' => 12, 'DoCanBang' => 'Hơi nặng đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 13, 'MaChiTiet' => 13, 'DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Rất cứng', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 14, 'MaChiTiet' => 14, 'DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Rất cứng', 'ChieuDai' => '675mm', 'SucCang' => '20-28 lbs'],
            ['MaThongSo' => 15, 'MaChiTiet' => 15, 'DoCanBang' => 'Nhẹ đầu', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Trung bình', 'ChieuDai' => '675mm', 'SucCang' => '21-29 lbs'],
            ['MaThongSo' => 16, 'MaChiTiet' => 16, 'DoCanBang' => 'Cân bằng', 'TrongLuong' => '3U (85-89g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '19-27 lbs'],
            ['MaThongSo' => 17, 'MaChiTiet' => 17, 'DoCanBang' => 'Cân bằng', 'TrongLuong' => '4U (80-84g)', 'DoCung' => 'Cứng', 'ChieuDai' => '675mm', 'SucCang' => '19-27 lbs'],
        ];
        DB::table('ThongSoVot')->insert($specs);

        // 13. PhanHoi mẫu
        DB::table('PhanHoi')->insert([
            ['NoiDung' => 'Vợt đánh rất đầm tay, đập cầu uy lực, chính hãng 100%!', 'NgayPhanHoi' => now(), 'DanhGia' => 5, 'MaKhachHang' => 3, 'MaSanPham' => 1],
            ['NoiDung' => 'Giao hàng nhanh, vợt rất đẹp và vừa tay.', 'NgayPhanHoi' => now(), 'DanhGia' => 5, 'MaKhachHang' => 4, 'MaSanPham' => 2],
            ['NoiDung' => 'Cước căng chuẩn số kg, đóng gói cẩn thận.', 'NgayPhanHoi' => now(), 'DanhGia' => 4, 'MaKhachHang' => 3, 'MaSanPham' => 8],
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
