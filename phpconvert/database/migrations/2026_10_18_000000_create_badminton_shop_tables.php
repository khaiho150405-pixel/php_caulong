<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. PhanQuyen
        Schema::create('PhanQuyen', function (Blueprint $table) {
            $table->increments('MaQuyen');
            $table->string('TenQuyen', 50);
        });

        // 2. TaiKhoan
        Schema::create('TaiKhoan', function (Blueprint $table) {
            $table->increments('MaTaiKhoan');
            $table->string('TenDangNhap', 50)->unique();
            $table->string('MatKhau', 255);
            $table->string('Email', 100)->nullable();
            $table->unsignedInteger('MaQuyen')->nullable();
            $table->timestamp('NgayTao')->useCurrent();

            $table->foreign('MaQuyen')->references('MaQuyen')->on('PhanQuyen')->nullOnDelete();
        });

        // 3. LoaiSanPham
        Schema::create('LoaiSanPham', function (Blueprint $table) {
            $table->increments('MaLoai');
            $table->string('TenLoai', 100);
            $table->string('MoTa', 255)->nullable();
        });

        // 4. NhaCungCap
        Schema::create('NhaCungCap', function (Blueprint $table) {
            $table->increments('MaNhaCungCap');
            $table->string('TenNhaCungCap', 100);
            $table->string('DiaChi', 255)->nullable();
            $table->string('SoDienThoai', 15)->nullable();
            $table->string('Email', 100)->nullable();
        });

        // 5. Hang
        Schema::create('Hang', function (Blueprint $table) {
            $table->increments('MaHang');
            $table->string('TenHang', 100);
            $table->string('QuocGia', 50)->nullable();
        });

        // 6. KhuyenMai
        Schema::create('KhuyenMai', function (Blueprint $table) {
            $table->increments('MaKhuyenMai');
            $table->string('TenChuongTrinh', 100);
            $table->string('MoTa', 255)->nullable();
            $table->date('NgayBatDau')->nullable();
            $table->date('NgayKetThuc')->nullable();
            $table->decimal('PhanTramGiam', 5, 2)->nullable();
            $table->decimal('GiamToiDa', 15, 2)->nullable();
            $table->integer('SoLuongSuDung')->nullable();
        });

        // 7. MauSac
        Schema::create('MauSac', function (Blueprint $table) {
            $table->increments('MaMau');
            $table->string('TenMau', 50);
        });

        // 8. Size
        Schema::create('Size', function (Blueprint $table) {
            $table->increments('MaSize');
            $table->string('TenSize', 20);
            $table->string('LoaiSize', 30); // 'AoQuan', 'Giay', 'Vot'
        });

        // 9. SanPham
        Schema::create('SanPham', function (Blueprint $table) {
            $table->increments('MaSanPham');
            $table->string('TenSanPham', 100);
            $table->text('MoTa')->nullable();
            $table->decimal('GiaGoc', 15, 2)->nullable();
            $table->string('HinhAnhDaiDien', 255)->nullable();
            $table->unsignedInteger('MaLoai')->nullable();
            $table->unsignedInteger('MaNhaCungCap')->nullable();
            $table->unsignedInteger('MaHang')->nullable();
            $table->unsignedInteger('MaKhuyenMai')->nullable();
            $table->boolean('CoSize')->default(false);
            $table->boolean('CoMau')->default(false);
            $table->timestamp('NgayTao')->useCurrent();

            $table->foreign('MaLoai')->references('MaLoai')->on('LoaiSanPham')->nullOnDelete();
            $table->foreign('MaNhaCungCap')->references('MaNhaCungCap')->on('NhaCungCap')->nullOnDelete();
            $table->foreign('MaHang')->references('MaHang')->on('Hang')->nullOnDelete();
            $table->foreign('MaKhuyenMai')->references('MaKhuyenMai')->on('KhuyenMai')->nullOnDelete();
        });

        // 10. ChiTietSanPham
        Schema::create('ChiTietSanPham', function (Blueprint $table) {
            $table->increments('MaChiTiet');
            $table->unsignedInteger('MaSanPham')->nullable();
            $table->unsignedInteger('MaMau')->nullable();
            $table->unsignedInteger('MaSize')->nullable();
            $table->decimal('GiaBan', 15, 2)->nullable();
            $table->integer('SoLuongTon')->default(0);
            $table->string('SKU', 50)->unique();
            $table->string('HinhAnh', 255)->nullable();

            $table->foreign('MaSanPham')->references('MaSanPham')->on('SanPham')->cascadeOnDelete();
            $table->foreign('MaMau')->references('MaMau')->on('MauSac')->nullOnDelete();
            $table->foreign('MaSize')->references('MaSize')->on('Size')->nullOnDelete();
        });

        // 11. ThongSoVot
        Schema::create('ThongSoVot', function (Blueprint $table) {
            $table->increments('MaThongSo');
            $table->unsignedInteger('MaChiTiet')->nullable();
            $table->string('DoCanBang', 50)->nullable();
            $table->string('TrongLuong', 20)->nullable();
            $table->string('DoCung', 50)->nullable();
            $table->string('ChieuDai', 20)->nullable();
            $table->string('SucCang', 20)->nullable();

            $table->foreign('MaChiTiet')->references('MaChiTiet')->on('ChiTietSanPham')->cascadeOnDelete();
        });

        // 12. KhachHang
        Schema::create('KhachHang', function (Blueprint $table) {
            $table->increments('MaKhachHang');
            $table->string('HoTen', 100);
            $table->string('Email', 100)->nullable();
            $table->string('SoDienThoai', 15)->nullable();
            $table->string('DiaChi', 255)->nullable();
            $table->unsignedInteger('MaTaiKhoan')->unique()->nullable();
            $table->timestamp('NgayTao')->useCurrent();

            $table->foreign('MaTaiKhoan')->references('MaTaiKhoan')->on('TaiKhoan')->cascadeOnDelete();
        });

        // 13. DonHang
        Schema::create('DonHang', function (Blueprint $table) {
            $table->increments('MaDonHang');
            $table->timestamp('NgayDat')->useCurrent();
            $table->string('TrangThai', 50)->default('Đang xử lý');
            $table->decimal('TongTien', 15, 2)->default(0);
            $table->decimal('TongTienSauGiam', 15, 2)->default(0);
            $table->unsignedInteger('MaKhachHang')->nullable();
            $table->decimal('TienGiam', 15, 2)->default(0);
            $table->string('DiaChiGiaoHang', 255)->nullable();
            $table->string('SoDienThoaiNhanHang', 15)->nullable();
            $table->string('GhiChu', 500)->nullable();

            $table->foreign('MaKhachHang')->references('MaKhachHang')->on('KhachHang')->nullOnDelete();
        });

        // 14. ChiTietDonHang
        Schema::create('ChiTietDonHang', function (Blueprint $table) {
            $table->increments('MaChiTiet');
            $table->unsignedInteger('MaDonHang')->nullable();
            $table->unsignedInteger('MaChiTietSanPham')->nullable();
            $table->integer('SoLuong')->default(1);
            $table->decimal('DonGia', 15, 2)->default(0);
            $table->decimal('ThanhTien', 15, 2)->default(0);

            $table->foreign('MaDonHang')->references('MaDonHang')->on('DonHang')->cascadeOnDelete();
            $table->foreign('MaChiTietSanPham')->references('MaChiTiet')->on('ChiTietSanPham')->nullOnDelete();
        });

        // 15. PhanHoi
        Schema::create('PhanHoi', function (Blueprint $table) {
            $table->increments('MaPhanHoi');
            $table->string('NoiDung', 255)->nullable();
            $table->timestamp('NgayPhanHoi')->useCurrent();
            $table->unsignedTinyInteger('DanhGia')->default(5);
            $table->unsignedInteger('MaKhachHang')->nullable();
            $table->unsignedInteger('MaSanPham')->nullable();

            $table->foreign('MaKhachHang')->references('MaKhachHang')->on('KhachHang')->nullOnDelete();
            $table->foreign('MaSanPham')->references('MaSanPham')->on('SanPham')->cascadeOnDelete();
        });

        // 16. GioHang
        Schema::create('GioHang', function (Blueprint $table) {
            $table->increments('MaGioHang');
            $table->unsignedInteger('MaKhachHang')->nullable();
            $table->unsignedInteger('MaChiTietSanPham')->nullable();
            $table->integer('SoLuong')->default(1);
            $table->timestamp('NgayThem')->useCurrent();

            $table->foreign('MaKhachHang')->references('MaKhachHang')->on('KhachHang')->cascadeOnDelete();
            $table->foreign('MaChiTietSanPham')->references('MaChiTiet')->on('ChiTietSanPham')->cascadeOnDelete();
        });

        // 17. LichSuThayDoiGia
        Schema::create('LichSuThayDoiGia', function (Blueprint $table) {
            $table->increments('MaLichSu');
            $table->unsignedInteger('MaChiTiet')->nullable();
            $table->decimal('GiaCu', 15, 2)->nullable();
            $table->decimal('GiaMoi', 15, 2)->nullable();
            $table->timestamp('NgayThayDoi')->useCurrent();
            $table->string('NguoiThayDoi', 50)->nullable();
        });

        // 18. LogLoiGiaoTac
        Schema::create('LogLoiGiaoTac', function (Blueprint $table) {
            $table->increments('MaLog');
            $table->timestamp('ThoiGian')->useCurrent();
            $table->text('NoiDungLoi')->nullable();
            $table->string('MaGiaoTac', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('LogLoiGiaoTac');
        Schema::dropIfExists('LichSuThayDoiGia');
        Schema::dropIfExists('GioHang');
        Schema::dropIfExists('PhanHoi');
        Schema::dropIfExists('ChiTietDonHang');
        Schema::dropIfExists('DonHang');
        Schema::dropIfExists('KhachHang');
        Schema::dropIfExists('ThongSoVot');
        Schema::dropIfExists('ChiTietSanPham');
        Schema::dropIfExists('SanPham');
        Schema::dropIfExists('Size');
        Schema::dropIfExists('MauSac');
        Schema::dropIfExists('KhuyenMai');
        Schema::dropIfExists('Hang');
        Schema::dropIfExists('NhaCungCap');
        Schema::dropIfExists('LoaiSanPham');
        Schema::dropIfExists('TaiKhoan');
        Schema::dropIfExists('PhanQuyen');
    }
};
