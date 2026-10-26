<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DoiMatKhauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matKhauCu' => ['required', 'string'],
            'matKhauMoi' => ['required', 'string', 'min:6', 'max:100', 'different:matKhauCu'],
            'xacNhanMoi' => ['required', 'same:matKhauMoi'],
        ];
    }

    public function attributes(): array
    {
        return [
            'matKhauCu' => 'mật khẩu cũ',
            'matKhauMoi' => 'mật khẩu mới',
            'xacNhanMoi' => 'xác nhận mật khẩu mới',
        ];
    }

    public function messages(): array
    {
        return [
            'xacNhanMoi.same' => 'Mật khẩu mới không khớp.',
            'matKhauMoi.different' => 'Mật khẩu mới phải khác mật khẩu cũ.',
        ];
    }
}
