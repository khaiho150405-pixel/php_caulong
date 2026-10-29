<?php

namespace App\Models;

use Database\Factories\SanPhamFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPham extends Model
{
    /** @use HasFactory<SanPhamFactory> */
    // SoftDeletes: xóa mềm sản phẩm (cột deleted_at) để giữ lại lịch sử đơn hàng
    use HasFactory, SoftDeletes;

    protected $table = 'SanPham';

    protected $primaryKey = 'MaSanPham';

    public $timestamps = false;

    protected $fillable = [
        'TenSanPham',
        'MoTa',
        'GiaGoc',
        'HinhAnhDaiDien',
        'MaLoai',
        'MaNhaCungCap',
        'MaHang',
        'MaKhuyenMai',
        'CoSize',
        'CoMau',
        'NgayTao',
    ];

    protected $casts = [
        'GiaGoc' => 'decimal:2',
        'CoSize' => 'boolean',
        'CoMau' => 'boolean',
    ];

    protected static function newFactory(): SanPhamFactory
    {
        return SanPhamFactory::new();
    }

    /**
     * Scope tìm kiếm theo từ khóa: tên sản phẩm, tên hãng hoặc tên loại.
     */
    public function scopeTimKiem(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('TenSanPham', 'LIKE', "%{$keyword}%")
                ->orWhereHas('hang', fn (Builder $hq) => $hq->where('TenHang', 'LIKE', "%{$keyword}%"))
                ->orWhereHas('loaiSanPham', fn (Builder $lq) => $lq->where('TenLoai', 'LIKE', "%{$keyword}%"));
        });
    }

    /**
     * Biểu thức SQL tính giá bán sau khuyến mãi (khớp với accessor gia_sau_khuyen_mai).
     * Có 2 tham số ràng buộc (ngày hôm nay) cho điều kiện khuyến mãi còn hiệu lực.
     */
    private const GIA_BAN_SQL = '(SanPham.GiaGoc - COALESCE((SELECT CASE
            WHEN k.GiamToiDa IS NOT NULL AND k.GiamToiDa > 0 AND SanPham.GiaGoc * k.PhanTramGiam / 100 > k.GiamToiDa THEN k.GiamToiDa
            ELSE SanPham.GiaGoc * k.PhanTramGiam / 100 END
        FROM KhuyenMai k
        WHERE k.MaKhuyenMai = SanPham.MaKhuyenMai
          AND (k.NgayBatDau IS NULL OR k.NgayBatDau <= ?)
          AND (k.NgayKetThuc IS NULL OR k.NgayKetThuc >= ?)), 0))';

    /**
     * Lọc theo khoảng giá bán thực tế (đã trừ khuyến mãi).
     */
    public function scopeLocTheoGia(Builder $query, ?float $min, ?float $max): Builder
    {
        $today = now()->toDateString();

        if ($min !== null && $min > 0) {
            $query->whereRaw(self::GIA_BAN_SQL.' >= CAST(? AS DECIMAL(15,2))', [$today, $today, $min]);
        }
        if ($max !== null && $max > 0) {
            $query->whereRaw(self::GIA_BAN_SQL.' <= CAST(? AS DECIMAL(15,2))', [$today, $today, $max]);
        }

        return $query;
    }

    /**
     * Chỉ lấy sản phẩm đang có khuyến mãi còn hiệu lực.
     */
    public function scopeDangGiamGia(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->whereHas('khuyenMai', function (Builder $k) use ($today) {
            $k->where('PhanTramGiam', '>', 0)
                ->where(fn (Builder $d) => $d->whereNull('NgayBatDau')->orWhere('NgayBatDau', '<=', $today))
                ->where(fn (Builder $d) => $d->whereNull('NgayKetThuc')->orWhere('NgayKetThuc', '>=', $today));
        });
    }

    /**
     * Chỉ lấy sản phẩm còn ít nhất một biến thể còn tồn kho.
     */
    public function scopeConHang(Builder $query): Builder
    {
        return $query->whereHas('chiTietSanPhams', fn (Builder $c) => $c->where('SoLuongTon', '>', 0));
    }

    /**
     * Sắp xếp: moi (mặc định), gia_tang, gia_giam, ten.
     */
    public function scopeSapXep(Builder $query, ?string $kieu): Builder
    {
        $today = now()->toDateString();

        return match ($kieu) {
            'gia_tang' => $query->orderByRaw(self::GIA_BAN_SQL.' asc', [$today, $today])->orderByDesc('MaSanPham'),
            'gia_giam' => $query->orderByRaw(self::GIA_BAN_SQL.' desc', [$today, $today])->orderByDesc('MaSanPham'),
            'ten' => $query->orderBy('TenSanPham'),
            default => $query->orderByDesc('NgayTao')->orderByDesc('MaSanPham'),
        };
    }

    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class, 'MaLoai', 'MaLoai');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'MaNhaCungCap', 'MaNhaCungCap');
    }

    public function hang()
    {
        return $this->belongsTo(Hang::class, 'MaHang', 'MaHang');
    }

    public function khuyenMai()
    {
        return $this->belongsTo(KhuyenMai::class, 'MaKhuyenMai', 'MaKhuyenMai');
    }

    public function chiTietSanPhams()
    {
        return $this->hasMany(ChiTietSanPham::class, 'MaSanPham', 'MaSanPham');
    }

    public function phanHois()
    {
        return $this->hasMany(PhanHoi::class, 'MaSanPham', 'MaSanPham');
    }

    /**
     * Tính giá bán sau khuyến mãi
     */
    public function getGiaSauKhuyenMaiAttribute()
    {
        $giaGoc = (float) ($this->GiaGoc ?? 0);
        if ($giaGoc <= 0) {
            return 0;
        }

        if ($this->khuyenMai && $this->khuyenMai->isEffective()) {
            $phanTram = (float) $this->khuyenMai->PhanTramGiam;
            $giam = ($giaGoc * $phanTram) / 100;

            if ($this->khuyenMai->GiamToiDa && $giam > (float) $this->khuyenMai->GiamToiDa) {
                $giam = (float) $this->khuyenMai->GiamToiDa;
            }

            return max(0, $giaGoc - $giam);
        }

        return $giaGoc;
    }

    /**
     * Ảnh đại diện dạng thu nhỏ (WebP) để danh sách tải nhanh; không có bản thu nhỏ thì dùng ảnh gốc.
     */
    public function getAnhThumbAttribute(): string
    {
        $goc = $this->HinhAnhDaiDien ?: 'default-product.jpg';
        $thumb = 'thumbs/'.pathinfo($goc, PATHINFO_FILENAME).'.webp';

        return is_file(public_path('images/'.$thumb)) ? $thumb : $goc;
    }

    /**
     * Tính điểm đánh giá trung bình
     */
    public function getTrungBinhDanhGiaAttribute(): float
    {
        $avg = $this->phanHois()->avg('DanhGia');

        return $avg ? round((float) $avg, 1) : 5.0;
    }
}
