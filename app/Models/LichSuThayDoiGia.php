<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuThayDoiGia extends Model
{
    protected $table = 'LichSuThayDoiGia';

    protected $primaryKey = 'MaLichSu';

    public $timestamps = false;

    protected $fillable = [
        'MaChiTiet',
        'GiaCu',
        'GiaMoi',
        'NgayThayDoi',
        'NguoiThayDoi',
    ];

    protected $casts = [
        'GiaCu' => 'decimal:2',
        'GiaMoi' => 'decimal:2',
        'NgayThayDoi' => 'datetime',
    ];

    public function chiTietSanPham()
    {
        return $this->belongsTo(ChiTietSanPham::class, 'MaChiTiet', 'MaChiTiet');
    }
}
