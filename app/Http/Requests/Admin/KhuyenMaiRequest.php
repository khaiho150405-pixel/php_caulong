<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho form Thêm / Sửa chương trình khuyến mãi.
 */
class KhuyenMaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'TenChuongTrinh' => ['required', 'string', 'max:100'],
            'MoTa' => ['nullable', 'string', 'max:255'],
            'PhanTramGiam' => ['required', 'numeric', 'min:0', 'max:100'],
            'GiamToiDa' => ['nullable', 'numeric', 'min:0'],
            'SoLuongSuDung' => ['nullable', 'integer', 'min:0'],
            'NgayBatDau' => ['required', 'date'],
            'NgayKetThuc' => ['required', 'date', 'after_or_equal:NgayBatDau'],
        ];
    }

    public function attributes(): array
    {
        return [
            'TenChuongTrinh' => 'tên chương trình',
            'MoTa' => 'mô tả',
            'PhanTramGiam' => 'phần trăm giảm',
            'GiamToiDa' => 'số tiền giảm tối đa',
            'SoLuongSuDung' => 'số lượng sử dụng',
            'NgayBatDau' => 'ngày bắt đầu',
            'NgayKetThuc' => 'ngày kết thúc',
        ];
    }
}
