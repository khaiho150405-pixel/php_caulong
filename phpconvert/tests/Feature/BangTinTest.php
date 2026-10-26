<?php

namespace Tests\Feature;

use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Chức năng Bảng tin cầu lông: trang công khai, tìm kiếm/lọc/phân trang, CRUD quản trị và phân quyền.
 */
class BangTinTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function tinHopLe(array $ghiDe = []): array
    {
        return array_merge([
            'TieuDe' => 'Tin kiểm thử về giải đấu mới',
            'TomTat' => 'Tóm tắt ngắn cho tin kiểm thử.',
            'NoiDung' => 'Nội dung bài viết kiểm thử có độ dài đủ để qua kiểm tra hợp lệ.',
            'LoaiTin' => 'giai-dau',
            'NguonTin' => 'BWF',
            'LienKetNguon' => 'https://bwfbadminton.com',
        ], $ghiDe);
    }

    private function tranHopLe(array $ghiDe = []): array
    {
        return array_merge([
            'TenGiaiDau' => 'Giải kiểm thử', 'Vong' => 'Chung kết', 'NoiDung' => 'Đơn nam',
            'DoiA' => 'Người chơi A', 'DoiB' => 'Người chơi B',
            'TiSo' => '21-15, 21-18', 'DoiThang' => 'A', 'NgayThiDau' => '2026-08-23',
        ], $ghiDe);
    }

    /* ---------- Dữ liệu mẫu ---------- */

    public function test_seeded_data_is_consistent_and_sourced(): void
    {
        $this->assertGreaterThanOrEqual(8, TranDau::count());
        $this->assertGreaterThanOrEqual(8, TinTuc::count());

        // Trận có tỉ số thì phải có bên thắng và nguồn tham khảo
        foreach (TranDau::whereNotNull('TiSo')->get() as $td) {
            $this->assertContains($td->DoiThang, ['A', 'B'], "Trận #{$td->MaTranDau} thiếu bên thắng");
            $this->assertNotEmpty($td->LienKetNguon, "Trận #{$td->MaTranDau} thiếu nguồn");
        }
        // Tin trận đấu/tuyển thủ/giải đấu là tin thật nên phải có nguồn
        foreach (TinTuc::whereIn('LoaiTin', ['tran-dau', 'tuyen-thu', 'giai-dau'])->get() as $t) {
            $this->assertNotEmpty($t->LienKetNguon, "Bài '{$t->TieuDe}' thiếu nguồn");
        }
        // Có đủ tin sản phẩm hot/mới gắn với sản phẩm thật
        $this->assertGreaterThanOrEqual(2, TinTuc::whereNotNull('MaSanPham')->count());
    }

    /* ---------- Trang công khai ---------- */

    public function test_news_index_lists_filters_and_searches(): void
    {
        // Giao diện kiểu bảng tin: đầu trang, dải tin nhanh, bảng điểm, dòng thời gian theo tháng
        $this->get('/BangTin')->assertStatus(200)
            ->assertSee('class="bt-masthead', false)
            ->assertSee('Tin nhanh')
            ->assertSee('Bảng điểm')
            ->assertSee('Tin nổi bật')
            ->assertSee('class="bt-timeline"', false)
            ->assertSee('Sản phẩm đang hot')
            ->assertSee('Hàng mới về');

        // Lọc theo loại
        $res = $this->get('/BangTin?loai=tuyen-thu')->assertStatus(200);
        $items = $res->viewData('tins')->items();
        $this->assertNotEmpty($items);
        foreach ($items as $t) {
            $this->assertSame('tuyen-thu', $t->LoaiTin);
        }

        // Tìm kiếm theo từ khóa (có phân biệt kết quả)
        $this->get('/BangTin?keyword=Lanier')->assertStatus(200)->assertSee('Alex Lanier');
        $this->get('/BangTin?keyword=zzzkhongtontai')->assertStatus(200)->assertSee('Không có tin nào khớp');
    }

    public function test_news_detail_increments_views_once_per_session_and_shows_context(): void
    {
        $tin = TinTuc::where('LoaiTin', 'tran-dau')->whereNotNull('MaTranDau')->firstOrFail();
        $truoc = $tin->LuotXem;

        $this->get("/BangTin/{$tin->MaTin}")->assertStatus(200)
            ->assertSee($tin->TieuDe)
            ->assertSee($tin->tranDau->DoiA)
            ->assertSee('Nguồn tham khảo');
        $this->get("/BangTin/{$tin->MaTin}")->assertStatus(200); // cùng phiên: không tăng thêm

        $this->assertSame($truoc + 1, $tin->fresh()->LuotXem);

        // Bài gắn sản phẩm có thẻ dẫn sang trang chi tiết sản phẩm
        $spTin = TinTuc::whereNotNull('MaSanPham')->firstOrFail();
        $this->get("/BangTin/{$spTin->MaTin}")->assertStatus(200)
            ->assertSee('/Home/ChiTietSanPham/' . $spTin->MaSanPham, false);
    }

    public function test_scheduled_and_deleted_posts_are_hidden_from_public(): void
    {
        $hen = TinTuc::create($this->tinHopLe(['TieuDe' => 'Bài hẹn giờ tương lai', 'NgayDang' => now()->addDays(3)]));
        $this->get("/BangTin/{$hen->MaTin}")->assertNotFound();
        $this->get('/BangTin')->assertDontSee('Bài hẹn giờ tương lai');

        $xoa = TinTuc::create($this->tinHopLe(['TieuDe' => 'Bài đã xóa mềm']));
        $xoa->delete();
        $this->get("/BangTin/{$xoa->MaTin}")->assertNotFound();
    }

    public function test_landing_page_shows_news_teaser(): void
    {
        $this->get('/')->assertStatus(200)->assertSee('Tin nóng làng cầu lông')->assertSee('/BangTin', false);
    }

    /* ---------- Phân quyền ---------- */

    public function test_admin_news_pages_require_staff_or_admin(): void
    {
        $this->get('/admin/ban-tin')->assertRedirect();
        $this->get('/admin/tran-dau')->assertRedirect();

        $this->actingAs($this->khachHang())->get('/admin/ban-tin')->assertRedirect();

        $this->actingAs($this->nhanVien())->get('/admin/ban-tin')->assertStatus(200)->assertSee('Bài viết trên bảng tin');
        $this->actingAs($this->admin())->get('/admin/tran-dau')->assertStatus(200)->assertSee('Kết quả trận đấu');
    }

    /* ---------- CRUD bài viết ---------- */

    public function test_admin_can_create_update_and_soft_delete_a_post(): void
    {
        $admin = $this->admin();
        $sp = SanPham::firstOrFail();

        $this->actingAs($admin)->get('/admin/ban-tin/create')->assertStatus(200);

        $this->actingAs($admin)->post('/admin/ban-tin/create', $this->tinHopLe(['MaSanPham' => $sp->MaSanPham, 'NoiBat' => 1]))
            ->assertRedirect('/admin/ban-tin')->assertSessionHas('message');

        $tin = TinTuc::where('TieuDe', 'Tin kiểm thử về giải đấu mới')->firstOrFail();
        $this->assertSame($admin->MaTaiKhoan, $tin->MaTaiKhoan);
        $this->assertTrue($tin->NoiBat);
        $this->assertSame($sp->MaSanPham, $tin->MaSanPham);

        $this->actingAs($admin)->post("/admin/ban-tin/{$tin->MaTin}/edit", $this->tinHopLe(['TieuDe' => 'Tiêu đề đã chỉnh sửa']))
            ->assertRedirect('/admin/ban-tin')->assertSessionHas('message');
        $this->assertSame('Tiêu đề đã chỉnh sửa', $tin->fresh()->TieuDe);
        $this->assertFalse($tin->fresh()->NoiBat);

        $this->actingAs($admin)->get("/admin/ban-tin/{$tin->MaTin}/delete")->assertStatus(200)->assertSee('Tiêu đề đã chỉnh sửa');
        $this->actingAs($admin)->post("/admin/ban-tin/{$tin->MaTin}/delete")->assertRedirect('/admin/ban-tin')->assertSessionHas('message');
        $this->assertSoftDeleted('TinTuc', ['MaTin' => $tin->MaTin]);
    }

    public function test_post_form_validation_rejects_bad_input(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/ban-tin/create', $this->tinHopLe([
            'TieuDe' => '', 'NoiDung' => 'ngắn', 'LoaiTin' => 'khong-hop-le', 'LienKetNguon' => 'khong-phai-url', 'MaSanPham' => 999999,
        ]))->assertSessionHasErrors(['TieuDe', 'NoiDung', 'LoaiTin', 'LienKetNguon', 'MaSanPham']);

        $this->assertDatabaseMissing('TinTuc', ['LoaiTin' => 'khong-hop-le']);
    }

    public function test_admin_post_list_keeps_keyword_when_paginating(): void
    {
        $admin = $this->admin();
        foreach (range(1, 12) as $i) {
            TinTuc::create($this->tinHopLe(['TieuDe' => "Bài phân trang số {$i}"]));
        }

        $res = $this->actingAs($admin)->get('/admin/ban-tin?keyword=' . urlencode('Bài phân trang'))->assertStatus(200);
        $res->assertSee('keyword=B', false)->assertSee('page=2', false);
    }

    /* ---------- CRUD trận đấu ---------- */

    public function test_admin_can_manage_matches_and_deleting_keeps_related_posts(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe())
            ->assertRedirect('/admin/tran-dau')->assertSessionHas('message');
        $tran = TranDau::where('TenGiaiDau', 'Giải kiểm thử')->firstOrFail();

        $this->actingAs($admin)->post("/admin/tran-dau/{$tran->MaTranDau}/edit", $this->tranHopLe(['TiSo' => '21-10, 21-12', 'DoiThang' => 'B']))
            ->assertRedirect('/admin/tran-dau');
        $this->assertSame('B', $tran->fresh()->DoiThang);
        $this->assertSame('Người chơi B', $tran->fresh()->tenDoiThang);

        // Bài viết gắn với trận vẫn còn sau khi xóa trận
        $tin = TinTuc::create($this->tinHopLe(['TieuDe' => 'Bài gắn trận kiểm thử', 'MaTranDau' => $tran->MaTranDau]));
        $this->actingAs($admin)->get("/admin/tran-dau/{$tran->MaTranDau}/delete")->assertStatus(200)->assertSee('1');
        $this->actingAs($admin)->post("/admin/tran-dau/{$tran->MaTranDau}/delete")->assertRedirect('/admin/tran-dau');

        $this->assertDatabaseMissing('TranDau', ['MaTranDau' => $tran->MaTranDau]);
        $this->assertNull($tin->fresh()->MaTranDau);
        $this->assertNotNull($tin->fresh());
    }

    public function test_match_form_validation(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['DoiB' => 'Người chơi A']))
            ->assertSessionHasErrors(['DoiB']);
        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['TiSo' => '21 - 11 hai ván']))
            ->assertSessionHasErrors(['TiSo']);
        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['DoiThang' => null]))
            ->assertSessionHasErrors(['DoiThang']);
        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['NgayThiDau' => 'khong-phai-ngay', 'TenGiaiDau' => '']))
            ->assertSessionHasErrors(['NgayThiDau', 'TenGiaiDau']);

        // Trận chưa có tỉ số vẫn hợp lệ (lịch thi đấu sắp tới)
        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['TiSo' => null, 'DoiThang' => null, 'TenGiaiDau' => 'Giải sắp diễn ra']))
            ->assertSessionHasNoErrors()->assertRedirect('/admin/tran-dau');
    }
}
