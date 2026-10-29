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
        'TieuDe', 'TomTat', 'NoiDung', 'HinhAnh', 'LoaiTin', 'MaSanPham', 'MaTranDau', 'MaTaiKhoan',
        'NguonTin', 'LienKetNguon', 'NoiBat', 'HienThi', 'ThuTu', 'NgayDang',
    ];

    protected $casts = [
        'NgayDang' => 'datetime',
        'NoiBat' => 'boolean',
        'HienThi' => 'boolean',
        'ThuTu' => 'integer',
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

    /** Chỉ lấy các bài đã được bật hiển thị và đã đến giờ đăng. */
    public function scopeDaDang(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('HienThi')->orWhere('HienThi', true);
        })->where('NgayDang', '<=', now());
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

    /** Ảnh minh họa: ưu tiên HinhAnh của bài viết, rồi đến ảnh sản phẩm gắn kèm. */
    public function getAnhAttribute(): ?string
    {
        if (! empty($this->HinhAnh)) {
            return $this->HinhAnh;
        }

        return $this->sanPham ? 'images/'.$this->sanPham->anh_thumb : null;
    }

    /** Trả về URL hình ảnh đầy đủ để hiển thị. */
    public function getHinhAnhUrlAttribute(): string
    {
        if (! empty($this->HinhAnh)) {
            if (str_starts_with($this->HinhAnh, 'http://') || str_starts_with($this->HinhAnh, 'https://')) {
                return $this->HinhAnh;
            }
            if (is_file(public_path($this->HinhAnh))) {
                return asset($this->HinhAnh);
            }
            if (is_file(storage_path('app/public/'.$this->HinhAnh))) {
                return asset('storage/'.$this->HinhAnh);
            }

            return asset($this->HinhAnh);
        }

        if ($this->sanPham) {
            return asset('images/'.$this->sanPham->anh_thumb);
        }

        return asset('images/news/court-journal-thumb.webp');
    }
}
