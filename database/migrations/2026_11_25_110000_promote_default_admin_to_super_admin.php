<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Promosikan admin default menjadi super_admin bila masih ber-role 'admin'
        DB::table('users')
            ->where('email', 'admin@rt.com')
            ->where('role', 'admin')
            ->update([
                'role' => 'super_admin',
                'status_akun' => 'aktif',
            ]);
    }

    public function down()
    {
        // Tidak perlu revert otomatis
    }
};
