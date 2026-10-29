<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoaiSanPham extends Model
{
    protected $table = 'LoaiSanPham';

    protected $primaryKey = 'MaLoai';

    public $timestamps = false;

    protected $fillable = [
        'TenLoai',
        'MoTa',
    ];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MaLoai', 'MaLoai');
    }
}
