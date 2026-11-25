<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // 1. Pengumuman RT
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('konten');
            $table->enum('kategori', ['umum', 'penting', 'darurat'])->default('umum');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });

        // 2. Surat Pengantar Digital
        Schema::create('surat_pengantars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('cascade');
            $table->string('no_surat')->nullable();
            $table->enum('jenis_surat', [
                'domisili', 'ktp', 'skck', 'keterangan_usaha',
                'keterangan_tidak_mampu', 'keterangan_menikah', 'lainnya'
            ]);
            $table->string('keperluan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'diproses', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('diproses_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamps();
        });

        // 3. Laporan / Aspirasi Warga
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('kategori', ['infrastruktur', 'kebersihan', 'keamanan', 'sosial', 'lainnya'])->default('lainnya');
            $table->string('lokasi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['baru', 'diproses', 'selesai', 'ditolak'])->default('baru');
            $table->text('tanggapan_admin')->nullable();
            $table->timestamps();
        });

        // 4. Jadwal Kegiatan RT
        Schema::create('jadwal_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori', ['rapat', 'gotong_royong', 'posyandu', 'arisan', 'olahraga', 'lainnya'])->default('lainnya');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });

        // 5. Kontak Darurat
        Schema::create('kontak_darurats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nomor_telepon');
            $table->string('kategori'); // Ketua RT, Keamanan, PLN, Pemadam, Puskesmas, Polisi, dll
            $table->string('keterangan')->nullable();
            $table->string('icon')->default('fas fa-phone');
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // 6. Kartu Keluarga (anggota keluarga)
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade'); // kepala keluarga
            $table->string('no_kk', 20);
            $table->string('nama_lengkap');
            $table->string('nik', 20)->nullable();
            $table->enum('hubungan', ['kepala_keluarga', 'istri', 'anak', 'orang_tua', 'mertua', 'cucu', 'lainnya']);
            $table->enum('jenis_kelamin', ['laki_laki', 'perempuan']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('status_kawin', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])->default('belum_kawin');
            $table->string('pekerjaan')->nullable();
            $table->timestamps();
        });

        // 7. Panic Button / SOS
        Schema::create('panic_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->onDelete('cascade');
            $table->enum('jenis', ['kebakaran', 'kemalingan', 'medis', 'kecelakaan', 'lainnya']);
            $table->text('keterangan')->nullable();
            $table->string('lokasi')->nullable();
            $table->enum('status', ['aktif', 'ditangani', 'selesai'])->default('aktif');
            $table->timestamp('ditangani_at')->nullable();
            $table->foreignId('ditangani_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Seed awal Kontak Darurat standar nasional & RT
        $now = now();
        DB::table('kontak_darurats')->insert([
            [
                'nama' => 'Ketua RT / Pengurus RT',
                'nomor_telepon' => '081234567890',
                'kategori' => 'Pengurus RT',
                'keterangan' => 'Koordinasi warga, surat pengantar, & darurat lingkungan',
                'icon' => 'fas fa-user-tie',
                'urutan' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Pos Keamanan / Satpam RT',
                'nomor_telepon' => '081298765432',
                'kategori' => 'Keamanan',
                'keterangan' => 'Jaga 24 jam — laporan orang mencurigakan & tamu',
                'icon' => 'fas fa-shield-halved',
                'urutan' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Panggilan Darurat Nasional',
                'nomor_telepon' => '112',
                'kategori' => 'Darurat',
                'keterangan' => 'Layanan darurat terpadu bebas pulsa',
                'icon' => 'fas fa-triangle-exclamation',
                'urutan' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Kepolisian (Polri)',
                'nomor_telepon' => '110',
                'kategori' => 'Polisi',
                'keterangan' => 'Laporan tindak kriminal & gangguan keamanan',
                'icon' => 'fas fa-building-shield',
                'urutan' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Pemadam Kebakaran',
                'nomor_telepon' => '113',
                'kategori' => 'Pemadam',
                'keterangan' => 'Penanganan kebakaran & penyelamatan darurat',
                'icon' => 'fas fa-fire-extinguisher',
                'urutan' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'Ambulans / Layanan Medis',
                'nomor_telepon' => '119',
                'kategori' => 'Puskesmas',
                'keterangan' => 'Pertolongan medis gawat darurat',
                'icon' => 'fas fa-truck-medical',
                'urutan' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama' => 'PLN (Gangguan Listrik)',
                'nomor_telepon' => '123',
                'kategori' => 'PLN',
                'keterangan' => 'Laporan mati lampu, kabel putus, atau korsleting',
                'icon' => 'fas fa-bolt',
                'urutan' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('panic_buttons');
        Schema::dropIfExists('kartu_keluargas');
        Schema::dropIfExists('kontak_darurats');
        Schema::dropIfExists('jadwal_kegiatans');
        Schema::dropIfExists('laporans');
        Schema::dropIfExists('surat_pengantars');
        Schema::dropIfExists('pengumumans');
    }
};
