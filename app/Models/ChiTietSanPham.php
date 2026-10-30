<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietSanPham extends Model
{
    protected $table = 'ChiTietSanPham';

    protected $primaryKey = 'MaChiTiet';

    public $timestamps = false;

    protected $fillable = [
        'MaSanPham',
        'MaMau',
        'MaSize',
        'GiaBan',
        'SoLuongTon',
        'SKU',
        'HinhAnh',
    ];

    protected $casts = [
        'GiaBan' => 'decimal:2',
        'SoLuongTon' => 'integer',
    ];

    public function sanPham()
    {

        return $this->belongsTo(SanPham::class, 'MaSanPham', 'MaSanPham')->withTrashed();
    }

    public function mauSac()
    {
        return $this->belongsTo(MauSac::class, 'MaMau', 'MaMau');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'MaSize', 'MaSize');
    }

    public function thongSoVots()
    {
        return $this->hasMany(ThongSoVot::class, 'MaChiTiet', 'MaChiTiet');
    }

    public function thongSoVot()
    {
        return $this->hasOne(ThongSoVot::class, 'MaChiTiet', 'MaChiTiet');
    }

    public function gioHangs()
    {
        return $this->hasMany(GioHang::class, 'MaChiTietSanPham', 'MaChiTiet');
    }

    public function chiTietDonHangs()
    {
        return $this->hasMany(ChiTietDonHang::class, 'MaChiTietSanPham', 'MaChiTiet');
    }

    public function getEffectivePriceAttribute()
    {
        $basePrice = (float) ($this->GiaBan ?? ($this->sanPham ? $this->sanPham->GiaGoc : 0) ?? 0);
        if ($basePrice <= 0) {
            return 0;
        }

        $sp = $this->sanPham;
        if ($sp && $sp->khuyenMai && $sp->khuyenMai->isEffective()) {
            $km = $sp->khuyenMai;
            $phanTram = (float) ($km->PhanTramGiam ?? 0);
            $giam = ($basePrice * $phanTram) / 100;

            if ($km->GiamToiDa && $giam > (float) $km->GiamToiDa) {
                $giam = (float) $km->GiamToiDa;
            }

            return max(0, $basePrice - $giam);
        }

        return $basePrice;
    }

    public function getDisplayImageAttribute()
    {
        if (! empty($this->HinhAnh)) {
            return $this->HinhAnh;
        }

        return $this->sanPham ? $this->sanPham->HinhAnhDaiDien : 'default-product.jpg';
    }
}
