<?php

namespace App\Services\Ai;

use App\Models\SanPham;
use App\Models\KhuyenMai;
use Illuminate\Support\Facades\Http;
use Exception;

class GeminiChatbotService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY', ''));
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
                $priceNumber = (float)$matches[2];
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
                return !in_array($w, $stopWords) && !is_numeric($w);
            });

            if (!empty($keywords)) {
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

            $promoInfo = "";
            if (str_contains($messageLower, 'khuyến mãi') || str_contains($messageLower, 'giảm giá') || str_contains($messageLower, 'sale')) {
                $promoList = [];
                foreach ($promos as $km) {
                    $promoList[] = "{$km->TenChuongTrinh} giảm {$km->PhanTramGiam}%";
                }
                if (!empty($promoList)) {
                    $promoInfo = "Chương trình khuyến mãi hiện có: " . implode(', ', $promoList);
                }
            }

            // BƯỚC 3: XÂY DỰNG NGỮ CẢNH (CONTEXT)
            $contextData = "Hệ thống không tìm thấy sản phẩm nào khớp hoàn toàn với tiêu chí lọc.";
            if ($products->count() > 0) {
                $contextData = "Dữ liệu sản phẩm " . ($maxPrice ? "dưới " . number_format($maxPrice, 0, ',', '.') . " VNĐ" : "") . " tìm thấy trong kho:\n";
                foreach ($products as $p) {
                    $tonKho = $p->chiTietSanPhams->sum('SoLuongTon');
                    $giaBan = number_format($p->gia_sau_khuyen_mai, 0, ',', '.');
                    $contextData .= "- {$p->TenSanPham}: Giá {$giaBan} VNĐ (Tồn kho: {$tonKho})\n";
                }
            }
            if (!empty($promoInfo)) {
                $contextData .= "\n" . $promoInfo;
            }

            // BƯỚC 4: GỌI GOOGLE GEMINI API
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$this->apiKey}";

            $prompt = "Bạn là trợ lý ảo tư vấn bán thiết bị cầu lông thông minh, lịch sự, chuyên nghiệp của Shop Cầu Lông.\n" .
                "Dưới đây là dữ liệu thực tế từ hệ thống kho hàng:\n{$contextData}\n\n" .
                "Hãy trả lời câu hỏi sau của khách hàng bằng tiếng Việt một cách tự nhiên, súc tích, tư vấn cụ thể tên sản phẩm, giá bán và tình trạng còn hàng:\n" .
                "Khách hàng: {$message}";

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                $replyText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi chưa thể trả lời câu hỏi này.';
                return ['success' => true, 'reply' => $replyText];
            }

            // Fallback nếu API lỗi
            return [
                'success' => true,
                'reply' => "Dưới đây là một số gợi ý cho bạn:\n" . $contextData
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'reply' => 'Lỗi kết nối AI: ' . $e->getMessage()
            ];
        }
    }
}
