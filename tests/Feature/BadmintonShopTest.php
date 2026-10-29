<?php

namespace Tests\Feature;

use App\Models\Hang;
use App\Models\SanPham;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm tra các trang chính của khu vực người dùng và khu vực quản trị đều hiển thị được.
 */
class BadmintonShopTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_home_page_loads_successfully(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Cửa Hàng Cầu Lông');
    }

    public function test_product_detail_page_loads(): void
    {
        $sp = SanPham::first();

        $this->get("/Home/ChiTietSanPham/{$sp->MaSanPham}")
            ->assertStatus(200)
            ->assertSee($sp->TenSanPham);
    }

    public function test_public_pages_load(): void
    {
        $this->get('/SanPham/Index')->assertStatus(200);
        $this->get('/KhuyenMai/Index')->assertStatus(200)->assertSee('Ưu đãi đang diễn ra');
        $this->get('/Home/Contact')->assertStatus(200)->assertSee('Thông Tin Cửa Hàng');
        $this->get('/TaiKhoan/DangNhap')->assertStatus(200)->assertSee('Đăng nhập');
        $this->get('/TaiKhoan/DangKy')->assertStatus(200)->assertSee('Đăng ký tài khoản');
    }

    public function test_search_is_paginated_and_keeps_keyword(): void
    {
        $res = $this->get('/SanPham/TimKiem?keyword=Yonex');
        $res->assertStatus(200);

        $tong = SanPham::timKiem('Yonex')->count();
        $this->assertGreaterThan(12, $tong, 'Dữ liệu mẫu cần > 12 sản phẩm Yonex để kiểm tra phân trang');
        $res->assertSee("({$tong} sản phẩm)", false);
        // Link sang trang 2 phải giữ nguyên từ khóa tìm kiếm
        $res->assertSee('keyword=Yonex&amp;page=2', false);

        $this->get('/SanPham/TimKiem?keyword=Yonex&page=2')->assertStatus(200)->assertSee('Yonex');
    }

    public function test_root_is_the_landing_page_with_real_product_content(): void
    {
        $res = $this->get('/');

        $res->assertStatus(200)
            ->assertSee('Bạn định chi bao nhiêu?')
            ->assertSee('Mua sắm theo nhu cầu')
            ->assertViewHas('thongKe', fn ($t) => $t['sanPham'] === SanPham::count())
            ->assertViewHas('nganSach', fn ($n) => $n->count() === 5 && $n->sum('soLuong') >= 1)
            ->assertViewHas('heroSp', fn ($h) => $h->count() === 3);

        // Các mức ngân sách dẫn sang bộ lọc giá của trang sản phẩm
        $res->assertSee('khoang=1000000-2000000', false);

        // Ảnh hero và ưu đãi là sản phẩm thật từ CSDL, có liên kết sang trang chi tiết
        $sp = $res->viewData('heroSp')->first();
        $res->assertSee($sp->TenSanPham)->assertSee('/Home/ChiTietSanPham/'.$sp->MaSanPham, false);

        // Đã bỏ hiệu ứng 3D: không nạp Three.js/canvas, menu không còn mục "Khám phá 3D"
        $res->assertDontSee('three.module', false)
            ->assertDontSee('heroCanvas', false)
            ->assertDontSee('KHÁM PHÁ 3D');

        // Route /Landing tự chuyển hướng về trang chủ
        $this->get('/Landing')->assertRedirect('/');

        // Trang cửa hàng truy cập bình thường
        $this->get('/Home/Index')->assertStatus(200);
    }

    public function test_accessories_category_has_products_with_real_photos(): void
    {
        $pk = SanPham::where('MaLoai', 4)->get();
        $this->assertGreaterThanOrEqual(3, $pk->count(), 'Nhóm Phụ kiện cần có dữ liệu mẫu');

        foreach ($pk as $sp) {
            $this->assertFileExists(public_path('images/'.$sp->HinhAnhDaiDien), 'Thiếu ảnh của '.$sp->TenSanPham);
            $this->assertGreaterThan(0, $sp->chiTietSanPhams()->where('SoLuongTon', '>', 0)->count());
        }

        $this->get('/SanPham/Index?loai=4')->assertStatus(200)->assertSee($pk->first()->TenSanPham);
        $this->get('/Home/ChiTietSanPham/'.$pk->first()->MaSanPham)->assertStatus(200)->assertSee($pk->first()->TenSanPham);
    }

    public function test_promotion_page_shows_programs_with_discounted_products(): void
    {
        $res = $this->get('/KhuyenMai/Index');

        $res->assertStatus(200)
            ->assertSee('Ưu đãi đang diễn ra')
            ->assertSee('promo-block', false);

        $sp = $res->viewData('products')->first();
        $res->assertSee($sp->TenSanPham);
    }

    public function test_price_filter_uses_price_after_promotion(): void
    {
        $min = 1000000;
        $max = 2000000;

        $res = $this->get("/SanPham/Index?gia_tu={$min}&gia_den={$max}&sap_xep=gia_tang");
        $res->assertStatus(200);

        // Mọi sản phẩm trả về đều phải có giá bán thực tế nằm trong khoảng lọc
        $items = $res->viewData('sanPhams')->items();
        $this->assertNotEmpty($items, 'Dữ liệu mẫu cần có sản phẩm trong khoảng 1–2 triệu');

        $prev = 0;
        foreach ($items as $sp) {
            $gia = (float) $sp->gia_sau_khuyen_mai;
            $this->assertGreaterThanOrEqual($min, $gia);
            $this->assertLessThanOrEqual($max, $gia);
            $this->assertGreaterThanOrEqual($prev, $gia, 'Sắp xếp giá tăng dần');
            $prev = $gia;
        }
    }

    public function test_brand_and_discount_filters(): void
    {
        $hang = Hang::where('TenHang', 'LIKE', '%Yonex%')->first();

        $res = $this->get("/SanPham/Index?hangs[]={$hang->MaHang}&giam_gia=1&con_hang=1");
        $res->assertStatus(200);

        foreach ($res->viewData('sanPhams')->items() as $sp) {
            $this->assertSame($hang->MaHang, $sp->MaHang);
            $this->assertLessThan((float) $sp->GiaGoc, (float) $sp->gia_sau_khuyen_mai);
        }

        // Tham số URL ?hang=Yonex tự tick sẵn ô hãng tương ứng
        $this->get('/SanPham/Index?hang=Yonex')->assertStatus(200)->assertViewHas('locHang', [$hang->MaHang]);
    }

    public function test_admin_pages_load_for_admin(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin')->assertStatus(200)->assertSee('Dashboard Thống Kê');
        $this->actingAs($admin)->get('/admin/san-pham')->assertStatus(200)->assertSee('Danh Sách Sản Phẩm');
        $this->actingAs($admin)->get('/admin/san-pham/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/san-pham/1')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/san-pham/1/edit')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/san-pham/1/delete')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/bien-the/1/edit')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/don-hang')->assertStatus(200)->assertSee('Quản Lý Đơn Hàng');
        $this->actingAs($admin)->get('/admin/don-hang/1')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/tai-khoan')->assertStatus(200)->assertSee('Quản Lý Tài Khoản');
        $this->actingAs($admin)->get('/admin/tai-khoan/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/tai-khoan/3/edit')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/tai-khoan/3/delete')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/khuyen-mai')->assertStatus(200)->assertSee('Quản Lý Chương Trình Khuyến Mãi');
        $this->actingAs($admin)->get('/admin/khuyen-mai/create')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/khuyen-mai/1/edit')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/khuyen-mai/1/delete')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/thong-bao')->assertStatus(200)->assertSee('Cảnh Báo Phản Hồi');
    }

    public function test_admin_product_search_keeps_keyword_when_paging(): void
    {
        $res = $this->actingAs($this->admin())->get('/admin/san-pham?keyword=Yonex');

        $res->assertStatus(200);
        $res->assertSee('value="Yonex"', false);
        $res->assertSee('keyword=Yonex&amp;page=2', false);
    }

    public function test_chatbot_endpoint_responds_json(): void
    {
        $this->postJson('/Chatbot/Ask', ['message' => 'Shop có vợt Yonex dưới 2 triệu không?'])
            ->assertStatus(200)
            ->assertJsonStructure(['success', 'reply']);
    }
}
