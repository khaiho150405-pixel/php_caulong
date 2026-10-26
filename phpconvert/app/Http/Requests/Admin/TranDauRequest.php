<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho form Thêm / Sửa kết quả trận đấu.
 */
class TranDauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'TiSo' => trim((string) $this->input('TiSo')) ?: null,
            'DoiThang' => $this->input('DoiThang') ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'TenGiaiDau' => ['required', 'string', 'max:150'],
            'Vong' => ['required', 'string', 'max:60'],
            'NoiDung' => ['required', 'string', 'max:60'],
            'DoiA' => ['required', 'string', 'max:150'],
            'DoiB' => ['required', 'string', 'max:150', 'different:DoiA'],
            // Ví dụ hợp lệ: 21-11, 21-11  hoặc  22-20, 19-21, 21-15
            'TiSo' => ['nullable', 'regex:/^\d{1,2}-\d{1,2}(,\s*\d{1,2}-\d{1,2}){0,2}$/'],
            'DoiThang' => ['nullable', 'in:A,B', 'required_with:TiSo'],
            'NgayThiDau' => ['required', 'date'],
            'DiaDiem' => ['nullable', 'string', 'max:120'],
            'NguonTin' => ['nullable', 'string', 'max:150'],
            'LienKetNguon' => ['nullable', 'url', 'max:300'],
        ];
    }

    public function attributes(): array
    {
        return [
            'TenGiaiDau' => 'tên giải đấu',
            'Vong' => 'vòng đấu',
            'NoiDung' => 'nội dung thi đấu',
            'DoiA' => 'bên A',
            'DoiB' => 'bên B',
            'TiSo' => 'tỉ số',
            'DoiThang' => 'bên thắng',
            'NgayThiDau' => 'ngày thi đấu',
            'DiaDiem' => 'địa điểm',
            'NguonTin' => 'tên nguồn tin',
            'LienKetNguon' => 'liên kết nguồn',
        ];
    }

    public function messages(): array
    {
        return [
            'DoiB.different' => 'Hai bên thi đấu phải khác nhau.',
            'TiSo.regex' => 'Tỉ số phải theo dạng 21-11, 21-11 (mỗi ván cách nhau bằng dấu phẩy, tối đa 3 ván).',
            'DoiThang.required_with' => 'Hãy chọn bên thắng khi đã nhập tỉ số.',
        ];
    }
}
