<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation cho form Thêm / Sửa sản phẩm (khu vực quản trị).
 */
class SanPhamRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Quyền đã được kiểm tra bởi middleware role:admin,nhanvien
        return true;
    }

    public function rules(): array
    {
        return [
            'TenSanPham' => ['required', 'string', 'max:100'],
            'GiaGoc' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'MaLoai' => ['required', 'integer', 'exists:LoaiSanPham,MaLoai'],
            'MaHang' => ['required', 'integer', 'exists:Hang,MaHang'],
            'MaNhaCungCap' => ['required', 'integer', 'exists:NhaCungCap,MaNhaCungCap'],
            'MaKhuyenMai' => ['nullable', 'integer', 'exists:KhuyenMai,MaKhuyenMai'],
            'MoTa' => ['nullable', 'string', 'max:5000'],
            'HinhAnhUpload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'CoSize' => ['nullable', 'boolean'],
            'CoMau' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'TenSanPham' => 'tên sản phẩm',
            'GiaGoc' => 'giá gốc',
            'MaLoai' => 'loại sản phẩm',
            'MaHang' => 'hãng sản xuất',
            'MaNhaCungCap' => 'nhà cung cấp',
            'MaKhuyenMai' => 'chương trình khuyến mãi',
            'MoTa' => 'mô tả',
            'HinhAnhUpload' => 'hình ảnh đại diện',
        ];
    }
}
