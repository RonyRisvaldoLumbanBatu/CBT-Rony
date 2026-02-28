<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai: {{ $exam->title }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #1e40af; }
        .info { margin-bottom: 20px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f3f4f6; color: #111; font-weight: bold; }
        .score { font-weight: bold; color: #16a34a; text-align: center; }
        .footer { margin-top: 30px; font-size: 12px; text-align: right; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Hasil Ujian (CBT)</h2>
        <p>{{ $exam->title }}</p>
    </div>

    <div class="info">
        <strong>Total Waktu:</strong> {{ $exam->time_limit }} Menit<br>
        <strong>Dicetak Pada:</strong> {{ now()->format('d M Y, H:i') }} WIB
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 40%;">Nama Siswa</th>
                <th style="width: 30%;">Waktu Pengumpulan</th>
                <th style="width: 25%; text-align: center;">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $result)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $result->user->name }}</td>
                    <td>{{ $result->created_at->format('d/m/Y H:i') }}</td>
                    <td class="score">{{ $result->score }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #999;">Belum ada siswa yang mengerjakan ujian ini.</td>
                </tr>
            @endempty
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem Ujian Online
    </div>

</body>
</html>