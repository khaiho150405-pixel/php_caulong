<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'HoTen' => ['required', 'string', 'max:100'],
            'TenDangNhap' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:TaiKhoan,TenDangNhap'],
            'Email' => ['required', 'email', 'max:100', 'unique:TaiKhoan,Email', 'unique:KhachHang,Email'],
            'SoDienThoai' => ['nullable', 'regex:/^0[0-9]{9,10}$/'],
            'DiaChi' => ['nullable', 'string', 'max:255'],
            'MatKhau' => ['required', 'string', 'min:6', 'max:100'],
            'XacNhanMatKhau' => ['required', 'same:MatKhau'],
        ];
    }

    public function attributes(): array
    {
        return [
            'HoTen' => 'họ tên',
            'TenDangNhap' => 'tên đăng nhập',
            'Email' => 'email',
            'SoDienThoai' => 'số điện thoại',
            'DiaChi' => 'địa chỉ',
            'MatKhau' => 'mật khẩu',
            'XacNhanMatKhau' => 'xác nhận mật khẩu',
        ];
    }

    public function messages(): array
    {
        return [
            'TenDangNhap.unique' => 'Tên đăng nhập đã tồn tại.',
            'Email.unique' => 'Email đã được sử dụng.',
            'XacNhanMatKhau.same' => 'Xác nhận mật khẩu không khớp.',
            'SoDienThoai.regex' => 'Số điện thoại phải bắt đầu bằng 0 và có 10–11 chữ số.',
        ];
    }
}
