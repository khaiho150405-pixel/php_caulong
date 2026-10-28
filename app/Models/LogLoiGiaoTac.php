<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogLoiGiaoTac extends Model
{
    protected $table = 'LogLoiGiaoTac';
    protected $primaryKey = 'MaLog';
    public $timestamps = false;

    protected $fillable = [
        'ThoiGian',
        'NoiDungLoi',
        'MaGiaoTac',
    ];

    protected $casts = [
        'ThoiGian' => 'datetime',
    ];
}
