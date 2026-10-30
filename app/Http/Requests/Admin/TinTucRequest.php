<?php

namespace App\Http\Requests\Admin;

use App\Models\TinTuc;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TinTucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {

        $this->merge([
            'MaSanPham' => $this->input('MaSanPham') ?: null,
            'MaTranDau' => $this->input('MaTranDau') ?: null,
            'LoaiTin' => $this->input('LoaiTin') ?: 'san-pham-hot',
            'NoiBat' => $this->boolean('NoiBat'),
            'HienThi' => $this->boolean('HienThi'),
            'ThuTu' => (int) ($this->input('ThuTu') ?: 1),
        ]);
    }

    public function rules(): array
    {
        return [
            'TieuDe' => ['required', 'string', 'max:200'],
            'TomTat' => ['nullable', 'string', 'max:500'],
            'NoiDung' => ['required', 'string', 'min:20'],
            'LoaiTin' => ['required', Rule::in(array_keys(TinTuc::LOAI))],
            'HinhAnh' => ['nullable', 'string', 'max:255'],
            'HinhAnh_file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'MaSanPham' => ['nullable', 'integer', Rule::exists('SanPham', 'MaSanPham')],
            'MaTranDau' => ['nullable', 'integer', Rule::exists('TranDau', 'MaTranDau')],
            'NguonTin' => ['nullable', 'string', 'max:150'],
            'LienKetNguon' => ['nullable', 'url', 'max:300'],
            'NoiBat' => ['boolean'],
            'HienThi' => ['boolean'],
            'ThuTu' => ['nullable', 'integer', 'min:0'],
            'NgayDang' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'TieuDe' => 'tiêu đề',
            'TomTat' => 'tóm tắt',
            'NoiDung' => 'nội dung',
            'LoaiTin' => 'loại tin',
            'MaSanPham' => 'sản phẩm',
            'MaTranDau' => 'trận đấu',
            'NguonTin' => 'tên nguồn tin',
            'LienKetNguon' => 'liên kết nguồn',
            'NgayDang' => 'ngày đăng',
        ];
    }

    public function messages(): array
    {
        return [
            'NoiDung.min' => 'Nội dung quá ngắn, hãy nhập ít nhất :min ký tự.',
            'LienKetNguon.url' => 'Liên kết nguồn phải là đường dẫn hợp lệ (bắt đầu bằng http:// hoặc https://).',
        ];
    }
}
