<?php

namespace Tests\Feature;

use App\Models\ChiTietSanPham;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * CRUD khu vực quản trị: Sản phẩm (+ biến thể), Khuyến mãi, Tài khoản; Validation + Flash message.
 */
class QuanLyDuLieuTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function duLieuSanPham(array $ghiDe = []): array
    {
        return array_merge([
            'TenSanPham' => 'Vợt Test Laravel',
            'GiaGoc' => 2500000,
            'MaLoai' => 1,
            'MaHang' => 1,
            'MaNhaCungCap' => 1,
            'MaKhuyenMai' => '',
            'MoTa' => 'Mô tả thử',
            'CoSize' => '1',
        ], $ghiDe);
    }

    public function test_admin_can_create_update_and_soft_delete_product(): void
    {
        Storage::fake('images');
        $admin = $this->admin();

        // CREATE (kèm upload ảnh qua Laravel Storage)
        $res = $this->actingAs($admin)->post('/admin/san-pham/create', $this->duLieuSanPham([
            'HinhAnhUpload' => UploadedFile::fake()->image('vot.png', 300, 300),
        ]));
        $sp = SanPham::where('TenSanPham', 'Vợt Test Laravel')->firstOrFail();
        $res->assertRedirect(route('quanlysanpham.details', ['id' => $sp->MaSanPham]));
        $res->assertSessionHas('message');
        $this->assertStringStartsWith('uploads/', $sp->HinhAnhDaiDien);
        Storage::disk('images')->assertExists($sp->HinhAnhDaiDien);

        // UPDATE
        $this->actingAs($admin)
            ->post("/admin/san-pham/{$sp->MaSanPham}/edit", $this->duLieuSanPham(['TenSanPham' => 'Vợt Đã Sửa', 'GiaGoc' => 3000000]))
            ->assertRedirect(route('quanlysanpham.index'))
            ->assertSessionHas('message', 'Cập nhật sản phẩm thành công!');
        $this->assertDatabaseHas('SanPham', ['MaSanPham' => $sp->MaSanPham, 'TenSanPham' => 'Vợt Đã Sửa']);

        // DELETE (Soft Delete)
        $this->actingAs($admin)
            ->post("/admin/san-pham/{$sp->MaSanPham}/delete")
            ->assertRedirect(route('quanlysanpham.index'))
            ->assertSessionHas('message');
        $this->assertSoftDeleted('SanPham', ['MaSanPham' => $sp->MaSanPham]);
        auth()->logout();
        $this->get("/Home/ChiTietSanPham/{$sp->MaSanPham}")->assertStatus(404);
    }

    public function test_product_validation_shows_vietnamese_errors(): void
    {
        $this->actingAs($this->admin())
            ->from('/admin/san-pham/create')
            ->post('/admin/san-pham/create', $this->duLieuSanPham(['TenSanPham' => '', 'GiaGoc' => -5, 'MaLoai' => 999]))
            ->assertRedirect('/admin/san-pham/create')
            ->assertSessionHasErrors([
                'TenSanPham' => 'Vui lòng nhập tên sản phẩm.',
                'GiaGoc' => 'Giá gốc phải tối thiểu là 0.',
                'MaLoai' => 'Loại sản phẩm đã chọn không hợp lệ.',
            ]);
    }

    public function test_admin_can_manage_product_variants(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/san-pham/1/bien-the', [
            'SKU' => 'TEST-SKU-01', 'MaMau' => 1, 'MaSize' => 1, 'GiaBan' => 1000000, 'SoLuongTon' => 7,
        ])->assertRedirect(route('quanlysanpham.details', ['id' => 1]))->assertSessionHas('message');

        $bt = ChiTietSanPham::where('SKU', 'TEST-SKU-01')->firstOrFail();
        $this->assertSame(1, (int) $bt->MaSanPham);

        // SKU trùng -> lỗi
        $this->actingAs($admin)->post('/admin/san-pham/1/bien-the', [
            'SKU' => 'TEST-SKU-01', 'GiaBan' => 1, 'SoLuongTon' => 1,
        ])->assertSessionHasErrors('SKU');

        $this->actingAs($admin)->post("/admin/bien-the/{$bt->MaChiTiet}/edit", [
            'SKU' => 'TEST-SKU-01', 'GiaBan' => 1200000, 'SoLuongTon' => 3,
        ])->assertSessionHas('message');
        $this->assertDatabaseHas('ChiTietSanPham', ['MaChiTiet' => $bt->MaChiTiet, 'SoLuongTon' => 3]);

        $this->actingAs($admin)->post("/admin/bien-the/{$bt->MaChiTiet}/delete")->assertSessionHas('message');
        $this->assertDatabaseMissing('ChiTietSanPham', ['MaChiTiet' => $bt->MaChiTiet]);
    }

    public function test_admin_can_crud_promotions(): void
    {
        $admin = $this->admin();
        $data = [
            'TenChuongTrinh' => 'Sale Test',
            'PhanTramGiam' => 20,
            'GiamToiDa' => 100000,
            'NgayBatDau' => now()->toDateString(),
            'NgayKetThuc' => now()->addDays(10)->toDateString(),
        ];

        $this->actingAs($admin)->post('/admin/khuyen-mai/create', $data)
            ->assertRedirect(route('quanlykhuyenmai.index'))->assertSessionHas('message');
        $km = KhuyenMai::where('TenChuongTrinh', 'Sale Test')->firstOrFail();

        $this->actingAs($admin)->post("/admin/khuyen-mai/{$km->MaKhuyenMai}/edit", array_merge($data, ['PhanTramGiam' => 30]))
            ->assertSessionHas('message');
        $this->assertEquals(30, (float) $km->fresh()->PhanTramGiam);

        // Ngày kết thúc trước ngày bắt đầu -> lỗi
        $this->actingAs($admin)->post('/admin/khuyen-mai/create', array_merge($data, ['NgayKetThuc' => now()->subDay()->toDateString()]))
            ->assertSessionHasErrors('NgayKetThuc');

        $this->actingAs($admin)->post("/admin/khuyen-mai/{$km->MaKhuyenMai}/delete")->assertSessionHas('message');
        $this->assertDatabaseMissing('KhuyenMai', ['MaKhuyenMai' => $km->MaKhuyenMai]);
    }

    public function test_admin_can_crud_accounts_but_not_delete_self(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/tai-khoan/create', [
            'TenDangNhap' => 'nhanvienmoi', 'MatKhau' => '123456', 'Email' => 'nvmoi@example.com',
            'MaQuyen' => TaiKhoan::QUYEN_NHANVIEN, 'HoTen' => 'Nhân Viên Mới', 'SoDienThoai' => '0912345678',
        ])->assertRedirect(route('quanlytaikhoan.index'))->assertSessionHas('message');

        $tk = TaiKhoan::where('TenDangNhap', 'nhanvienmoi')->firstOrFail();
        $this->assertDatabaseHas('KhachHang', ['MaTaiKhoan' => $tk->MaTaiKhoan, 'HoTen' => 'Nhân Viên Mới']);

        $this->actingAs($admin)->post("/admin/tai-khoan/{$tk->MaTaiKhoan}/edit", [
            'Email' => 'nvmoi2@example.com', 'MaQuyen' => TaiKhoan::QUYEN_NHANVIEN, 'HoTen' => 'Tên Đã Sửa',
        ])->assertSessionHas('message');
        $this->assertDatabaseHas('KhachHang', ['MaTaiKhoan' => $tk->MaTaiKhoan, 'HoTen' => 'Tên Đã Sửa']);

        $this->actingAs($admin)->post("/admin/tai-khoan/{$tk->MaTaiKhoan}/delete")->assertSessionHas('message');
        $this->assertDatabaseMissing('TaiKhoan', ['MaTaiKhoan' => $tk->MaTaiKhoan]);

        $this->actingAs($admin)->post("/admin/tai-khoan/{$admin->MaTaiKhoan}/delete")->assertSessionHas('error');
        $this->assertDatabaseHas('TaiKhoan', ['MaTaiKhoan' => $admin->MaTaiKhoan]);
    }
}
