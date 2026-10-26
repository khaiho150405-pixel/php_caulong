<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột deleted_at cho bảng SanPham để áp dụng Soft Delete:
     * xóa sản phẩm không làm mất dữ liệu ở các đơn hàng đã đặt.
     */
    public function up(): void
    {
        Schema::table('SanPham', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('SanPham', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
