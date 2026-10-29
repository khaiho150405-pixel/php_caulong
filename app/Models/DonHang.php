<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'DonHang';

    protected $primaryKey = 'MaDonHang';

    public $timestamps = false;

    protected $fillable = [
        'NgayDat',
        'TrangThai',
        'TongTien',
        'TongTienSauGiam',
        'MaKhachHang',
        'TienGiam',
        'DiaChiGiaoHang',
        'SoDienThoaiNhanHang',
        'GhiChu',
    ];

    protected $casts = [
        'NgayDat' => 'datetime',
        'TongTien' => 'decimal:2',
        'TongTienSauGiam' => 'decimal:2',
        'TienGiam' => 'decimal:2',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'MaKhachHang', 'MaKhachHang');
    }

    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaDonHang', 'MaDonHang');
    }
}
