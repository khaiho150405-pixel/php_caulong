<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'HoTenNhan' => ['required', 'string', 'max:100'],
            'SoDienThoaiNhan' => ['required', 'regex:/^0[0-9]{9,10}$/'],
            'DiaChiGiao' => ['required', 'string', 'max:255'],
            'GhiChu' => ['nullable', 'string', 'max:400'],
        ];
    }

    public function attributes(): array
    {
        return [
            'HoTenNhan' => 'họ tên người nhận',
            'SoDienThoaiNhan' => 'số điện thoại người nhận',
            'DiaChiGiao' => 'địa chỉ giao hàng',
            'GhiChu' => 'ghi chú',
        ];
    }

    public function messages(): array
    {
        return [
            'SoDienThoaiNhan.regex' => 'Số điện thoại phải bắt đầu bằng 0 và có 10–11 chữ số.',
        ];
    }
}
