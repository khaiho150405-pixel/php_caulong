<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    protected $table = 'SanPham';
    protected $primaryKey = 'MaSanPham';
    public $timestamps = false;

    protected $fillable = [
        'TenSanPham',
        'MoTa',
        'GiaGoc',
        'HinhAnhDaiDien',
        'MaLoai',
        'MaNhaCungCap',
        'MaHang',
        'MaKhuyenMai',
        'CoSize',
        'CoMau',
        'NgayTao',
    ];

    protected $casts = [
        'GiaGoc' => 'decimal:2',
        'CoSize' => 'boolean',
        'CoMau' => 'boolean',
    ];

    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class, 'MaLoai', 'MaLoai');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'MaNhaCungCap', 'MaNhaCungCap');
    }

    public function hang()
    {
        return $this->belongsTo(Hang::class, 'MaHang', 'MaHang');
    }

    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKhuyenMai', 'MaKhuyenMai');
    }

    public function chiTietSanPhams()
    {
        return $this->hasMany(ChiTietSanPham::class, 'MaSanPham', 'MaSanPham');
    }

    public function phanHois()
    {
        return $this->hasMany(PhanHoi::class, 'MaSanPham', 'MaSanPham');
    }

    /**
     * Tương đương hàm dbo.GiaSauKhuyenMai(@MaSanPham) trong SQL Server
     */
    public function getGiaSauKhuyenMaiAttribute()
    {
        $giaGoc = (float)($this->GiaGoc ?? 0);
        if ($giaGoc <= 0) {
            return 0;
        }

        if ($this->khuyenMai && $this->khuyenMai->isEffective()) {
            $phanTram = (float)$this->khuyenMai->PhanTramGiam;
            $giam = ($giaGoc * $phanTram) / 100;

            if ($this->khuyenMai->GiamToiDa && $giam > (float)$this->khuyenMai->GiamToiDa) {
                $giam = (float)$this->khuyenMai->GiamToiDa;
            }

            return max(0, $giaGoc - $giam);
        }

        return $giaGoc;
    }

    /**
     * Tương đương hàm dbo.TrungBinhDanhGia(@MaSP) trong SQL Server
     */
    public function getTrungBinhDanhGiaAttribute(): float
    {
        $avg = $this->phanHois()->avg('DanhGia');
        return $avg ? round((float)$avg, 1) : 5.0;
    }
}
