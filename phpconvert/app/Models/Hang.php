<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hang extends Model
{
    protected $table = 'Hang';
    protected $primaryKey = 'MaHang';
    public $timestamps = false;

    protected $fillable = [
        'TenHang',
        'QuocGia',
    ];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MaHang', 'MaHang');
    }
}
