<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanHoi extends Model
{
    protected $table = 'PhanHoi';

    protected $primaryKey = 'MaPhanHoi';

    public $timestamps = false;

    protected $fillable = [
        'NoiDung',
        'NgayPhanHoi',
        'DanhGia',
        'MaKhachHang',
        'MaSanPham',
    ];

    protected $casts = [
        'NgayPhanHoi' => 'datetime',
        'DanhGia' => 'integer',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MaSanPham', 'MaSanPham');
    }
}
