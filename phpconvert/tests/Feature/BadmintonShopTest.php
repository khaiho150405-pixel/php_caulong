<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TaiKhoan;
use App\Models\SanPham;

class BadmintonShopTest extends TestCase
{
    public function test_home_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Cửa Hàng Cầu Lông');
    }

    public function test_product_detail_page_loads()
    {
        $sp = SanPham::first();
        $this->assertNotNull($sp);

        $response = $this->get("/Home/ChiTietSanPham/{$sp->MaSanPham}");
        $response->assertStatus(200);
        $response->assertSee($sp->TenSanPham);
    }

    public function test_san_pham_index_loads()
    {
        $response = $this->get('/SanPham/Index');
        $response->assertStatus(200);
    }

    public function test_khuyen_mai_index_loads()
    {
        $response = $this->get('/KhuyenMai/Index');
        $response->assertStatus(200);
        $response->assertSee('Chương trình Khuyến Mãi');
    }

    public function test_contact_page_loads()
    {
        $response = $this->get('/Home/Contact');
        $response->assertStatus(200);
        $response->assertSee('Thông Tin Cửa Hàng');
    }

    public function test_auth_pages_load()
    {
        $loginRes = $this->get('/TaiKhoan/DangNhap');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Đăng nhập');

        $registerRes = $this->get('/TaiKhoan/DangKy');
        $registerRes->assertStatus(200);
        $registerRes->assertSee('Đăng ký tài khoản');
    }

    public function test_admin_dashboard_loads_for_admin_user()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/QuanLySanPham/Dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Thống Kê');
    }

    public function test_admin_product_management_loads()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $response = $this->actingAs($admin)->get('/QuanLySanPham/Index');
        $response->assertStatus(200);
        $response->assertSee('Danh Sách Sản Phẩm');
    }

    public function test_admin_order_management_loads()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $response = $this->actingAs($admin)->get('/QuanLyDonHang/Index');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Đơn Hàng');
    }

    public function test_admin_account_management_loads()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $response = $this->actingAs($admin)->get('/QuanLyTaiKhoan/Index');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Tài Khoản');
    }

    public function test_admin_promotion_management_loads()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $response = $this->actingAs($admin)->get('/QuanLyKhuyenMai/Index');
        $response->assertStatus(200);
        $response->assertSee('Quản Lý Chương Trình Khuyến Mãi');
    }

    public function test_admin_notification_management_loads()
    {
        $admin = TaiKhoan::where('MaQuyen', 1)->first();
        $response = $this->actingAs($admin)->get('/QuanLyThongBao/ThongBao');
        $response->assertStatus(200);
        $response->assertSee('Cảnh Báo Phản Hồi');
    }

    public function test_chatbot_endpoint_responds_json()
    {
        $response = $this->postJson('/Chatbot/Ask', ['message' => 'Shop có vợt Yonex dưới 2 triệu không?']);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'reply']);
    }
}
