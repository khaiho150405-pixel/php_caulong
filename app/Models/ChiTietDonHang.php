<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'ChiTietDonHang';

    protected $primaryKey = 'MaChiTiet';

    public $timestamps = false;

    protected $fillable = [
        'MaDonHang',
        'MaChiTietSanPham',
        'SoLuong',
        'DonGia',
        'ThanhTien',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2',
        'ThanhTien' => 'decimal:2',
    ];

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'MaDonHang', 'MaDonHang');
    }

    public function chiTietSanPham()
    {
        return $this->belongsTo(ChiTietSanPham::class, 'MaChiTietSanPham', 'MaChiTiet');
    }
}
