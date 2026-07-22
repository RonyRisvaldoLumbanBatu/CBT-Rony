<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Peserta - {{ $classroom->name }}</title>
    <style>
        body { font-family: sans-serif; color: #222; font-size: 11px; }
        .grid { width: 100%; border-collapse: separate; border-spacing: 6px; }
        .kartu {
            width: 46%; border: 1.5px solid #333; border-radius: 6px;
            padding: 8px 10px; vertical-align: top;
        }
        .kop { border-bottom: 1.5px solid #333; padding-bottom: 5px; margin-bottom: 6px; }
        .kop h3 { margin: 0; font-size: 12px; }
        .kop p { margin: 1px 0 0; font-size: 9px; color: #555; }
        table.isi { width: 100%; font-size: 10.5px; }
        table.isi td { padding: 1.5px 0; vertical-align: top; }
        td.k { width: 34%; color: #555; }
        .kredensial { background: #f2f2f2; border-radius: 4px; padding: 4px 7px; margin-top: 5px; font-family: monospace; font-size: 11px; }
        .catatan { font-size: 8px; color: #888; margin-top: 4px; }
    </style>
</head>
<body>

    <table class="grid">
        @foreach($classroom->students->chunk(2) as $pair)
            <tr>
                @foreach($pair as $siswa)
                    <td class="kartu">
                        <div class="kop">
                            <h3>KARTU PESERTA UJIAN</h3>
                            <p>{{ cbt_institution() }} &mdash; {{ cbt_name() }}@if(app_setting('academic_year')) &middot; TA {{ app_setting('academic_year') }}@endif</p>
                        </div>
                        <table class="isi">
                            <tr><td class="k">Nama</td><td><strong>{{ $siswa->name }}</strong></td></tr>
                            <tr><td class="k">No. Induk</td><td>{{ $siswa->nis ?? '-' }}</td></tr>
                            <tr><td class="k">{{ term('kelas') }}</td><td>{{ $classroom->name }}{{ $classroom->major ? ' - '.$classroom->major->name : '' }}</td></tr>
                        </table>
                        <div class="kredensial">
                            Username : <strong>{{ $siswa->username ?? '-' }}</strong><br>
                            Password : <strong>{{ $siswa->plain_password ?? '(diatur admin)' }}</strong>
                        </div>
                        <p class="catatan">Jaga kerahasiaan kartu ini. Password dapat direset oleh administrator.</p>
                    </td>
                @endforeach
                @if($pair->count() < 2)<td style="border: none;"></td>@endif
            </tr>
        @endforeach
    </table>

    @if($classroom->students->isEmpty())
        <p style="text-align:center; color:#999; margin-top:40px;">Belum ada {{ strtolower(term('siswa')) }} di {{ strtolower(term('kelas')) }} ini.</p>
    @endif

</body>
</html>
