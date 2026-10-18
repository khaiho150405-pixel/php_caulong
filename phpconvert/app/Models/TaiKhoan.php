<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $table = 'TaiKhoan';
    protected $primaryKey = 'MaTaiKhoan';
    public $timestamps = false;

    protected $fillable = [
        'TenDangNhap',
        'MatKhau',
        'Email',
        'MaQuyen',
        'NgayTao',
    ];

    protected $hidden = [
        'MatKhau',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    public function getAuthIdentifierName()
    {
        return 'MaTaiKhoan';
    }

    public function phanQuyen()
    {
        return $this->belongsTo(PhanQuyen::class, 'MaQuyen', 'MaQuyen');
    }

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }

    public function isAdmin(): bool
    {
        return (int)$this->MaQuyen === 1;
    }

    public function isStaff(): bool
    {
        return (int)$this->MaQuyen === 2;
    }

    public function isCustomer(): bool
    {
        return (int)$this->MaQuyen === 3;
    }

    public function hasAdminAccess(): bool
    {
        return in_array((int)$this->MaQuyen, [1, 2]);
    }
}
