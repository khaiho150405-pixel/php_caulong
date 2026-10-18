<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    protected $table = 'MauSac';
    protected $primaryKey = 'MaMau';
    public $timestamps = false;

    protected $fillable = [
        'TenMau',
    ];

    public function chiTietSanPhams()
    {
        return $this->hasMany(ChiTietSanPham::class, 'MaMau', 'MaMau');
    }
}
