<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Kết quả một trận đấu cầu lông thực tế (có nguồn tham khảo).
 */
class TranDau extends Model
{
    protected $table = 'TranDau';

    protected $primaryKey = 'MaTranDau';

    public $timestamps = false;

    protected $fillable = [
        'TenGiaiDau', 'Vong', 'NoiDung', 'DoiA', 'DoiB', 'TiSo', 'DoiThang',
        'NgayThiDau', 'DiaDiem', 'NguonTin', 'LienKetNguon',
    ];

    protected $casts = [
        'NgayThiDau' => 'date',
    ];

    public function tinTucs()
    {
        return $this->hasMany(TinTuc::class, 'MaTranDau', 'MaTranDau');
    }

    /** Tìm theo giải đấu, nội dung hoặc tên hai bên. */
    public function scopeTimKiem(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('TenGiaiDau', 'LIKE', "%{$keyword}%")
                ->orWhere('NoiDung', 'LIKE', "%{$keyword}%")
                ->orWhere('DoiA', 'LIKE', "%{$keyword}%")
                ->orWhere('DoiB', 'LIKE', "%{$keyword}%");
        });
    }

    public function getTenDoiThangAttribute(): ?string
    {
        return match ($this->DoiThang) {
            'A' => $this->DoiA,
            'B' => $this->DoiB,
            default => null,
        };
    }
}
