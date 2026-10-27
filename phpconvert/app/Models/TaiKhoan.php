<?php

namespace App\Models;

use Database\Factories\TaiKhoanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{

    use HasFactory, Notifiable;

    // Mã quyền trong bảng PhanQuyen
    public const QUYEN_ADMIN = 1;
    public const QUYEN_NHANVIEN = 2;
    public const QUYEN_KHACHHANG = 3;


    public const ROLES = [
        'admin' => self::QUYEN_ADMIN,
        'nhanvien' => self::QUYEN_NHANVIEN,
        'khachhang' => self::QUYEN_KHACHHANG,
    ];

    public static function roleId(string $role): int
    {
        $role = strtolower(trim($role));
        if (is_numeric($role)) {
            return (int) $role;
        }
        if (!array_key_exists($role, self::ROLES)) {
            throw new \InvalidArgumentException("Vai trò không hợp lệ: {$role}");
        }

        return self::ROLES[$role];
    }

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

    protected static function newFactory(): TaiKhoanFactory
    {
        return TaiKhoanFactory::new();
    }

    public function khachHang()
    {
        return $this->hasOne(KhachHang::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }

    public function isAdmin(): bool
    {
        return (int)$this->MaQuyen === self::QUYEN_ADMIN;
    }

    public function isStaff(): bool
    {
        return (int)$this->MaQuyen === self::QUYEN_NHANVIEN;
    }

    public function isCustomer(): bool
    {
        return (int)$this->MaQuyen === self::QUYEN_KHACHHANG;
    }

    public function hasAdminAccess(): bool
    {
        return in_array((int)$this->MaQuyen, [self::QUYEN_ADMIN, self::QUYEN_NHANVIEN], true);
    }
}
