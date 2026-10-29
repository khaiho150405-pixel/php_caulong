<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanQuyen extends Model
{
    protected $table = 'PhanQuyen';

    protected $primaryKey = 'MaQuyen';

    public $timestamps = false;

    protected $fillable = [
        'TenQuyen',
    ];

    public function taiKhoans()
    {
        return $this->hasMany(TaiKhoan::class, 'MaQuyen', 'MaQuyen');
    }
}
