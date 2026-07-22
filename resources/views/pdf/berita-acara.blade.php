<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $exam->title }}</title>
    <style>
        body { font-family: sans-serif; color: #222; font-size: 12px; line-height: 1.55; }
        .kop { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 16px; }
        .kop h2 { margin: 0; font-size: 15px; }
        .kop p { margin: 2px 0 0; font-size: 10px; color: #555; }
        table.info { margin: 10px 0 14px; }
        table.info td { padding: 1.5px 8px 1.5px 0; vertical-align: top; }
        .isian { border-bottom: 1px dotted #666; display: inline-block; min-width: 180px; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>BERITA ACARA PELAKSANAAN UJIAN</h2>
        <p>{{ cbt_institution() }}@if(app_setting('academic_year')) &mdash; Tahun Ajaran {{ app_setting('academic_year') }}@endif</p>
    </div>

    <p>Pada hari ini <span class="isian"></span> tanggal <span class="isian"></span>
        telah diselenggarakan ujian berbasis komputer ({{ cbt_name() }}) dengan rincian berikut:</p>

    <table class="info">
        <tr><td style="width:170px;">Nama Ujian</td><td>: <strong>{{ $exam->title }}</strong></td></tr>
        <tr><td>Mata Pelajaran</td><td>: {{ $exam->subject?->name ?? '-' }}</td></tr>
        <tr><td>{{ term('kelas') }}</td><td>: {{ $exam->classroom?->name ?? 'Semua '.term('kelas') }}</td></tr>
        <tr><td>{{ term('guru') }} Pengampu</td><td>: {{ $exam->teacher?->name ?? '-' }}</td></tr>
        <tr><td>Durasi</td><td>: {{ $exam->time_limit }} Menit</td></tr>
        @if($exam->starts_at)
            <tr><td>Jadwal</td><td>: {{ $exam->starts_at->translatedFormat('d F Y H:i') }}{{ $exam->ends_at ? ' s.d. '.$exam->ends_at->translatedFormat('d F Y H:i') : '' }}</td></tr>
        @endif
        <tr><td>Jumlah Peserta Terdaftar</td><td>: {{ $totalPeserta }} {{ strtolower(term('siswa')) }}</td></tr>
        <tr><td>Jumlah Mengumpulkan</td><td>: {{ $exam->results_count }} {{ strtolower(term('siswa')) }}</td></tr>
        <tr><td>Tidak Hadir / Tidak Selesai</td><td>: {{ max(0, $totalPeserta - $exam->results_count) }} {{ strtolower(term('siswa')) }}</td></tr>
    </table>

    <p>Catatan kejadian selama ujian berlangsung:</p>
    <p style="border:1px solid #999; border-radius:4px; min-height:90px; padding:8px;"></p>

    <p>Demikian berita acara ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>

    <table style="width:100%; margin-top: 26px;">
        <tr>
            <td style="width:50%; text-align:center;">
                Pengawas Ujian,<br><br><br><br>
                ( ..................................... )
            </td>
            <td style="text-align:center;">
                {{ term('guru') }} Pengampu,<br><br><br><br>
                ( {{ $exam->teacher?->name ?? '.....................................' }} )
            </td>
        </tr>
    </table>
</body>
</html>
