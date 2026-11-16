<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->string('rfid_uid', 50)->nullable()->unique()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'rfid_uid')) {
                $table->dropColumn('rfid_uid');
            }
        });
    }
};
