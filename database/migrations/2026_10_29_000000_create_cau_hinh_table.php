<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('CauHinh', function (Blueprint $table) {
            $table->id();
            $table->string('Khoa', 60)->unique();
            $table->text('GiaTri')->nullable();
            $table->string('MoTa', 255)->nullable();
            $table->timestamps();
        });

        DB::table('CauHinh')->insert([
            [
                'Khoa' => 'logo_url',
                'GiaTri' => 'images/HUIT.jpg',
                'MoTa' => 'Đường dẫn logo hệ thống',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Khoa' => 'site_name',
                'GiaTri' => 'Shop Cầu Lông',
                'MoTa' => 'Tên thương hiệu hệ thống',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Khoa' => 'site_description',
                'GiaTri' => 'Cửa hàng dụng cụ và phụ kiện cầu lông chính hãng',
                'MoTa' => 'Mô tả ngắn gọn hệ thống',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('CauHinh');
    }
};
