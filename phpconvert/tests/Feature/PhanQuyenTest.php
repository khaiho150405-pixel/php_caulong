<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm tra Middleware phân quyền: route của từng vai trò phải được bảo vệ đúng quyền hạn.
 */
class PhanQuyenTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_guest_is_redirected_to_login_from_admin_area(): void
    {
        $this->get('/admin')->assertRedirect();
        $this->get('/admin/san-pham')->assertRedirectContains('/TaiKhoan/DangNhap');
        $this->get('/DonHang/Index')->assertRedirectContains('/TaiKhoan/DangNhap');
    }

    public function test_customer_cannot_access_admin_area(): void
    {
        $kh = $this->khachHang();

        $this->actingAs($kh)->get('/admin')->assertRedirect(route('home'));
        $this->actingAs($kh)->get('/admin/san-pham')->assertRedirect(route('home'));
        $this->actingAs($kh)->post('/admin/san-pham/1/delete')->assertRedirect(route('home'));
        $this->assertDatabaseHas('SanPham', ['MaSanPham' => 1, 'deleted_at' => null]);
    }

    public function test_staff_can_manage_products_and_orders_but_not_accounts_or_promotions(): void
    {
        $nv = $this->nhanVien();

        $this->actingAs($nv)->get('/admin/san-pham')->assertStatus(200);
        $this->actingAs($nv)->get('/admin/don-hang')->assertStatus(200);

        $this->actingAs($nv)->get('/admin/tai-khoan')->assertRedirect(route('quanlysanpham.dashboard'));
        $this->actingAs($nv)->get('/admin/khuyen-mai')->assertRedirect(route('quanlysanpham.dashboard'));
        $this->actingAs($nv)->post('/admin/tai-khoan/3/delete')->assertRedirect(route('quanlysanpham.dashboard'));
        $this->assertDatabaseHas('TaiKhoan', ['MaTaiKhoan' => 3]);
    }
}
