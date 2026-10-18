<?php

namespace App\Http\Controllers;

use App\Services\Ai\GeminiChatbotService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function ask(Request $request, GeminiChatbotService $chatbotService)
    {
        $message = trim((string)$request->input('message', ''));
        if (empty($message)) {
            return response()->json(['success' => false, 'reply' => 'Vui lòng nhập câu hỏi của bạn.']);
        }

        $result = $chatbotService->ask($message);
        return response()->json($result);
    }
}
