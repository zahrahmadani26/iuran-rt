<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Ubah enum role di tabel users: tambah 'super_admin'
        // SQLite tidak support ALTER COLUMN untuk enum, jadi kita buat kolom baru
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite: tambah kolom baru lalu drop lama
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_new')->default('warga')->after('password');
            });
            DB::statement("UPDATE users SET role_new = role");
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('role_new', 'role');
            });
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin','admin','bendahara','warga') NOT NULL DEFAULT 'warga'");
        }

        // Tambah kolom status_akun ke users (pending = menunggu verifikasi super admin)
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_akun', ['pending', 'aktif', 'ditolak'])->default('aktif')->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status_akun');
        });
    }
};
