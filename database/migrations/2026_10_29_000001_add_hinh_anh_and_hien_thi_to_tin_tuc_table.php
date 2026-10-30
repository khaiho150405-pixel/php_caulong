<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('TinTuc', function (Blueprint $table) {
            $table->string('HinhAnh', 255)->nullable()->after('NoiDung');
            $table->boolean('HienThi')->default(true)->after('NoiBat');
            $table->integer('ThuTu')->default(1)->after('HienThi');
        });
    }

    public function down(): void
    {
        Schema::table('TinTuc', function (Blueprint $table) {
            $table->dropColumn(['HinhAnh', 'HienThi', 'ThuTu']);
        });
    }
};
