<?php

namespace App\Services\Payment;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoPaymentService
{
    protected string $endpoint;

    protected string $partnerCode;

    protected string $accessKey;

    protected string $secretKey;

    protected string $returnUrl;

    protected string $notifyUrl;

    public function __construct()
    {
        $this->endpoint = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->partnerCode = config('services.momo.partner_code', 'MOMO5RGX20191128');
        $this->accessKey = config('services.momo.access_key', 'M8brj9K6E22vXoDB');
        $this->secretKey = config('services.momo.secret_key', 'nqQiVSgDMy809JoPF6OzP5OdBUB550Y4');
        $this->returnUrl = config('services.momo.return_url', url('/DonHang/ThanhToanMomoSuccess'));
        $this->notifyUrl = config('services.momo.notify_url', url('/DonHang/SavePayment'));
    }

    public function createPayment(int $maDonHang, float $amount, string $orderInfo = ''): string
    {
        $orderId = (string) $maDonHang;

        if (config('services.momo.mock', true)) {
            return route('donhang.momo_mock_gateway', [
                'orderId' => $orderId,
            ]);
        }

        $requestId = (string) Str::uuid();
        $amountInt = (int) $amount;
        $amountStr = (string) $amountInt;
        $orderInfoStr = ! empty($orderInfo) ? $orderInfo : "Thanh toan don hang #{$maDonHang}";
        $extraData = '';
        $requestType = 'captureWallet';

        $rawHash = "accessKey={$this->accessKey}".
            "&amount={$amountStr}".
            "&extraData={$extraData}".
            "&ipnUrl={$this->notifyUrl}".
            "&orderId={$orderId}".
            "&orderInfo={$orderInfoStr}".
            "&partnerCode={$this->partnerCode}".
            "&redirectUrl={$this->returnUrl}".
            "&requestId={$requestId}".
            "&requestType={$requestType}";

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => 'Shop Cầu Lông',
            'storeId' => 'MomoTestStore',
            'requestId' => $requestId,
            'amount' => $amountInt,
            'orderId' => $orderId,
            'orderInfo' => $orderInfoStr,
            'redirectUrl' => $this->returnUrl,
            'ipnUrl' => $this->notifyUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->endpoint, $payload);

        if (! $response->successful()) {
            throw new Exception('Lỗi kết nối cổng thanh toán MoMo: '.$response->body());
        }

        $resJson = $response->json();
        if (isset($resJson['payUrl'])) {
            return $resJson['payUrl'];
        }

        $message = $resJson['message'] ?? 'Lỗi không xác định';
        if (($resJson['resultCode'] ?? null) === 99) {
            $message .= ' (Tài khoản test mặc định của MoMo bị từ chối. Bạn hãy bật MOMO_MOCK=true trong file .env để chạy chế độ giả lập MoMo Sandbox, hoặc đăng ký Merchant riêng tại business.momo.vn)';
        }

        throw new Exception('MoMo không trả về đường dẫn thanh toán: '.$message);
    }

    public function verifySignature(array $data): bool
    {
        if (! isset($data['signature'])) {
            return false;
        }

        $rawHash = "accessKey={$this->accessKey}".
            "&amount={$data['amount']}".
            "&extraData={$data['extraData']}".
            "&message={$data['message']}".
            "&orderId={$data['orderId']}".
            "&orderInfo={$data['orderInfo']}".
            "&orderType={$data['orderType']}".
            "&partnerCode={$data['partnerCode']}".
            "&payType={$data['payType']}".
            "&requestId={$data['requestId']}".
            "&responseTime={$data['responseTime']}".
            "&resultCode={$data['resultCode']}".
            "&transId={$data['transId']}";

        $expectedSignature = hash_hmac('sha256', $rawHash, $this->secretKey);

        return hash_equals($expectedSignature, $data['signature']);
    }

    public function generateResponseSignature(array $data): string
    {
        $rawHash = "accessKey={$this->accessKey}".
            "&amount={$data['amount']}".
            "&extraData={$data['extraData']}".
            "&message={$data['message']}".
            "&orderId={$data['orderId']}".
            "&orderInfo={$data['orderInfo']}".
            "&orderType={$data['orderType']}".
            "&partnerCode={$data['partnerCode']}".
            "&payType={$data['payType']}".
            "&requestId={$data['requestId']}".
            "&responseTime={$data['responseTime']}".
            "&resultCode={$data['resultCode']}".
            "&transId={$data['transId']}";

        return hash_hmac('sha256', $rawHash, $this->secretKey);
    }
}
