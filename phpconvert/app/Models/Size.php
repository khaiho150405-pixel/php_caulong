<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'Size';
    protected $primaryKey = 'MaSize';
    public $timestamps = false;

    protected $fillable = [
        'TenSize',
        'LoaiSize',
    ];

    public function chiTietSanPhams()
    {
        return $this->hasMany(ChiTietSanPham::class, 'MaSize', 'MaSize');
    }
}
