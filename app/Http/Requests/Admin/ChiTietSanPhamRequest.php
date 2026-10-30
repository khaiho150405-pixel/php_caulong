<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChiTietSanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maChiTiet = $this->route('maChiTiet');

        return [
            'MaMau' => ['nullable', 'integer', 'exists:MauSac,MaMau'],
            'MaSize' => ['nullable', 'integer', 'exists:Size,MaSize'],
            'GiaBan' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'SoLuongTon' => ['required', 'integer', 'min:0', 'max:100000'],
            'SKU' => [
                'required', 'string', 'max:50',
                Rule::unique('ChiTietSanPham', 'SKU')->ignore($maChiTiet, 'MaChiTiet'),
            ],
            'HinhAnhUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'MaMau' => 'màu sắc',
            'MaSize' => 'size',
            'GiaBan' => 'giá bán',
            'SoLuongTon' => 'số lượng tồn',
            'SKU' => 'mã SKU',
            'HinhAnhUpload' => 'hình ảnh biến thể',
        ];
    }
}
