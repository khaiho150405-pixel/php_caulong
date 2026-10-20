<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    protected $table = 'NhaCungCap';
    protected $primaryKey = 'MaNhaCungCap';
    public $timestamps = false;

    protected $fillable = [
        'TenNhaCungCap',
        'DiaChi',
        'SoDienThoai',
        'Email',
    ];

    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MaNhaCungCap', 'MaNhaCungCap');
    }
}
