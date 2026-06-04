<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_iuran', 100);
            $table->decimal('nominal', 15, 2);
            $table->enum('periode', ['bulanan', 'tahunan']);
            $table->date('tanggal_jatuh_tempo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('iurans');
    }
};