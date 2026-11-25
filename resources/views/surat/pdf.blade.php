<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengantar - {{ $surat->no_surat }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #111;
            line-height: 1.5;
            margin: 30px 40px;
        }
        .kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop h2 {
            margin: 0;
            font-size: 15pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop h3 {
            margin: 2px 0;
            font-size: 13pt;
            text-transform: uppercase;
        }
        .kop p {
            margin: 0;
            font-size: 10pt;
            color: #333;
        }
        .judul-surat {
            text-align: center;
            margin: 20px 0;
        }
        .judul-surat h4 {
            margin: 0;
            font-size: 13pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .judul-surat p {
            margin: 4px 0 0 0;
            font-size: 11pt;
        }
        .biodata {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 20px 20px;
        }
        .biodata td {
            padding: 4px 6px;
            vertical-align: top;
        }
        .biodata td.label { width: 170px; }
        .biodata td.sep { width: 15px; }
        .ttd-wrap {
            width: 100%;
            margin-top: 40px;
        }
        .ttd-box {
            width: 45%;
            float: right;
            text-align: center;
        }
        .verify-box {
            width: 48%;
            float: left;
            border: 1px dashed #666;
            padding: 10px;
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #333;
            margin-top: 25px;
        }
        .stempel-digital {
            display: inline-block;
            border: 2px solid #1e3a8a;
            color: #1e3a8a;
            padding: 6px 12px;
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            font-weight: bold;
            margin: 12px 0;
            border-radius: 4px;
        }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>RUKUN TETANGGA 01 / RUKUN WARGA 01</h2>
        <h3>PENGURUS LINGKUNGAN SI-IURAN RT DIGITAL</h3>
        <p>Sistem Administrasi & Layanan Warga Terpadu</p>
    </div>

    <div class="judul-surat">
        <h4>{{ strtoupper($surat->jenis_label) }}</h4>
        <p>Nomor: {{ $surat->no_surat }}</p>
    </div>

    <p>Yang bertanda tangan di bawah ini, Ketua RT 01 menerangkan dengan sebenarnya bahwa:</p>

    <table class="biodata">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="sep">:</td>
            <td><strong>{{ $surat->warga->nama_lengkap ?? $surat->user->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="sep">:</td>
            <td>{{ $surat->warga->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor KK</td>
            <td class="sep">:</td>
            <td>{{ $surat->warga->no_kk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Telepon</td>
            <td class="sep">:</td>
            <td>{{ $surat->warga->no_telp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="sep">:</td>
            <td>{{ $surat->warga->alamat ?? 'Lingkungan RT 01' }}</td>
        </tr>
        <tr>
            <td class="label">Keperluan</td>
            <td class="sep">:</td>
            <td><strong>{{ $surat->keperluan }}</strong></td>
        </tr>
        @if($surat->keterangan)
        <tr>
            <td class="label">Keterangan</td>
            <td class="sep">:</td>
            <td>{{ $surat->keterangan }}</td>
        </tr>
        @endif
    </table>

    <p>
        Orang tersebut di atas adalah benar merupakan warga yang berdomisili di wilayah kami.
        Surat pengantar ini diberikan untuk dipergunakan sebagaimana mestinya sesuai keperluan yang tercantum.
    </p>

    @if($surat->catatan_admin)
    <p><em>Catatan Pengurus: {{ $surat->catatan_admin }}</em></p>
    @endif

    <p>Demikian surat pengantar ini dibuat agar dapat digunakan sebagaimana mestinya.</p>

    <div class="ttd-wrap">
        <div class="verify-box">
            <strong>VERIFIKASI DIGITAL</strong><br>
            Kode Verifikasi: <strong>{{ strtoupper(substr(md5($surat->id . $surat->no_surat), 0, 12)) }}</strong><br>
            Disetujui Oleh: {{ $surat->processor->name ?? 'Ketua RT' }}<br>
            Tanggal Terbit: {{ $surat->tanggal_disetujui ? $surat->tanggal_disetujui->format('d/m/Y H:i') : date('d/m/Y') }}
        </div>

        <div class="ttd-box">
            <div>Dikeluarkan pada tanggal {{ $surat->tanggal_disetujui ? $surat->tanggal_disetujui->format('d M Y') : date('d M Y') }}</div>
            <div><strong>Ketua RT 01,</strong></div>
            <div class="stempel-digital">
                DITANDATANGANI SECARA ELEKTRONIK<br>
                SI-IURAN RT DIGITAL
            </div>
            <div><strong><u>{{ $surat->processor->name ?? 'Ketua RT' }}</u></strong></div>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
