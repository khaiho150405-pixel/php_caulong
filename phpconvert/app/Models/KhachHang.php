<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    protected $table = 'KhachHang';
    protected $primaryKey = 'MaKhachHang';
    public $timestamps = false;

    protected $fillable = [
        'HoTen',
        'Email',
        'SoDienThoai',
        'DiaChi',
        'MaTaiKhoan',
        'NgayTao',
    ];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }

    public function donHangs()
    {
        return $this->hasMany(DonHang::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function gioHangs()
    {
        return $this->hasMany(GioHang::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function phanHois()
    {
        return $this->hasMany(PhanHoi::class, 'MaKhachHang', 'MaKhachHang');
    }
}
