<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warga_id');
            $table->unsignedBigInteger('iuran_id');
            $table->decimal('jumlah_dibayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->enum('status', ['pending', 'verified', 'ditolak'])->default('pending');
            $table->string('bukti_transfer', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->foreign('warga_id')->references('id')->on('wargas')->onDelete('cascade');
            $table->foreign('iuran_id')->references('id')->on('iurans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
};