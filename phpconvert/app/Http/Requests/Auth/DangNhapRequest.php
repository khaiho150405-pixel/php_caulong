<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'TenDangNhap' => ['required', 'string', 'max:50'],
            'MatKhau' => ['required', 'string', 'max:100'],
            'returnUrl' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'TenDangNhap.required' => 'Vui lòng nhập tên đăng nhập.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
        ];
    }
}
