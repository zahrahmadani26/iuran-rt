<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->string('kategori', 50);
            $table->string('deskripsi', 200);
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->string('bukti', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keuangans');
    }
};