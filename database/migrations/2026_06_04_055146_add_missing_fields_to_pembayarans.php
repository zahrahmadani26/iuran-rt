<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Cek apakah kolom yang diperlukan sudah ada
            if (!Schema::hasColumn('pembayarans', 'status')) {
                $table->enum('status', ['pending', 'verified', 'ditolak'])->default('pending');
            }
            if (!Schema::hasColumn('pembayarans', 'tanggal_bayar')) {
                $table->date('tanggal_bayar');
            }
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            if (Schema::hasColumn('pembayarans', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('pembayarans', 'tanggal_bayar')) {
                $table->dropColumn('tanggal_bayar');
            }
        });
    }
};