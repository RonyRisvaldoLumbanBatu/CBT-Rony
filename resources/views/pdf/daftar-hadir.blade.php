<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $exam->title }}</title>
    <style>
        body { font-family: sans-serif; color: #222; font-size: 11px; }
        .kop { text-align: center; border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 14px; }
        .kop h2 { margin: 0; font-size: 15px; }
        .kop p { margin: 2px 0 0; font-size: 10px; color: #555; }
        table.info { margin-bottom: 12px; font-size: 11px; }
        table.info td { padding: 1px 8px 1px 0; }
        table.hadir { width: 100%; border-collapse: collapse; }
        table.hadir th, table.hadir td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        table.hadir th { background: #f0f0f0; font-size: 10px; text-transform: uppercase; }
        .ttd { width: 110px; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>DAFTAR HADIR PESERTA UJIAN</h2>
        <p>{{ cbt_institution() }}@if(app_setting('academic_year')) &mdash; Tahun Ajaran {{ app_setting('academic_year') }}@endif</p>
    </div>

    <table class="info">
        <tr><td>Ujian</td><td>: <strong>{{ $exam->title }}</strong></td></tr>
        <tr><td>Mata Pelajaran</td><td>: {{ $exam->subject?->name ?? '-' }}</td></tr>
        <tr><td>{{ term('kelas') }}</td><td>: {{ $exam->classroom?->name ?? 'Semua '.term('kelas') }}</td></tr>
        <tr><td>{{ term('guru') }}</td><td>: {{ $exam->teacher?->name ?? '-' }}</td></tr>
        <tr><td>Durasi</td><td>: {{ $exam->time_limit }} Menit</td></tr>
    </table>

    <table class="hadir">
        <thead>
            <tr>
                <th style="width:4%; text-align:center;">No</th>
                <th>Nama {{ term('siswa') }}</th>
                <th style="width:14%;">No. Induk</th>
                <th style="width:16%;">Status</th>
                <th class="ttd">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $i => $p)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->nis ?? '-' }}</td>
                    <td>{{ isset($results[$p->id]) ? 'Selesai ('.$results[$p->id]->created_at->format('H:i').')' : '' }}</td>
                    <td>{{ $i + 1 }}. </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center; color:#999;">Belum ada peserta.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table style="width:100%; margin-top: 30px; font-size: 11px;">
        <tr>
            <td style="width:60%;"></td>
            <td style="text-align:center;">
                .................. , ..........................<br>
                Pengawas Ujian,<br><br><br><br>
                ( ..................................... )
            </td>
        </tr>
    </table>
</body>
</html>
