# Đề xuất landing page HUIT Badminton

Ngày: 24/10/2026. Trạng thái: phương án để xem trước, chưa triển khai vào ứng dụng.

## Yêu cầu đã chốt với người dùng

- **3D là thành phần mặc định của landing page:** mô hình vợt tự tải và hiển thị khi truy cập, trên cả desktop và mobile có hỗ trợ WebGL; không cần bấm nút mở 3D hoặc cuộn đến một mục khác.
- **Landing là trang đầu tiên của website:** truy cập đường dẫn gốc `/` hiển thị landing, thay cho trang chủ bán hàng hiện tại.
- Tối ưu hiệu năng trong kiến trúc 3D mặc định. Ảnh poster chỉ hỗ trợ lúc tải hoặc khi có lỗi/không hỗ trợ 3D, không thay thế trải nghiệm 3D mặc định trên thiết bị hoạt động bình thường.
- Lần cập nhật này chỉ sửa plan; chưa đổi route hay triển khai giao diện ứng dụng.

## Quyết định thiết kế đề xuất

Đề xuất hướng **Thể thao biên tập**: hero xanh sân cầu, chữ lớn tương phản rõ, vợt 3D đúng hình dáng làm điểm nhấn mặc định; phần mua sắm dùng nền sáng, sản phẩm xuất hiện sớm. Thông điệp: **Chọn đúng vợt. Bứt phá từng pha cầu.**

Phương án **Mua sắm nhanh** dùng hero ngắn, danh mục ngay dưới header và thiên về tìm hàng; vẫn có vợt 3D tự hiển thị trong hero. Cả hai dùng cùng cấu trúc dữ liệu Laravel hiện có và cùng yêu cầu landing ở đường dẫn gốc.

Bản xem trước trước đó dùng ảnh có sẵn và giá gốc từ seeder để minh họa bố cục; chưa thể hiện yêu cầu 3D mặc định vừa cập nhật. Khi triển khai, thay vùng ảnh hero bằng cảnh 3D tự tải, giữ ảnh làm poster. Đây không phải dữ liệu giá, tồn kho hoặc khuyến mãi trực tiếp. Bộ chọn lối chơi chỉ minh họa tương tác; hệ thống gợi ý theo lối chơi chưa được triển khai. Không coi thao tác xem trước là thao tác trên giỏ hàng thật.

## Những gì đã kiểm tra

- Đọc Blade, CSS, JavaScript 3D, controller, layout chung, seeder và xem trực tiếp `/Landing` đang chạy trên localhost.
- PHP 8.3.30 và Composer 2.9.4 có sẵn trong Laragon, dù chưa nằm trong PATH.
- Chưa chạy Lighthouse, ghi Performance trace, đo FPS, LCP, INP hoặc kiểm tra trên điện thoại thật. Các vấn đề dưới đây là bằng chứng từ mã và quan sát giao diện, chưa phải benchmark.
- Không sửa route, Blade, CSS, JavaScript, dependency hay dữ liệu ứng dụng. Repository đã có nhiều thay đổi từ trước; giữ nguyên các thay đổi đó.

## Vấn đề và bằng chứng

| Vấn đề | Bằng chứng trong dự án | Tác động / hướng xử lý |
|---|---|---|
| Vợt mang dáng tennis | `public/js/landing/models.js:148–166`: đầu oval, hai thanh chéo hội tụ về thân tạo cổ chữ Y, thân tương đối ngắn | Loại cổ chữ Y; dựng lại mô hình theo mẫu vợt cầu lông cụ thể |
| Khởi tạo đồng loạt nhiều cảnh khi tải | Cuối `public/js/landing/landing3d.js` gọi `mountHero()`, toàn bộ canvas danh mục và `mountViewer()` | Chỉ tự khởi tạo cảnh hero 3D; danh mục dùng ảnh, bỏ cảnh viewer trùng lặp |
| Nhiều renderer và môi trường ánh sáng | Mỗi hero/danh mục/viewer tạo renderer, environment qua PMREM riêng | Với cấu hình 4 danh mục hiện tại có 4 renderer hiển thị: hero, vợt, cầu, viewer. Giảm còn tối đa 1 renderer cho hero tự hiển thị |
| Hero nặng về hiệu ứng | `landing3d.js:123–128`: EffectComposer + Bloom + OutputPass; thêm nhiều mô hình cầu, orbit và hạt sáng | Giữ vợt 3D làm chủ thể, bỏ Bloom, hạt và quỹ đạo tự chạy; tối giản ánh sáng/vật liệu |
| Nhiều công việc lúc cuộn | Lenis + ticker GSAP + ScrollTrigger + đường sao chổi blur + reveal chữ + đổi màu nền | Dùng cuộn trình duyệt; chỉ giữ chuyển động ngắn có mục đích |
| Chữ phụ thuộc khởi động hiệu ứng | Blade thêm `l-js`; CSS ẩn `.l-lines, .l-scrub`; có fallback 3 giây | Nội dung và CTA hiển thị từ HTML đầu tiên, JavaScript chỉ tăng cường |
| Người mua phải cuộn xa | Sản phẩm nằm sau giới thiệu, thống kê, danh mục và viewer | Đưa nhóm mua sắm và sản phẩm nổi bật lên ngay sau hero/thương hiệu |
| Ảnh lớn và fallback hỏng | `banner2.png` khoảng 3,23 MB; `default-product.jpg` và `default-category.jpg` đang 0 byte | Chuẩn bị ảnh theo kích thước hiển thị và fallback hợp lệ; tránh gán lặp URL ảnh lỗi |
| Nhãn chưa khớp truy vấn | “Được quan tâm nhất” nhưng controller ưu tiên có mã khuyến mãi rồi ngày tạo, không xếp theo lượt quan tâm | Dùng “Sản phẩm nổi bật”; chỉ dùng “Bán chạy” khi có truy vấn đơn hàng phù hợp |

Lưu ý: `createLoop()` **đã** dùng IntersectionObserver và Page Visibility để dừng render ngoài màn hình hoặc khi tab ẩn. Không quy kết rằng mọi canvas luôn render. Điểm cần cải thiện là khởi tạo đồng loạt nhiều cảnh, số renderer, độ nặng cảnh đang hiển thị và chuyển động liên tục khi không tương tác.

## Trang vào mặc định và điều hướng

Hiện tại `routes/web.php` đưa `/` tới `HomeController::index` với tên route `home`, còn `/Landing` tới `LandingController::index` với tên route `landing`. Kế hoạch cập nhật:

| Đường dẫn / liên kết | Hành vi sau triển khai |
|---|---|
| `/` — route `home` | Render trực tiếp `LandingController::index`, hiển thị landing với hero 3D tự tải; không cần qua trang chủ cũ hoặc thêm một bước chuyển hướng |
| `/Landing` — route `landing` | Giữ đường dẫn và tên route tương thích; chuyển hướng một lần về `/`, không tạo vòng lặp |
| `/Home/Index` | Giữ trang chủ bán hàng cũ ở đường dẫn riêng; có thể đặt tên route `shop.home` để liên kết rõ ràng |
| Logo và mục “Trang chủ” | Dẫn về `/`, tức landing mới |
| CTA “Khám phá vợt”, “Mua sắm”, mục “Sản phẩm” | Dẫn tới `sanpham.index` và bộ lọc tương ứng; người dùng vào danh mục mua sắm ngay |
| Link trực tiếp sản phẩm, giỏ hàng, checkout, tài khoản và `/admin` | Tiếp tục truy cập đúng trang đích; không ép mọi URL về landing |

- Landing ở `/` áp dụng cho cả khách chưa đăng nhập và người dùng đã đăng nhập khi chủ động vào đường dẫn gốc.
- Rà soát các chỗ dùng `route('home')`, `route('landing')`, redirect sau đăng nhập/đăng xuất và nút “Tiếp tục mua sắm”. Liên kết về trang chủ đi tới landing; liên kết tiếp tục mua sắm đi tới danh mục hoặc `/Home/Index` theo ngữ cảnh.
- Giữ cơ chế quay lại trang người dùng đang cần sau đăng nhập (intended URL), đặc biệt giỏ hàng/checkout; không chèn landing vào luồng thanh toán.
- Lấy `/` làm URL chính của landing; cập nhật metadata/canonical và kiểm tra route cache nếu ứng dụng đang dùng cache.

## UI đề xuất

### Hệ hình ảnh và màu sắc

- Xanh đậm `#123E32`: hero, mảng thương hiệu.
- Trắng ngà `#F6F7F2`: nền mua sắm; trắng cho nền ảnh sản phẩm.
- Xanh chanh `#D9F36A`: nút chính và một phần tiêu đề, dùng tiết chế.
- Màu chữ `#172F27`; chữ phụ `#5D6D63` trên nền sáng. Kiểm tra tương phản ở cả trạng thái hover/focus.
- Inter hoặc Segoe UI hiện có, hỗ trợ tiếng Việt; không thêm nhiều font hoặc nhiều độ đậm.
- Khung nội dung khoảng 1180–1240 px ở desktop; khoảng cách section 56–72 px, mobile 28–40 px.
- Ảnh sản phẩm trung thực, không kéo méo. Hero dùng một mô hình vợt 3D đúng tỉ lệ làm điểm nhấn, poster cùng góc nhìn hỗ trợ lúc tải. Không dùng Bloom hay nhiều phần tử trang trí cạnh tranh nhau.

### Trật tự nội dung

1. **Header gọn:** logo HUIT hiện có + tên cửa hàng; Sản phẩm, Thương hiệu, Khuyến mãi; tìm kiếm, tài khoản, giỏ hàng. Mobile thu về logo, tìm kiếm, giỏ hàng và menu truy cập được bằng bàn phím.
2. **Hero:** thông điệp 2–3 dòng, mô tả ngắn, CTA chính “Khám phá vợt”, CTA phụ “Tìm theo lối chơi”. Bên phải là vợt 3D tự tải và tự hiển thị, có thể kéo xoay trực tiếp; mobile bố trí vùng 3D gọn cùng nội dung/CTA trong khu vực đầu trang. Không cần nút “Mở 3D”, không carousel tự chạy.
3. **Thương hiệu:** Yonex, Li-Ning, Victor, Mizuno dạng hàng nhỏ. Không có animation marquee.
4. **Danh mục + sản phẩm nổi bật:** bốn nhóm Vợt / Giày / Trang phục / Phụ kiện; 4 sản phẩm một hàng desktop, 2 ở mobile. Giá sau khuyến mãi, giá gốc gạch ngang chỉ khi thực sự được giảm; ảnh có kích thước xác định. Link tới chi tiết để chọn màu/size trước khi thêm giỏ.
5. **Chọn theo lối chơi:** phần hướng dẫn Tấn công / Kiểm soát / Linh hoạt, ngắn gọn. Chỉ biến thành bộ lọc khi có nguồn thông số và ánh xạ sản phẩm đã kiểm chứng. Phiên bản đầu có thể dùng thẻ hướng dẫn/link đến danh mục thay vì giả lập gợi ý tự động.
6. **Ưu đãi và thông tin cửa hàng:** hiển thị chương trình còn hiệu lực; chỉ ghi giao hàng/bảo hành/chính hãng theo chính sách đã xác nhận. Không tạo đánh giá, số người mua hoặc bảo chứng không có dữ liệu.
7. **Footer:** liên hệ, chính sách và liên kết tài khoản. Thống kê có thể thu gọn ở đây hoặc bỏ khỏi landing nếu không giúp quyết định mua hàng.

Tương tác 3D tích hợp ngay trong hero. Bỏ phòng trưng bày riêng và các canvas danh mục để tránh lặp nội dung và tăng tải GPU; danh mục dùng ảnh sản phẩm.

### Mobile và khả năng sử dụng

- Kiểm tra 360, 390, 768, 1024 và 1440 px; không tràn ngang.
- Giữ tiêu đề đọc rõ, nút chạm khoảng 44 px; bộ lọc xuống dòng thay vì bị cắt.
- Nội dung, liên kết danh mục và CTA hoạt động khi JavaScript lỗi hoặc bị tắt.
- Giữ focus rõ, điều hướng bàn phím, nhãn nút biểu tượng, alt ảnh và tương phản phù hợp.
- Không chiếm thao tác cuộn dọc để điều khiển mô hình trên điện thoại.
- Không ẩn nội dung để chờ animation; tôn trọng `prefers-reduced-motion`.

## Cây vợt đúng hình dáng

Mô hình vợt đúng hình dáng là hạng mục bắt buộc của landing mới. Dùng ảnh thật làm tham chiếu dựng mô hình và poster trong lúc tải. Ảnh trong bản xem trước lấy từ kho dự án; trước khi triển khai cần đối chiếu mã sản phẩm, nguồn/quyền sử dụng và chuẩn hóa ảnh. Không coi tên file là bằng chứng đầy đủ về đúng mẫu vợt.

Yêu cầu dựng mô hình 3D:

1. Chốt một mẫu vợt cụ thể và ảnh tham chiếu mặt trước, cạnh, cán; không tự gán mô hình chung cho nhiều SKU.
2. Khung đầu mảnh, đầu isometric hoặc oval đúng mẫu; phần đáy khung nối với **một thân mảnh**, không có tam giác cổ chữ Y mở kiểu tennis.
3. Đối chiếu tỉ lệ đầu/thân/cán từ mẫu thật; tránh phóng to đầu và thu ngắn thân. Bổ sung cone, nắp cán, dây và lỗ gen ở mức đủ nhận diện.
4. Ưu tiên mô hình GLB đã tối ưu. Gộp mesh/vật liệu; dây dùng texture hoặc line geometry nhẹ. Không dựng mỗi sợi dây thành một ống nhiều đoạn.
5. Tiêu chí đề xuất ban đầu: mô hình đơn dưới khoảng 15k tam giác, dưới 20 draw calls, tải nén mục tiêu dưới 500 KB; điều chỉnh theo kiểm tra hình dáng và thiết bị mục tiêu.
6. Không giả lập đổi màu thành biến thể có thể mua. Chỉ hiển thị màu đã có trong dữ liệu sản phẩm, hoặc ghi rõ “màu minh họa” nếu đây là mô hình giới thiệu chung.

## Kiến trúc hiệu năng

Giữ Laravel + Blade + CSS hiện có, không chuyển framework cho việc này.

### Khi truy cập landing

- Blade hiển thị tiêu đề, CTA, danh mục và sản phẩm ngay; không ẩn nội dung hoặc khóa cuộn để chờ 3D.
- Module hero tự khởi động trong lần tải trang, tải Three.js, loader cần thiết và GLB bất đồng bộ. Không chờ click, tương tác hay cuộn; không trì hoãn bằng một khoảng chờ dài để làm đẹp điểm đo.
- Hero có poster AVIF/WebP và fallback với `srcset`, `sizes`, kích thước/`aspect-ratio` cố định. Giữ poster đến khi cảnh 3D render thành công frame đầu, rồi thay trong cùng khung để tránh nhảy bố cục.
- Poster tải ưu tiên, không `loading="lazy"`; ảnh sản phẩm bên dưới lazy-load. Chỉ prefetch/preload tài nguyên 3D thực sự cần sau khi kiểm tra waterfall, tránh tranh băng thông với nội dung đầu trang.
- Chỉ nạp engine/loader cần cho hero; không nạp GSAP, Lenis, Bloom hay addons hậu kỳ cho hiệu ứng trang trí.
- Native scroll. Hover/focus chủ yếu màu hoặc transform ngắn 120–180 ms. Chuyển động giới thiệu của vợt ngắn, nhẹ rồi về tư thế nghỉ; sau đó phản hồi khi kéo xoay.
- Font ít biến thể, `font-display: swap`; không bổ sung phụ thuộc lớn.
- Bản mockup dùng ảnh nhúng để xem độc lập; **không** đưa cách nhúng base64 đó vào production.

### Vận hành hero 3D mặc định

- Tối đa một canvas/renderer WebGL cho landing; chỉ một vợt chính, gộp mesh/vật liệu và tái sử dụng tài nguyên. Không tạo renderer riêng cho từng danh mục.
- DPR giới hạn khoảng 1–1,5 theo thiết bị; cấu hình mobile nhẹ hơn. Giảm DPR, độ phức tạp ánh sáng và chuyển động dựa trên hiệu năng thực tế, vẫn giữ 3D tự hiển thị.
- Không Bloom/hậu kỳ mặc định, không PMREM tạo lặp cho nhiều cảnh; dùng ánh sáng và vật liệu đơn giản đủ làm rõ hình dáng vợt.
- Sau khi tải, render ngay để người dùng thấy mô hình mà không cần thao tác. Chuyển động giới thiệu kết thúc thì dừng vòng lặp liên tục; render lại khi kéo, zoom, đổi trạng thái hoặc đổi kích thước, dừng khi quán tính kết thúc.
- Khi hero khuất hoặc tab ẩn thì dừng render; hiện lại thì cập nhật frame cần thiết. Chỉ giải phóng geometry/material/texture/renderer và observer/listener khi teardown hoặc rời trang, không tải lại mô hình mỗi lần cuộn qua hero.
- Mobile vẫn tự tải và hiển thị 3D; chế độ tiết kiệm dữ liệu ưu tiên asset nhẹ. `prefers-reduced-motion` tắt chuyển động tự chạy nhưng vẫn hiển thị mô hình 3D ở tư thế tĩnh.
- Khi không hỗ trợ WebGL, JavaScript bị tắt, tải mô hình thất bại hoặc không thể khôi phục context: giữ poster, giá và CTA. Lỗi có thể phục hồi thì cung cấp “Thử lại”; fallback không chặn mua sắm và không phải bước bật 3D trong luồng bình thường.

### Backend

- `LandingController` đã eager-load `hang`, `khuyenMai` và dùng `withCount`: giữ các điểm này.
- Đo TTFB/query trước khi quyết định cache; không quy mọi vấn đề cho PHP hoặc MySQL.
- Nếu cần cache danh mục/thống kê, có thời hạn/invalidation rõ. Giá, tồn kho và chương trình hết hạn cần phản ánh đúng dữ liệu.
- Landing chỉ hiển thị tập sản phẩm nhỏ; phần còn lại dùng trang danh mục/phân trang hiện có.

## Kế hoạch triển khai theo thứ tự

| Bước | Công việc | Kết quả kiểm tra được |
|---|---|---|
| 1. Baseline | Đo landing hiện tại ở `/Landing`, cùng thiết bị và điều kiện mạng với bản mới; ghi trace tải/cuộn, tài nguyên, long task, GPU/renderer, TTFB | Lưu số liệu trước thay đổi, phân biệt tải chậm với cuộn giật |
| 2. Mô hình vợt | Chốt mẫu tham chiếu, sửa hình dáng và tỉ lệ, tối ưu GLB/vật liệu, tạo poster | Mô hình đúng vợt cầu lông, có ngân sách tài nguyên rõ ràng |
| 3. Landing với 3D mặc định | Thay cấu trúc Blade, scoped CSS, một hero 3D tự tải; sản phẩm lên sớm, loại hiệu ứng thừa | Nội dung/CTA dùng ngay; 3D hiển thị không cần click trên desktop/mobile hỗ trợ |
| 4. Trang vào & dữ liệu | Cho `/` render landing; `/Landing` chuyển về `/`; giữ trang bán hàng cũ ở `/Home/Index`; cập nhật navigation, ảnh và dữ liệu thật | Vào website gặp landing; các luồng danh mục, tài khoản, giỏ hàng và checkout vẫn đúng |
| 5. Nghiệm thu | Đo landing mới ở `/` với cùng điều kiện baseline; kiểm tra 3D tự tải, responsive, bàn phím, reduced motion, JS/WebGL lỗi và điều hướng | Có so sánh trước/sau, thời gian 3D sẵn sàng và giới hạn thực tế |

Các điểm dự kiến thay đổi khi triển khai: `routes/web.php`, `resources/views/Landing/Index.blade.php`, `public/css/landing.css`, module hero 3D, mô hình và asset ảnh; các liên kết/navigation và redirect liên quan. Module 3D có thể tách file để tổ chức mã nhưng vẫn tự tải mặc định trong hero. Scoped CSS và tùy chọn layout riêng cho landing giúp tránh ảnh hưởng checkout/admin.

Trước khi sửa ứng dụng, đọc lại hướng dẫn AGENTS hiện hành và hoàn tất setup Laravel Boost theo yêu cầu repository. Bước thiết kế này không cần cài thêm dependency.

## Tiêu chí nghiệm thu đề xuất — chưa phải kết quả đã đạt

- **Truy cập `/` hiển thị landing và vợt 3D tự tải, tự xuất hiện mà không cần click hoặc cuộn** trên desktop/mobile hỗ trợ WebGL; tối đa 1 renderer. Không nạp GSAP/Lenis/Bloom cho hiệu ứng landing.
- `/Landing` chuyển hướng một lần về `/`; `/Home/Index` vẫn mở được trang bán hàng cũ. Logo/Trang chủ về landing; CTA mua sắm vào đúng danh mục; link trực tiếp và intended URL sau đăng nhập không bị ép về landing.
- JS logic riêng của landing mục tiêu dưới 20 KB gzip; poster mobile dưới 180 KB; GLB dưới 500 KB; tổng tài nguyên khởi động, **tính cả engine/loader/mô hình 3D tự tải và layout chung**, mục tiêu dưới 1,5 MB nén. Đây là ngân sách đề xuất cần xác nhận bằng phép đo.
- Đo riêng thời gian từ navigation tới frame 3D đầu tiên thành công (`hero3d-ready`) và khả năng kéo xoay. Không dùng chỉ số LCP của poster để thay cho bằng chứng 3D đã sẵn sàng; ghi rõ điều kiện thiết bị/mạng và kết quả.
- Mục tiêu Core Web Vitals: LCP ≤ 2,5 giây, INP ≤ 200 ms, CLS ≤ 0,1; đánh giá dữ liệu thực tế ở phân vị 75 khi có traffic. Lab chỉ là kiểm tra đại diện, không khẳng định INP thực tế từ điểm Lighthouse.
- Lighthouse mobile ≥ 90 là mục tiêu bổ sung, không thay thế trải nghiệm cuộn và tương tác thực tế.
- Cuộn/đóng mở bộ lọc không có long task lặp lại do hiệu ứng; kiểm tra trace trên profile CPU chậm và điện thoại tầm trung. Hero 3D hướng tới tương tác mượt trên thiết bị mục tiêu, không cam kết 60 FPS trên mọi máy. Không có render loop liên tục sau khi vợt về tư thế nghỉ, khi hero khuất hoặc tab ẩn.
- Kiểm tra 3 lượt tải cache lạnh cùng điều kiện, lưu trung vị và từng kết quả; thêm cache ấm. Ghi rõ máy, trình duyệt, mạng và throttling.
- Test trọng điểm: `/` render landing, `/Landing` redirect không lặp, `/Home/Index` còn truy cập được; 3D tự tải không cần thao tác, giảm chuyển động vẫn có mô hình tĩnh; liên kết và intended URL đúng; sản phẩm/giá/khuyến mãi đúng dữ liệu; thiếu ảnh, khuyến mãi hết hạn và lỗi 3D không phá CTA. Không viết test chụp lại từng chi tiết CSS.

Tài liệu kỹ thuật tham chiếu:

- [Three.js: Rendering on Demand](https://threejs.org/manual/pages/rendering-on-demand.html) — render theo tương tác cho dạng trình xem sản phẩm.
- [Three.js: Responsive Design](https://threejs.org/manual/pages/responsive.html) — chi phí độ phân giải khi render trên GPU.
- [Google: Web Vitals](https://web.dev/articles/vitals) — ngưỡng và cách đánh giá LCP, INP, CLS.
