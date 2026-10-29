<?php

namespace Tests\Feature;

use App\Models\ChiTietSanPham;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Luồng khách hàng: đăng ký, đăng nhập/đăng xuất, giỏ hàng, đặt hàng, hủy đơn, đánh giá, thanh toán MoMo.
 */
class KhachHangTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_register_login_and_logout(): void
    {
        $this->post('/TaiKhoan/DangKy', [
            'HoTen' => 'Khách Mới', 'TenDangNhap' => 'khachmoi', 'Email' => 'khachmoi@example.com',
            'MatKhau' => '123456', 'XacNhanMatKhau' => '123456',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('TaiKhoan', ['TenDangNhap' => 'khachmoi', 'MaQuyen' => TaiKhoan::QUYEN_KHACHHANG]);

        $this->post('/TaiKhoan/DangXuat')->assertRedirect(route('home'));
        $this->assertGuest();

        $this->post('/TaiKhoan/DangNhap', ['TenDangNhap' => 'khachmoi', 'MatKhau' => 'saimatkhau'])
            ->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->post('/TaiKhoan/DangNhap', ['TenDangNhap' => 'khachmoi', 'MatKhau' => '123456'])
            ->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_register_validation_messages(): void
    {
        $this->post('/TaiKhoan/DangKy', [
            'HoTen' => '', 'TenDangNhap' => 'admin', 'Email' => 'khong-hop-le',
            'MatKhau' => '123', 'XacNhanMatKhau' => '456',
        ])->assertSessionHasErrors([
            'HoTen' => 'Vui lòng nhập họ tên.',
            'TenDangNhap' => 'Tên đăng nhập đã tồn tại.',
            'Email' => 'Email phải là địa chỉ email hợp lệ.',
            'XacNhanMatKhau' => 'Xác nhận mật khẩu không khớp.',
        ]);
    }

    public function test_login_page_has_password_visibility_button(): void
    {
        $this->get(route('taikhoan.dangnhap'))
            ->assertOk()
            ->assertSee('type="password"', false)
            ->assertSee('type="button" id="togglePassword"', false)
            ->assertSee('aria-label="Hiện mật khẩu"', false)
            ->assertSee('aria-controls="MatKhau"', false);
    }

    public function test_customer_order_pages_only_show_cancel_for_allowed_statuses(): void
    {
        $kh = $this->khachHang();
        $don = DonHang::where('MaKhachHang', $kh->khachHang->MaKhachHang)->firstOrFail();
        $cancelAction = 'action="'.route('donhang.huydonhang', ['id' => $don->MaDonHang]).'"';

        foreach (['Chờ xác nhận', 'Chờ thanh toán MoMo', 'Đang xử lý', 'Đang giao hàng', 'Đã thanh toán bằng MoMo', 'Đã hoàn thành', 'Đã hủy'] as $status) {
            $don->update(['TrangThai' => $status]);

            foreach (['donhang.index', 'donhang.details'] as $page) {
                $response = $this->actingAs($kh)->get(route($page, ['id' => $don->MaDonHang]))->assertOk();

                if (in_array($status, ['Chờ xác nhận', 'Chờ thanh toán MoMo', 'Đang xử lý'], true)) {
                    $response->assertSee($cancelAction, false);
                } else {
                    $response->assertDontSee($cancelAction, false);
                }
            }
        }
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $this->post('/TaiKhoan/DangNhap', ['TenDangNhap' => 'admin', 'MatKhau' => 'admin@123'])
            ->assertRedirect(route('quanlysanpham.dashboard'));
    }

    public function test_open_redirect_is_blocked(): void
    {
        $this->post('/TaiKhoan/DangNhap', ['TenDangNhap' => 'nguyenvana', 'MatKhau' => 'vana@123', 'returnUrl' => '//evil.example.com'])
            ->assertRedirect(route('home'));
    }

    public function test_admin_login_ignores_customer_return_url(): void
    {
        $this->post('/TaiKhoan/DangNhap', [
            'TenDangNhap' => 'admin', 'MatKhau' => 'admin@123', 'returnUrl' => '/Home/ViewCart',
        ])->assertRedirect(route('quanlysanpham.dashboard'));
    }

    public function test_add_to_cart_checkout_and_cancel_order_restores_stock(): void
    {
        $kh = $this->khachHang();
        $bienThe = ChiTietSanPham::findOrFail(1);
        $tonBanDau = $bienThe->SoLuongTon;

        $this->actingAs($kh)->post('/Home/AddToCart', ['chiTietId' => 1, 'quantity' => 2])->assertSessionHas('message');
        $this->actingAs($kh)->get('/Home/ViewCart')->assertStatus(200);
        $this->actingAs($kh)->get('/DonHang/Checkout')->assertStatus(200);

        // Validation thông tin giao hàng
        $this->actingAs($kh)->post('/DonHang/Checkout', ['HoTenNhan' => '', 'SoDienThoaiNhan' => 'abc', 'DiaChiGiao' => ''])
            ->assertSessionHasErrors(['HoTenNhan', 'SoDienThoaiNhan', 'DiaChiGiao']);

        $this->actingAs($kh)->post('/DonHang/Checkout', [
            'HoTenNhan' => 'Nguyễn Văn A', 'SoDienThoaiNhan' => '0901234567', 'DiaChiGiao' => '123 Nguyễn Trãi',
        ])->assertRedirect(route('donhang.index'))->assertSessionHas('message');

        $don = DonHang::where('MaKhachHang', $kh->khachHang->MaKhachHang)->latest('MaDonHang')->firstOrFail();
        $this->assertSame('Đang xử lý', $don->TrangThai);
        $this->assertSame($tonBanDau - 2, $bienThe->fresh()->SoLuongTon);
        $this->assertDatabaseCount('GioHang', 0);

        $this->actingAs($kh)->post("/DonHang/HuyDonHang/{$don->MaDonHang}")->assertSessionHas('message');
        $this->assertSame('Đã hủy', $don->fresh()->TrangThai);
        $this->assertSame($tonBanDau, $bienThe->fresh()->SoLuongTon);

        // Hủy lần 2 không được hoàn kho thêm
        $this->actingAs($kh)->post("/DonHang/HuyDonHang/{$don->MaDonHang}")->assertSessionHas('error');
        $this->assertSame($tonBanDau, $bienThe->fresh()->SoLuongTon);
    }

    public function test_remove_item_from_cart(): void
    {
        $kh = $this->khachHang();
        $this->actingAs($kh)->post('/Home/AddToCart', ['chiTietId' => 1, 'quantity' => 1]);
        $maGioHang = GioHang::firstOrFail()->MaGioHang;

        $this->actingAs($kh)->post("/Home/RemoveFromCart/{$maGioHang}")->assertRedirect(route('home.viewcart'));
        $this->assertDatabaseCount('GioHang', 0);
    }

    public function test_admin_order_status_transitions_are_validated(): void
    {
        $admin = $this->admin();
        $don = DonHang::where('TrangThai', 'Đã hoàn thành')->firstOrFail();

        $this->actingAs($admin)->post("/admin/don-hang/{$don->MaDonHang}/trang-thai", ['trangThaiMoi' => 'Đang xử lý'])
            ->assertSessionHas('error');
        $this->assertSame('Đã hoàn thành', $don->fresh()->TrangThai);

        $dangXuLy = DonHang::where('TrangThai', 'Đang xử lý')->firstOrFail();
        $this->actingAs($admin)->post("/admin/don-hang/{$dangXuLy->MaDonHang}/trang-thai", ['trangThaiMoi' => 'Đang giao hàng'])
            ->assertSessionHas('message');
        $this->assertSame('Đang giao hàng', $dangXuLy->fresh()->TrangThai);
    }

    public function test_review_requires_login_and_validates_input(): void
    {
        $this->get('/ThemPhanHoiSP/ReviewForm/1')->assertRedirectContains('/TaiKhoan/DangNhap');

        $kh = $this->khachHang();
        $this->actingAs($kh)->get('/ThemPhanHoiSP/ReviewForm/1')->assertStatus(200);
        $this->actingAs($kh)->post('/ThemPhanHoiSP/SubmitReview', ['maSP' => 1, 'danhGia' => 9, 'noiDung' => ''])
            ->assertSessionHasErrors(['danhGia', 'noiDung']);

        $this->actingAs($kh)->post('/ThemPhanHoiSP/SubmitReview', ['maSP' => 1, 'danhGia' => 4, 'noiDung' => 'Vợt rất tốt, đáng tiền'])
            ->assertRedirect(route('home.chitietsanpham', ['id' => 1]))->assertSessionHas('message');
        $this->assertDatabaseHas('PhanHoi', ['MaSanPham' => 1, 'DanhGia' => 4]);
    }

    public function test_momo_return_requires_valid_signature(): void
    {
        $don = DonHang::create([
            'NgayDat' => now(), 'TrangThai' => 'Chờ thanh toán MoMo', 'TongTien' => 100000, 'TongTienSauGiam' => 100000,
            'MaKhachHang' => $this->khachHang()->khachHang->MaKhachHang, 'TienGiam' => 0,
        ]);

        $data = [
            'partnerCode' => config('services.momo.partner_code'), 'orderId' => (string) $don->MaDonHang,
            'requestId' => 'req-1', 'amount' => '100000', 'orderInfo' => 'Thanh toan', 'orderType' => 'momo_wallet',
            'transId' => '123', 'resultCode' => '0', 'message' => 'Successful.', 'payType' => 'qr',
            'responseTime' => '1700000000000', 'extraData' => '',
        ];

        // Chữ ký giả -> không được cập nhật đơn
        $this->get('/DonHang/ThanhToanMomoSuccess?'.http_build_query($data + ['signature' => 'gia-mao']))->assertStatus(200);
        $this->assertSame('Chờ thanh toán MoMo', $don->fresh()->TrangThai);

        // Chữ ký hợp lệ -> đã thanh toán
        $raw = 'accessKey='.config('services.momo.access_key')
            ."&amount={$data['amount']}&extraData={$data['extraData']}&message={$data['message']}"
            ."&orderId={$data['orderId']}&orderInfo={$data['orderInfo']}&orderType={$data['orderType']}"
            ."&partnerCode={$data['partnerCode']}&payType={$data['payType']}&requestId={$data['requestId']}"
            ."&responseTime={$data['responseTime']}&resultCode={$data['resultCode']}&transId={$data['transId']}";
        $data['signature'] = hash_hmac('sha256', $raw, config('services.momo.secret_key'));

        $this->get('/DonHang/ThanhToanMomoSuccess?'.http_build_query($data))->assertStatus(200);
        $this->assertSame('Đã thanh toán bằng MoMo', $don->fresh()->TrangThai);
    }
}
