<?php

namespace App\Services\Ai;

use App\Models\KhuyenMai;
use App\Models\SanPham;
use Exception;
use Illuminate\Support\Facades\Http;

class GeminiChatbotService
{
    protected string $apiKey;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.api_key', '');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Xử lý tin nhắn hỏi đáp tư vấn từ khách hàng
     */
    public function ask(string $message): array
    {
        try {
            $messageLower = mb_strtolower(trim($message), 'UTF-8');
            $maxPrice = null;

            // BƯỚC 1: PHÂN TÍCH GIÁ BẰNG REGEX (ví dụ: "dưới 3 triệu", "tầm 2tr")
            if (preg_match('/(dưới|tầm|khoảng)\s*(\d+(?:\.\d+)?)\s*(triệu|tr)/iu', $messageLower, $matches)) {
                $priceNumber = (float) $matches[2];
                $maxPrice = $priceNumber * 1000000;
            }

            // BƯỚC 2: TÌM KIẾM SẢN PHẨM TRONG DATABASE
            $query = SanPham::with(['chiTietSanPhams', 'khuyenMai']);

            if ($maxPrice !== null) {
                $query->where('GiaGoc', '<=', $maxPrice);
            }

            $stopWords = ['dưới', 'trên', 'triệu', 'tr', 'đồng', 'muốn', 'tìm', 'mua', 'cần', 'cho', 'tôi', 'mình', 'có', 'không', 'vợt', 'giá'];
            $words = preg_split('/\s+/u', $messageLower, -1, PREG_SPLIT_NO_EMPTY);
            $keywords = array_filter($words, function ($w) use ($stopWords) {
                return ! in_array($w, $stopWords) && ! is_numeric($w);
            });

            if (! empty($keywords)) {
                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $q->orWhere('TenSanPham', 'LIKE', "%{$kw}%");
                    }
                });
            }

            $products = $query->take(5)->get();

            // Lấy thông tin khuyến mãi đang diễn ra
            $promos = KhuyenMai::where(function ($q) {
                $q->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', now()->toDateString());
            })->get();

            $promoInfo = '';
            if (str_contains($messageLower, 'khuyến mãi') || str_contains($messageLower, 'giảm giá') || str_contains($messageLower, 'sale')) {
                $promoList = [];
                foreach ($promos as $km) {
                    $promoList[] = "{$km->TenChuongTrinh} giảm {$km->PhanTramGiam}%";
                }
                if (! empty($promoList)) {
                    $promoInfo = 'Chương trình khuyến mãi hiện có: '.implode(', ', $promoList);
                }
            }

            // BƯỚC 3: XÂY DỰNG NGỮ CẢNH (CONTEXT)
            $contextData = 'Hệ thống không tìm thấy sản phẩm nào khớp hoàn toàn với tiêu chí lọc.';
            if ($products->count() > 0) {
                $contextData = 'Dữ liệu sản phẩm '.($maxPrice ? 'dưới '.number_format($maxPrice, 0, ',', '.').' VNĐ' : '')." tìm thấy trong kho:\n";
                foreach ($products as $p) {
                    $tonKho = $p->chiTietSanPhams->sum('SoLuongTon');
                    $giaBan = number_format($p->gia_sau_khuyen_mai, 0, ',', '.');
                    $contextData .= "- {$p->TenSanPham}: Giá {$giaBan} VNĐ (Tồn kho: {$tonKho})\n";
                }
            }
            if (! empty($promoInfo)) {
                $contextData .= "\n".$promoInfo;
            }

            // BƯỚC 4: GỌI GOOGLE GEMINI API
            // Chưa cấu hình API key -> trả lời bằng dữ liệu tra cứu trong kho
            if ($this->apiKey === '') {
                return [
                    'success' => true,
                    'reply' => "Dưới đây là một số gợi ý cho bạn:\n".$contextData,
                ];
            }

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $prompt = "Bạn là trợ lý ảo tư vấn bán thiết bị cầu lông thông minh, lịch sự, chuyên nghiệp của Shop Cầu Lông.\n".
                "Dưới đây là dữ liệu thực tế từ hệ thống kho hàng:\n{$contextData}\n\n".
                "Hãy trả lời câu hỏi sau của khách hàng bằng tiếng Việt một cách tự nhiên, súc tích, tư vấn cụ thể tên sản phẩm, giá bán và tình trạng còn hàng:\n".
                "Khách hàng: {$message}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $replyText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi chưa thể trả lời câu hỏi này.';

                return ['success' => true, 'reply' => $replyText];
            }

            // Fallback nếu API lỗi
            return [
                'success' => true,
                'reply' => "Dưới đây là một số gợi ý cho bạn:\n".$contextData,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'reply' => 'Lỗi kết nối AI: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Tự động sinh nội dung bài viết tin tức / review bằng Google Gemini AI
     */
    public function generateArticle(string $topic, ?string $summary = null): array
    {
        try {
            $topic = trim($topic);
            if ($topic === '') {
                $topic = 'Kỹ thuật chơi cầu lông và hướng dẫn chọn vợt phù hợp';
            }

            // Nếu chưa cấu hình Gemini API key -> sinh nội dung mẫu chuyên nghiệp
            if ($this->apiKey === '') {
                return [
                    'success' => true,
                    'title' => $topic,
                    'summary' => $summary ?: "Tổng quan chi tiết và hướng dẫn chuyên sâu về chủ đề: {$topic}.",
                    'content' => "<p>Cầu lông là một trong những bộ môn thể thao được yêu thích nhất hiện nay nhờ tính linh hoạt, tốc độ và khả năng rèn luyện phản xạ tuyệt vời. Trong bài viết này, chúng tôi sẽ cùng bạn khám phá chi tiết về <strong>{$topic}</strong>.</p><h3>1. Những điểm nổi bật và phân tích chuyên sâu</h3><p>Khi tìm hiểu về bộ môn cũng như các trang thiết bị thi đấu, người chơi cần lưu ý đến sự cân bằng giữa sức mạnh, độ chính xác và độ bền. Việc lựa chọn công cụ phù hợp giúp bạn nâng cao phong độ đáng kể và phòng tránh các chấn thương khớp vai, cổ tay.</p><ul><li><strong>Độ trợ lực tối ưu:</strong> Thiết kế thân vợt dẻo hoặc cứng trung bình hỗ trợ người chơi dễ dàng thực hiện các pha phông cầu cuối sân.</li><li><strong>Cảm giác kiểm soát:</strong> Khung vát khí động học giúp tăng tốc độ vung vợt, tạo ra những cú đập cầu chuẩn xác và uy lực.</li><li><strong>Độ bền bỉ vượt trội:</strong> Ứng dụng vật liệu Carbon Nano cao cấp tăng cường sức chịu lực va chạm và độ căng cân cao.</li></ul><h3>2. Lời khuyên từ chuyên gia</h3><p>Để đạt hiệu quả thi đấu và rèn luyện tốt nhất, bạn nên dành ít nhất 15 phút khởi động kỹ các khớp trước mỗi buổi tập, kết hợp chế độ nghỉ ngơi và dinh dưỡng hợp lý. Chúc bạn luôn tràn đầy năng lượng và có những giờ phút thăng hoa trên sân cầu!</p>",
                ];
            }

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            $prompt = "Bạn là biên tập viên thể thao và chuyên gia cầu lông của website Shop Cầu Lông.\n".
                "Hãy viết một bài viết tin tức hoặc chia sẻ kinh nghiệm hoàn chỉnh về chủ đề: \"{$topic}\".".
                ($summary ? " Dựa trên tóm tắt ban đầu: \"{$summary}\"." : '')."\n\n".
                "Yêu cầu:\n".
                "- Định dạng trả về: Chuỗi HTML chuẩn dùng cho rich text editor (chỉ chứa các thẻ <p>, <h3>, <strong>, <em>, <ul>, <li>, <blockquote>).\n".
                "- Độ dài khoảng 300 - 500 từ, hành văn cuốn hút, chuyên nghiệp, mạch lạc.\n".
                '- KHÔNG bao bọc bằng ```html ... ```, chỉ trả về mã HTML trực tiếp.';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(25)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $replyText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $cleanHtml = preg_replace('/^```(?:html)?\s*|\s*```$/i', '', trim($replyText));

                return [
                    'success' => true,
                    'title' => $topic,
                    'summary' => $summary ?: "Bài viết chuyên sâu về {$topic} do trợ lý AI biên soạn.",
                    'content' => $cleanHtml,
                ];
            }

            throw new Exception('Không thể kết nối dịch vụ Gemini: HTTP '.$response->status());
        } catch (Exception $e) {
            return [
                'success' => true,
                'title' => $topic,
                'summary' => $summary ?: "Tổng quan chi tiết về chủ đề: {$topic}.",
                'content' => "<p>Nội dung giới thiệu chi tiết về <strong>{$topic}</strong>. Hiện tại hệ thống đang cập nhật thêm dữ liệu chuyên sâu.</p><p>Vui lòng theo dõi các bản tin tiếp theo để cập nhật những kiến thức hữu ích nhất!</p>",
                'warning' => 'AI fallback: '.$e->getMessage(),
            ];
        }
    }
}
