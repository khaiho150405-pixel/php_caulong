<?php

namespace App\Http\Requests\Admin;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaiKhoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');
        $taiKhoan = $id ? TaiKhoan::with('khachHang')->find($id) : null;
        $isUpdate = $taiKhoan !== null;

        return [
            'TenDangNhap' => $isUpdate
                ? ['prohibited']
                : ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:TaiKhoan,TenDangNhap'],
            'MatKhau' => $isUpdate
                ? ['nullable', 'string', 'min:6', 'max:100']
                : ['required', 'string', 'min:6', 'max:100'],
            'Email' => [
                'required', 'email', 'max:100',
                Rule::unique('TaiKhoan', 'Email')->ignore($id, 'MaTaiKhoan'),
                Rule::unique('KhachHang', 'Email')->ignore($taiKhoan?->khachHang?->MaKhachHang, 'MaKhachHang'),
            ],
            'MaQuyen' => ['required', 'integer', 'exists:PhanQuyen,MaQuyen'],
            'HoTen' => ['required', 'string', 'max:100'],
            'SoDienThoai' => ['nullable', 'regex:/^0[0-9]{9,10}$/'],
            'DiaChi' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'TenDangNhap' => 'tên đăng nhập',
            'MatKhau' => 'mật khẩu',
            'Email' => 'email',
            'MaQuyen' => 'phân quyền',
            'HoTen' => 'họ tên',
            'SoDienThoai' => 'số điện thoại',
            'DiaChi' => 'địa chỉ',
        ];
    }

    public function messages(): array
    {
        return [
            'SoDienThoai.regex' => 'Số điện thoại phải bắt đầu bằng 0 và có 10–11 chữ số.',
            'TenDangNhap.prohibited' => 'Không được thay đổi tên đăng nhập.',
        ];
    }
}
