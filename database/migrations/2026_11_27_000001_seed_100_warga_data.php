<?php

use Database\Seeders\WargaSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new WargaSeeder())->run();
    }

    public function down(): void
    {
        // Hapus hanya 100 warga dummy berdasarkan pola email warga001@rt.com .. warga100@rt.com
        $emails = [];
        for ($i = 1; $i <= 100; $i++) {
            $emails[] = sprintf('warga%03d@rt.com', $i);
        }
        \App\Models\User::whereIn('email', $emails)->delete();
    }
};
