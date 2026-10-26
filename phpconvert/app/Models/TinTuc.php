<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Bài đăng trên bảng tin cầu lông. Xóa mềm để không mất lịch sử.
 */
class TinTuc extends Model
{
    use SoftDeletes;

    protected $table = 'TinTuc';
    protected $primaryKey = 'MaTin';
    public $timestamps = false;

    /** Các loại tin: khóa => [nhãn, biểu tượng Font Awesome] */
    public const LOAI = [
        'san-pham-hot' => ['Sản phẩm hot', 'fa-fire'],
        'san-pham-moi' => ['Sản phẩm mới', 'fa-star'],
        'tran-dau' => ['Trận đấu', 'fa-trophy'],
        'tuyen-thu' => ['Tuyển thủ', 'fa-user-large'],
        'giai-dau' => ['Giải đấu', 'fa-medal'],
    ];

    protected $fillable = [
        'TieuDe', 'TomTat', 'NoiDung', 'LoaiTin', 'MaSanPham', 'MaTranDau', 'MaTaiKhoan',
        'NguonTin', 'LienKetNguon', 'NoiBat', 'NgayDang',
    ];

    protected $casts = [
        'NgayDang' => 'datetime',
        'NoiBat' => 'boolean',
        'LuotXem' => 'integer',
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MaSanPham', 'MaSanPham');
    }

    public function tranDau()
    {
        return $this->belongsTo(TranDau::class, 'MaTranDau', 'MaTranDau');
    }

    public function tacGia()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTaiKhoan', 'MaTaiKhoan');
    }

    /** Chỉ lấy các bài đã đến giờ đăng. */
    public function scopeDaDang(Builder $query): Builder
    {
        return $query->where('NgayDang', '<=', now());
    }

    public function scopeLoai(Builder $query, ?string $loai): Builder
    {
        return array_key_exists((string) $loai, self::LOAI) ? $query->where('LoaiTin', $loai) : $query;
    }

    /** Tìm theo tiêu đề, tóm tắt hoặc nội dung. */
    public function scopeTimKiem(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('TieuDe', 'LIKE', "%{$keyword}%")
                ->orWhere('TomTat', 'LIKE', "%{$keyword}%")
                ->orWhere('NoiDung', 'LIKE', "%{$keyword}%");
        });
    }

    public function getNhanLoaiAttribute(): string
    {
        return self::LOAI[$this->LoaiTin][0] ?? 'Tin tức';
    }

    public function getIconLoaiAttribute(): string
    {
        return self::LOAI[$this->LoaiTin][1] ?? 'fa-newspaper';
    }

    /** Ảnh minh họa: ảnh sản phẩm thật gắn kèm (nếu có), không dùng ảnh không rõ bản quyền. */
    public function getAnhAttribute(): ?string
    {
        return $this->sanPham ? 'images/' . $this->sanPham->anh_thumb : null;
    }
}
