<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Dữ liệu mẫu cho "Bảng tin cầu lông": kết quả trận đấu và bài viết có thật.
 *
 * Mọi kết quả/tỉ số dưới đây đã được đối chiếu với từ hai nguồn công khai trở lên (xem cột LienKetNguon
 * của từng trận); nội dung bài viết do nhóm tự diễn đạt lại, không sao chép nguyên văn.
 * Chạy lại nhiều lần không bị trùng:  php artisan db:seed --class=BanTinSeeder
 */
class BanTinSeeder extends Seeder
{
    private const WIKI_2026 = 'https://en.wikipedia.org/wiki/2026_BWF_World_Championships';
    private const WIKI_2025 = 'https://en.wikipedia.org/wiki/2025_BWF_World_Championships';
    private const WIKI_PARIS = 'https://en.wikipedia.org/wiki/Badminton_at_the_2024_Summer_Olympics';

    public function run(): void
    {
        $tacGia = DB::table('TaiKhoan')->where('MaQuyen', 1)->value('MaTaiKhoan');

        /* ---------------- Trận đấu ---------------- */
        $g26 = ['Giải vô địch thế giới BWF 2026', 'New Delhi (Ấn Độ)', '2026-08-23', 'Badminton Planet', 'https://www.badmintonplanet.com/?p=26517'];
        $g25 = ['Giải vô địch thế giới BWF 2025', 'Paris (Pháp)', '2025-08-31'];
        $og24 = ['Olympic Paris 2024', 'Paris (Pháp)'];

        $tran = [
            'ms26' => [$g26[0], 'Chung kết', 'Đơn nam', 'Alex Lanier (Pháp)', 'Kodai Naraoka (Nhật Bản)', '21-11, 21-11', 'A', $g26[2], $g26[1], 'Badminton Planet', 'https://www.badmintonplanet.com/?p=26517'],
            'ws26' => [$g26[0], 'Chung kết', 'Đơn nữ', 'An Se-young (Hàn Quốc)', 'Akane Yamaguchi (Nhật Bản)', '21-17, 21-14', 'A', $g26[2], $g26[1], 'Badminton Planet', 'https://www.badmintonplanet.com/?p=26512'],
            'md26' => [$g26[0], 'Chung kết', 'Đôi nam', 'Liang Weikeng / Wang Chang (Trung Quốc)', 'Aaron Chia / Soh Wooi Yik (Malaysia)', '21-17, 21-16', 'A', $g26[2], $g26[1], 'Outlook India', 'https://www.outlookindia.com/sports/badminton/bwf-world-championships-2026-liang-weikeng-wang-chang-vs-aaron-chia-soh-wooi-yikmens-doubles-final-new-delhi-in-pics'],
            'xd26' => [$g26[0], 'Chung kết', 'Đôi nam nữ', 'Thom Gicquel / Delphine Delrue (Pháp)', 'Feng Yanzhe / Huang Dongping (Trung Quốc)', '22-20, 19-21, 21-15', 'A', $g26[2], $g26[1], 'Wikipedia', self::WIKI_2026],
            'wd26' => [$g26[0], 'Chung kết', 'Đôi nữ', 'Baek Ha-na / Lee So-hee (Hàn Quốc)', 'Liu Shengshu / Tan Ning (Trung Quốc)', null, 'A', $g26[2], $g26[1], 'Wikipedia', self::WIKI_2026],

            'ms25' => [$g25[0], 'Chung kết', 'Đơn nam', 'Shi Yuqi (Trung Quốc)', 'Kunlavut Vitidsarn (Thái Lan)', '19-21, 21-10, 21-18', 'A', $g25[2], $g25[1], 'CGTN', 'https://news.cgtn.com/news/2025-09-01/Shi-holds-off-Vitidsarn-to-win-maiden-badminton-world-title-1Gj5F2WR1UQ/share_amp.html'],
            'ws25' => [$g25[0], 'Chung kết', 'Đơn nữ', 'Akane Yamaguchi (Nhật Bản)', 'Chen Yufei (Trung Quốc)', '21-9, 21-13', 'A', $g25[2], $g25[1], 'Wikipedia', self::WIKI_2025],
            'md25' => [$g25[0], 'Chung kết', 'Đôi nam', 'Kim Won-ho / Seo Seung-jae (Hàn Quốc)', 'Chen Boyang / Liu Yi (Trung Quốc)', '21-17, 21-12', 'A', $g25[2], $g25[1], 'Korea JoongAng Daily', 'https://www.koreajoongangdaily.com/sports/seo-seung-jae-kim-won-ho-win-mens-doubles-title-at-badminton-worlds/12382401'],

            'ms24' => [$og24[0], 'Chung kết', 'Đơn nam', 'Viktor Axelsen (Đan Mạch)', 'Kunlavut Vitidsarn (Thái Lan)', '21-11, 21-11', 'A', '2024-08-05', $og24[1], 'Wikipedia', 'https://en.wikipedia.org/wiki/Badminton_at_the_2024_Summer_Olympics_%E2%80%93_Men%27s_singles'],
            'ws24' => [$og24[0], 'Chung kết', 'Đơn nữ', 'An Se-young (Hàn Quốc)', 'He Bingjiao (Trung Quốc)', '21-13, 21-16', 'A', '2024-08-05', $og24[1], 'Badminton Asia', 'https://badmintonasia.org/2024/08/05/paris-olympics-2024-an-se-young-clinches-historic-gold-ends-koreas-28-year-wait/'],
            'md24' => [$og24[0], 'Chung kết', 'Đôi nam', 'Lee Yang / Wang Chi-lin (Đài Bắc Trung Hoa)', 'Liang Weikeng / Wang Chang (Trung Quốc)', '21-17, 18-21, 21-19', 'A', '2024-08-04', $og24[1], 'ESPN', 'https://www.espn.com/olympics/story/_/id/40724728/taiwan-duo-defends-badminton-men-doubles-gold-olympics'],
        ];

        $idTran = [];
        foreach ($tran as $key => [$giai, $vong, $nd, $a, $b, $tiSo, $thang, $ngay, $diaDiem, $nguon, $link]) {
            DB::table('TranDau')->updateOrInsert(
                ['TenGiaiDau' => $giai, 'Vong' => $vong, 'NoiDung' => $nd],
                ['DoiA' => $a, 'DoiB' => $b, 'TiSo' => $tiSo, 'DoiThang' => $thang, 'NgayThiDau' => $ngay,
                    'DiaDiem' => $diaDiem, 'NguonTin' => $nguon, 'LienKetNguon' => $link]
            );
            $idTran[$key] = DB::table('TranDau')
                ->where(['TenGiaiDau' => $giai, 'Vong' => $vong, 'NoiDung' => $nd])->value('MaTranDau');
        }

        $sp = fn (string $ten) => DB::table('SanPham')->where('TenSanPham', $ten)->value('MaSanPham');

        /* ---------------- Bài viết ---------------- */
        $tin = [
            [
                'tieu_de' => 'Alex Lanier viết nên lịch sử: tay vợt Pháp đầu tiên vô địch thế giới đơn nam',
                'tom_tat' => 'Hạt giống số 9 thắng Kodai Naraoka 21-11, 21-11 ở chung kết giải vô địch thế giới 2026 tại New Delhi.',
                'noi_dung' => "Tại Indira Gandhi Arena (New Delhi), Alex Lanier, hạt giống số 9, đánh bại Kodai Naraoka (hạt giống số 10 của Nhật Bản) với tỉ số 21-11, 21-11 sau khoảng 50 phút để trở thành nhà vô địch thế giới đơn nam đầu tiên của cầu lông Pháp.\n\nTrên đường vào chung kết, Lanier có màn lội ngược dòng ở bán kết trước Victor Lai (Canada) với tỉ số 16-21, 21-13, 21-14. Cùng ngày, Pháp còn có thêm tấm huy chương vàng đôi nam nữ nhờ cặp Thom Gicquel/Delphine Delrue, lần đầu tiên nước này đăng quang ở nội dung này.",
                'loai' => 'tran-dau', 'tran' => 'ms26', 'ngay' => '2026-08-24 09:00:00', 'noi_bat' => true,
                'nguon' => 'Badminton Planet', 'link' => 'https://www.badmintonplanet.com/?p=26517',
            ],
            [
                'tieu_de' => 'An Se-young lần thứ hai lên ngôi đơn nữ thế giới',
                'tom_tat' => 'Tay vợt số 1 thế giới thắng Akane Yamaguchi 21-17, 21-14 và không để thua ván nào suốt giải.',
                'noi_dung' => "An Se-young bảo vệ vị thế số 1 thế giới bằng chiếc huy chương vàng thế giới thứ hai trong sự nghiệp (sau lần đầu năm 2023). Ở chung kết ngày 23/8/2026, cô thắng Akane Yamaguchi 21-17, 21-14.\n\nĐây là chiến thắng thứ sáu liên tiếp của An trước Yamaguchi, nâng thành tích đối đầu lên 20-15 nghiêng về tay vợt Hàn Quốc. Theo các báo cáo, An vượt qua giải đấu mà không thua ván nào dù đang vướng chấn thương ở chân.",
                'loai' => 'tran-dau', 'tran' => 'ws26', 'ngay' => '2026-08-24 10:00:00', 'noi_bat' => false,
                'nguon' => 'Badminton Planet', 'link' => 'https://www.badmintonplanet.com/?p=26512',
            ],
            [
                'tieu_de' => 'Liang Weikeng/Wang Chang đăng quang đôi nam thế giới 2026',
                'tom_tat' => 'Cặp đôi Trung Quốc thắng Aaron Chia/Soh Wooi Yik 21-17, 21-16 trong trận chung kết chỉ khoảng 35 phút.',
                'noi_dung' => "Liang Weikeng và Wang Chang (hạt giống số 3) thắng Aaron Chia/Soh Wooi Yik (hạt giống số 4, Malaysia) với tỉ số 21-17, 21-16 ở chung kết đôi nam. Trước đó, cặp Malaysia đã gây bất ngờ khi đánh bại cặp số 1 thế giới Kim Won-ho/Seo Seung-jae ở bán kết (21-17, 21-18).\n\nChiến thắng giúp Liang/Wang nới rộng thành tích đối đầu với Chia/Soh lên 12-3.",
                'loai' => 'tran-dau', 'tran' => 'md26', 'ngay' => '2026-08-24 11:00:00', 'noi_bat' => false,
                'nguon' => 'Outlook India', 'link' => 'https://www.outlookindia.com/sports/badminton/bwf-world-championships-2026-liang-weikeng-wang-chang-vs-aaron-chia-soh-wooi-yikmens-doubles-final-new-delhi-in-pics',
            ],
            [
                'tieu_de' => 'Vô địch thế giới 2026 khép lại: Hàn Quốc và Pháp cùng giành 2 huy chương vàng',
                'tom_tat' => 'Giải diễn ra từ 17 đến 23/8/2026 tại New Delhi, năm nội dung có năm nhà vô địch đến từ ba quốc gia.',
                'noi_dung' => "Giải vô địch thế giới BWF 2026 (lần thứ 30) diễn ra từ 17 đến 23/8/2026 tại Indira Gandhi Arena, New Delhi (Ấn Độ).\n\nCác nhà vô địch: đơn nam Alex Lanier (Pháp); đơn nữ An Se-young (Hàn Quốc); đôi nam Liang Weikeng/Wang Chang (Trung Quốc); đôi nữ Baek Ha-na/Lee So-hee (Hàn Quốc); đôi nam nữ Thom Gicquel/Delphine Delrue (Pháp).\n\nBảng tổng sắp huy chương: Hàn Quốc 2 vàng - 0 bạc - 1 đồng, Pháp 2-0-0, Trung Quốc 1 vàng - 2 bạc - 3 đồng.",
                'loai' => 'giai-dau', 'tran' => null, 'ngay' => '2026-08-25 08:00:00', 'noi_bat' => true,
                'nguon' => 'Wikipedia', 'link' => self::WIKI_2026,
            ],
            [
                'tieu_de' => 'Nhìn lại giải vô địch thế giới 2025 tại Paris',
                'tom_tat' => 'Shi Yuqi lần đầu vô địch thế giới sau trận chung kết ba ván với Kunlavut Vitidsarn.',
                'noi_dung' => "Giải vô địch thế giới BWF 2025 diễn ra tại Adidas Arena (Paris). Ở chung kết ngày 31/8/2025, Shi Yuqi (Trung Quốc) thắng đương kim vô địch Kunlavut Vitidsarn 19-21, 21-10, 21-18 sau 1 giờ 17 phút để lần đầu đăng quang.\n\nAkane Yamaguchi giành chức vô địch đơn nữ thứ ba (21-9, 21-13 trước Chen Yufei). Đôi nam thuộc về Kim Won-ho/Seo Seung-jae (Hàn Quốc) sau chiến thắng 21-17, 21-12 trước Chen Boyang/Liu Yi. Liu Shengshu/Tan Ning vô địch đôi nữ, còn Chen Tang Jie/Toh Ee Wei (Malaysia) vô địch đôi nam nữ.",
                'loai' => 'giai-dau', 'tran' => 'ms25', 'ngay' => '2025-09-01 08:00:00', 'noi_bat' => false,
                'nguon' => 'Wikipedia', 'link' => self::WIKI_2025,
            ],
            [
                'tieu_de' => 'Olympic Paris 2024: danh sách các nhà vô địch cầu lông',
                'tom_tat' => 'Năm nội dung, năm nhà vô địch: Axelsen, An Se-young, Lee/Wang, Chen/Jia và Zheng/Huang.',
                'noi_dung' => "Môn cầu lông Olympic Paris 2024 diễn ra từ 27/7 đến 5/8/2024 tại Porte de La Chapelle Arena (Paris).\n\nHuy chương vàng: đơn nam Viktor Axelsen (Đan Mạch); đơn nữ An Se-young (Hàn Quốc); đôi nam Lee Yang/Wang Chi-lin (Đài Bắc Trung Hoa); đôi nữ Chen Qingchen/Jia Yifan (Trung Quốc); đôi nam nữ Zheng Siwei/Huang Yaqiong (Trung Quốc). Trung Quốc có hai huy chương vàng ở nội dung đôi nữ và đôi nam nữ.",
                'loai' => 'giai-dau', 'tran' => null, 'ngay' => '2024-08-06 08:00:00', 'noi_bat' => false,
                'nguon' => 'Wikipedia', 'link' => self::WIKI_PARIS,
            ],
            [
                'tieu_de' => 'Hồ sơ tuyển thủ: An Se-young, tay vợt số 1 thế giới',
                'tom_tat' => 'Huy chương vàng Olympic Paris 2024, hai lần vô địch thế giới (2023, 2026).',
                'noi_dung' => "An Se-young (Hàn Quốc) hiện là tay vợt đơn nữ số 1 thế giới. Tại Olympic Paris 2024, cô thắng He Bingjiao 21-13, 21-16 ở chung kết ngày 5/8/2024, trở thành tay vợt nữ Hàn Quốc thứ hai giành vàng Olympic đơn nữ, sau Bang Soo-hyun năm 1996.\n\nVới chức vô địch thế giới 2023 và 2026, An có hai danh hiệu thế giới. Lối chơi được nhắc đến nhiều nhờ khả năng phòng thủ bền bỉ và giữ nhịp độ trận đấu.",
                'loai' => 'tuyen-thu', 'tran' => 'ws24', 'ngay' => '2026-08-26 08:00:00', 'noi_bat' => false,
                'nguon' => 'Badminton Asia', 'link' => 'https://badmintonasia.org/2024/08/05/paris-olympics-2024-an-se-young-clinches-historic-gold-ends-koreas-28-year-wait/',
            ],
            [
                'tieu_de' => 'Viktor Axelsen bảo vệ thành công huy chương vàng Olympic đơn nam',
                'tom_tat' => 'Tay vợt Đan Mạch thắng Kunlavut Vitidsarn 21-11, 21-11 và không thua ván nào suốt giải.',
                'noi_dung' => "Tại Paris 2024, Viktor Axelsen (Đan Mạch) giữ vững ngôi vô địch Olympic đơn nam sau khi thắng Kunlavut Vitidsarn (Thái Lan) 21-11, 21-11 ở trận chung kết. Anh không để thua ván nào trong cả giải đấu. Huy chương đồng thuộc về Lee Zii Jia (Malaysia).",
                'loai' => 'tuyen-thu', 'tran' => 'ms24', 'ngay' => '2024-08-06 09:00:00', 'noi_bat' => false,
                'nguon' => 'Wikipedia', 'link' => 'https://en.wikipedia.org/wiki/Badminton_at_the_2024_Summer_Olympics_%E2%80%93_Men%27s_singles',
            ],
            [
                'tieu_de' => 'Yonex Astrox 100ZZ Kurenai: cây vợt thiên công màu đỏ thẫm',
                'tom_tat' => 'Phiên bản nâng cấp dành cho người chơi thiên về tấn công, đang được quan tâm nhiều tại cửa hàng.',
                'noi_dung' => "Yonex Astrox 100ZZ Kurenai là phiên bản nâng cấp của Astrox 100ZZ, nổi bật với màu đỏ thẫm và thiết kế cải tiến cho lối chơi tấn công mạnh mẽ.\n\nNếu bạn thích những cú đập dứt điểm và chủ động dồn điểm, đây là dòng vợt đầu nặng đáng cân nhắc. Hãy xem chi tiết và các tùy chọn trọng lượng ngay bên dưới.",
                'loai' => 'san-pham-hot', 'tran' => null, 'sp' => 'Vợt Cầu Lông Yonex Astrox 100ZZ', 'ngay' => '2026-10-20 08:00:00', 'noi_bat' => true,
                'nguon' => 'Cửa Hàng Cầu Lông', 'link' => null,
            ],
            [
                'tieu_de' => 'Yonex Nanoflare 1000Z: nhẹ đầu, tốc độ cao cho các pha phản tạt',
                'tom_tat' => 'Dòng vợt nhẹ đầu tập trung vào tốc độ vung vợt, hợp với lối chơi nhanh ở lưới.',
                'noi_dung' => "Nanoflare 1000Z thuộc nhóm vợt nhẹ đầu, cho tốc độ vung nhanh và phản tạt linh hoạt. Đây là lựa chọn quen thuộc với người chơi đôi, hay đứng gần lưới và thích đổi nhịp liên tục.",
                'loai' => 'san-pham-hot', 'tran' => null, 'sp' => 'Vợt Cầu Lông Yonex Nanoflare 1000Z', 'ngay' => '2026-10-15 08:00:00', 'noi_bat' => false,
                'nguon' => 'Cửa Hàng Cầu Lông', 'link' => null,
            ],
            [
                'tieu_de' => 'Phụ kiện mới về: cầu lông lông vịt thi đấu (ống 12 quả)',
                'tom_tat' => 'Quả cầu lông vịt đầu cork bọc da, quỹ đạo bay ổn định cho thi đấu và tập luyện thường xuyên.',
                'noi_dung' => "Cửa hàng vừa bổ sung nhóm Phụ kiện cầu lông. Cầu lông lông vịt thi đấu (ống 12 quả) có đầu cork bọc da, quỹ đạo bay ổn định, phù hợp thi đấu và tập luyện thường xuyên. Đang áp dụng khuyến mãi theo chương trình hiện hành.",
                'loai' => 'san-pham-moi', 'tran' => null, 'sp' => 'Cầu Lông Lông Vịt Thi Đấu (Ống 12 Quả)', 'ngay' => '2026-10-24 08:00:00', 'noi_bat' => false,
                'nguon' => 'Cửa Hàng Cầu Lông', 'link' => null,
            ],
            [
                'tieu_de' => 'Giày Yonex Eclipsion Z3: ổn định và chống lật cổ chân',
                'tom_tat' => 'Mẫu giày thiên về độ ổn định, phù hợp người hay di chuyển nhanh và đổi hướng liên tục.',
                'noi_dung' => "Giày cầu lông Yonex Eclipsion Z3 tăng cường độ ổn định và hạn chế lật cổ chân khi di chuyển, phù hợp với người chơi cần cảm giác chắc chân trong các pha đổi hướng. Sản phẩm đang có ưu đãi theo chương trình khuyến mãi của cửa hàng.",
                'loai' => 'san-pham-moi', 'tran' => null, 'sp' => 'Giày Cầu Lông Yonex Eclipsion Z3', 'ngay' => '2026-10-18 08:00:00', 'noi_bat' => false,
                'nguon' => 'Cửa Hàng Cầu Lông', 'link' => null,
            ],
        ];

        foreach ($tin as $t) {
            DB::table('TinTuc')->updateOrInsert(
                ['TieuDe' => $t['tieu_de']],
                [
                    'TomTat' => $t['tom_tat'],
                    'NoiDung' => $t['noi_dung'],
                    'LoaiTin' => $t['loai'],
                    'MaSanPham' => isset($t['sp']) ? $sp($t['sp']) : null,
                    'MaTranDau' => $t['tran'] ? $idTran[$t['tran']] : null,
                    'MaTaiKhoan' => $tacGia,
                    'NguonTin' => $t['nguon'],
                    'LienKetNguon' => $t['link'],
                    'NoiBat' => $t['noi_bat'],
                    'NgayDang' => $t['ngay'],
                    'deleted_at' => null,
                ]
            );
        }
    }
}
