<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('wargas', function (Blueprint $table) {
            // Cek apakah kolom status sudah ada
            if (!Schema::hasColumn('wargas', 'status')) {
                $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            }
        });
    }

    public function down()
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};