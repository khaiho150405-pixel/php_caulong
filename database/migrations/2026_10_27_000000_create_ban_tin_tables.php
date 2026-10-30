<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TranDau', function (Blueprint $table) {
            $table->increments('MaTranDau');
            $table->string('TenGiaiDau', 150);
            $table->string('Vong', 60);
            $table->string('NoiDung', 60);
            $table->string('DoiA', 150);
            $table->string('DoiB', 150);
            $table->string('TiSo', 60)->nullable();
            $table->char('DoiThang', 1)->nullable();
            $table->date('NgayThiDau');
            $table->string('DiaDiem', 120)->nullable();
            $table->string('NguonTin', 150)->nullable();
            $table->string('LienKetNguon', 300)->nullable();

            $table->index('NgayThiDau');
        });

        Schema::create('TinTuc', function (Blueprint $table) {
            $table->increments('MaTin');
            $table->string('TieuDe', 200);
            $table->string('TomTat', 300)->nullable();
            $table->text('NoiDung');
            $table->string('LoaiTin', 30);
            $table->unsignedInteger('MaSanPham')->nullable();
            $table->unsignedInteger('MaTranDau')->nullable();
            $table->unsignedInteger('MaTaiKhoan')->nullable();
            $table->string('NguonTin', 150)->nullable();
            $table->string('LienKetNguon', 300)->nullable();
            $table->boolean('NoiBat')->default(false);
            $table->unsignedInteger('LuotXem')->default(0);
            $table->timestamp('NgayDang')->useCurrent();
            $table->softDeletes();

            $table->index('LoaiTin');
            $table->index('NgayDang');
            $table->foreign('MaSanPham')->references('MaSanPham')->on('SanPham')->nullOnDelete();
            $table->foreign('MaTranDau')->references('MaTranDau')->on('TranDau')->nullOnDelete();
            $table->foreign('MaTaiKhoan')->references('MaTaiKhoan')->on('TaiKhoan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TinTuc');
        Schema::dropIfExists('TranDau');
    }
};
