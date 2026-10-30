<?php

namespace Tests\Feature;

use App\Models\SanPham;
use App\Models\TinTuc;
use App\Models\TranDau;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

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

    public function test_seeded_data_is_consistent_and_sourced(): void
    {
        $this->assertGreaterThanOrEqual(8, TranDau::count());
        $this->assertGreaterThanOrEqual(8, TinTuc::count());

        foreach (TranDau::whereNotNull('TiSo')->get() as $td) {
            $this->assertContains($td->DoiThang, ['A', 'B'], "Trận #{$td->MaTranDau} thiếu bên thắng");
            $this->assertNotEmpty($td->LienKetNguon, "Trận #{$td->MaTranDau} thiếu nguồn");
        }

        foreach (TinTuc::whereIn('LoaiTin', ['tran-dau', 'tuyen-thu', 'giai-dau'])->get() as $t) {
            $this->assertNotEmpty($t->LienKetNguon, "Bài '{$t->TieuDe}' thiếu nguồn");
        }

        $this->assertGreaterThanOrEqual(2, TinTuc::whereNotNull('MaSanPham')->count());
    }

    public function test_news_index_lists_filters_and_searches(): void
    {

        $this->get('/BangTin')->assertStatus(200)
            ->assertSee('Tin tức & sự kiện')
            ->assertSee('Kết quả thi đấu')
            ->assertSee('Tin nổi bật')
            ->assertSee('class="bt-timeline"', false)
            ->assertSee('Sản phẩm đang hot')
            ->assertSee('Hàng mới về');

        $res = $this->get('/BangTin?loai=tuyen-thu')->assertStatus(200);
        $items = $res->viewData('tins')->items();
        $this->assertNotEmpty($items);
        foreach ($items as $t) {
            $this->assertSame('tuyen-thu', $t->LoaiTin);
        }

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
        $this->get("/BangTin/{$tin->MaTin}")->assertStatus(200);

        $this->assertSame($truoc + 1, $tin->fresh()->LuotXem);

        $spTin = TinTuc::whereNotNull('MaSanPham')->firstOrFail();
        $this->get("/BangTin/{$spTin->MaTin}")->assertStatus(200)
            ->assertSee('/Home/ChiTietSanPham/'.$spTin->MaSanPham, false);
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
        $this->get('/')->assertStatus(200)->assertSee('Tin tức & sự kiện')->assertSee('/BangTin', false);
    }

    public function test_landing_keeps_results_visible_without_published_articles(): void
    {
        TinTuc::query()->delete();

        $this->get('/')->assertOk()->assertSee('Kết quả thi đấu')->assertSee('Bài viết đang được cập nhật.');
    }

    public function test_scoreboard_shows_each_teams_set_scores_in_the_correct_order(): void
    {
        $tran = new TranDau($this->tranHopLe(['TiSo' => '22-20, 19-21, 21-15']));
        $tran->setRelation('tinTucs', collect());

        $this->view('BangTin._bangdiem', ['ds' => collect([$tran])])
            ->assertSeeTextInOrder(['Người chơi A', '22', '19', '21', 'Người chơi B', '20', '21', '15'])
            ->assertSeeText('Thắng trận');
    }

    public function test_scoreboard_does_not_guess_scores_or_a_winner_for_an_unplayed_match(): void
    {
        $tran = new TranDau($this->tranHopLe(['TiSo' => null, 'DoiThang' => null]));
        $tran->setRelation('tinTucs', collect());

        $this->view('BangTin._bangdiem', ['ds' => collect([$tran])])
            ->assertSeeText('Chưa có kết quả')
            ->assertSeeText('Chưa cập nhật điểm từng ván.')
            ->assertSeeText('—')
            ->assertDontSeeText('Thắng trận');
    }

    public function test_scoreboard_handles_incomplete_legacy_scores_and_escapes_player_names(): void
    {
        $tran = new TranDau($this->tranHopLe(['TiSo' => '21-15, chưa cập nhật', 'DoiA' => '<script>alert(1)</script>']));
        $tran->setRelation('tinTucs', collect());

        $this->view('BangTin._bangdiem', ['ds' => collect([$tran])])
            ->assertSeeText('Điểm từng ván chưa đầy đủ.')
            ->assertSee('<script>alert(1)</script>')
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_admin_news_pages_require_staff_or_admin(): void
    {
        $this->get('/admin/ban-tin')->assertRedirect();
        $this->get('/admin/tran-dau')->assertRedirect();

        $this->actingAs($this->khachHang())->get('/admin/ban-tin')->assertRedirect();

        $this->actingAs($this->nhanVien())->get('/admin/ban-tin')->assertStatus(200)->assertSee('Bài viết trên bảng tin');
        $this->actingAs($this->admin())->get('/admin/tran-dau')->assertStatus(200)->assertSee('Kết quả trận đấu');
    }

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

        $res = $this->actingAs($admin)->get('/admin/ban-tin?keyword='.urlencode('Bài phân trang'))->assertStatus(200);
        $res->assertSee('keyword=B', false)->assertSee('page=2', false);
    }

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

        $this->actingAs($admin)->post('/admin/tran-dau/create', $this->tranHopLe(['TiSo' => null, 'DoiThang' => null, 'TenGiaiDau' => 'Giải sắp diễn ra']))
            ->assertSessionHasNoErrors()->assertRedirect('/admin/tran-dau');
    }

    public function test_upload_image_for_rich_text_editor(): void
    {
        $admin = $this->admin();
        $file = UploadedFile::fake()->create('editor-sample.jpg', 50, 'image/jpeg');

        $response = $this->actingAs($admin)->post('/admin/ban-tin/upload-image', [
            'upload' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'uploaded' => 1,
        ]);
        $this->assertNotEmpty($response->json('url'));
    }

    public function test_ai_generate_article_endpoint(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->postJson('/admin/ban-tin/ai-generate', [
            'topic' => 'Vợt cầu lông Yonex Astrox 100ZZ',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('content'));
    }

    public function test_toggling_noibat_makes_it_featured_and_hienthi_false_hides_it(): void
    {
        $admin = $this->admin();
        $tin = TinTuc::create($this->tinHopLe([
            'TieuDe' => 'Bài viết kiểm tra ẩn hiện',
            'NoiBat' => false,
            'HienThi' => true,
        ]));

        $this->actingAs($admin)->postJson("/admin/ban-tin/{$tin->MaTin}/toggle-status", [
            'field' => 'NoiBat',
            'value' => 1,
        ])->assertJson(['success' => true, 'value' => true]);

        $this->assertTrue($tin->fresh()->NoiBat);

        auth()->logout();

        $res = $this->get(route('bangtin.index'));
        $res->assertStatus(200);
        $res->assertSee('Bài viết kiểm tra ẩn hiện');

        $this->actingAs($admin)->postJson("/admin/ban-tin/{$tin->MaTin}/toggle-status", [
            'field' => 'HienThi',
            'value' => 0,
        ])->assertJson(['success' => true, 'value' => false]);

        $this->assertFalse($tin->fresh()->HienThi);

        auth()->logout();

        $res2 = $this->get(route('bangtin.index'));
        $res2->assertDontSee('Bài viết kiểm tra ẩn hiện');

        $this->get(route('bangtin.show', ['id' => $tin->MaTin]))->assertNotFound();
    }

    public function test_multiple_featured_articles_render_vertical_slider_on_bang_tin(): void
    {

        $tin1 = TinTuc::create($this->tinHopLe([
            'TieuDe' => 'Bài nổi bật trượt 1',
            'NoiBat' => true,
            'HienThi' => true,
            'ThuTu' => 1,
            'NgayDang' => now()->subMinutes(10),
        ]));
        $tin2 = TinTuc::create($this->tinHopLe([
            'TieuDe' => 'Bài nổi bật trượt 2',
            'NoiBat' => true,
            'HienThi' => true,
            'ThuTu' => 2,
            'NgayDang' => now()->subMinutes(5),
        ]));

        $res = $this->get(route('bangtin.index'))->assertStatus(200);

        $dsNoiBat = $res->viewData('dsNoiBat');
        $this->assertNotNull($dsNoiBat);
        $this->assertGreaterThanOrEqual(2, $dsNoiBat->count());

        $res->assertSee('id="featuredSliderBox"', false);
        $res->assertSee('id="featuredTrack"', false);
        $res->assertDontSee('Tự động trượt');
        $res->assertSee('Bài nổi bật trượt 1');
        $res->assertSee('Bài nổi bật trượt 2');

        $timelineIds = collect($res->viewData('tins')->items())->pluck('MaTin');
        $this->assertFalse($timelineIds->contains($tin1->MaTin));
        $this->assertFalse($timelineIds->contains($tin2->MaTin));

        $homeRes = $this->get('/')->assertStatus(200);
        $homeDsNoiBat = $homeRes->viewData('dsNoiBat');
        $this->assertNotNull($homeDsNoiBat);
        $this->assertGreaterThanOrEqual(2, $homeDsNoiBat->count());
        $homeRes->assertSee('id="featuredSliderBox"', false);
        $homeRes->assertSee('id="featuredTrack"', false);
        $homeRes->assertSee('Bài nổi bật trượt 1');
        $homeRes->assertSee('Bài nổi bật trượt 2');
    }

    public function test_article_form_does_not_contain_preview_button(): void
    {
        $admin = $this->admin();
        $tin = TinTuc::first();

        $res = $this->actingAs($admin)->get(route('quanlybantin.edit', ['id' => $tin->MaTin]));
        $res->assertStatus(200);
        $res->assertDontSee('Xem trước trên website');
    }
}
