<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KhuyenMai extends Model
{
    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKhuyenMai';
    public $timestamps = false;

    protected $fillable = [
        'TenChuongTrinh',
        'MoTa',
        'NgayBatDau',
        'NgayKetThuc',
        'PhanTramGiam',
        'GiamToiDa',
        'SoLuongSuDung',
    ];

    protected $casts = [
        'NgayBatDau' => 'date',
        'NgayKetThuc' => 'date',
        'PhanTramGiam' => 'decimal:2',
        'GiamToiDa' => 'decimal:2',
    ];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MaKhuyenMai', 'MaKhuyenMai');
    }

    public function isEffective(): bool
    {
        $today = Carbon::today();
        $start = $this->NgayBatDau ? Carbon::parse($this->NgayBatDau) : null;
        $end = $this->NgayKetThuc ? Carbon::parse($this->NgayKetThuc) : null;

        if ($start && $start->gt($today)) {
            return false;
        }
        if ($end && $end->lt($today)) {
            return false;
        }
        return true;
    }
}
