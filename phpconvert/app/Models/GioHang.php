<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'GioHang';
    protected $primaryKey = 'MaGioHang';
    public $timestamps = false;

    protected $fillable = [
        'MaKhachHang',
        'MaChiTietSanPham',
        'SoLuong',
        'NgayThem',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'NgayThem' => 'datetime',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function chiTietSanPham()
    {
        return $this->belongsTo(ChiTietSanPham::class, 'MaChiTietSanPham', 'MaChiTiet');
    }
}
