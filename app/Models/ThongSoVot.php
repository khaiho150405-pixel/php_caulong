<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThongSoVot extends Model
{
    protected $table = 'ThongSoVot';

    protected $primaryKey = 'MaThongSo';

    public $timestamps = false;

    protected $fillable = [
        'MaChiTiet',
        'DoCanBang',
        'TrongLuong',
        'DoCung',
        'ChieuDai',
        'SucCang',
    ];

    public function chiTietSanPham()
    {
        return $this->belongsTo(ChiTietSanPham::class, 'MaChiTiet', 'MaChiTiet');
    }
}
