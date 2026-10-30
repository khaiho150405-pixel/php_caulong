<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhanHoiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maSP' => ['required', 'integer', 'exists:SanPham,MaSanPham'],
            'danhGia' => ['required', 'integer', 'between:1,5'],
            'noiDung' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'maSP' => 'sản phẩm',
            'danhGia' => 'số sao đánh giá',
            'noiDung' => 'nội dung đánh giá',
        ];
    }
}
