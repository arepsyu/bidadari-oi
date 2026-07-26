<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Detail Data - {{ $user->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #222; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #005093; }
        .header p { margin: 2px 0; color: #444; font-size: 11px; }
        .info td { padding: 3px 8px; }
        .klaster-title { background: #005093; color: #fff; padding: 5px 8px; margin-top: 14px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.data th, table.data td { border: 1px solid #999; padding: 5px 7px; text-align: left; }
        table.data th { background-color: #eaf4fb; }
        .badge-ok { color: #1a7a3d; font-weight: bold; }
        .badge-no { color: #b02a2a; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Detail Kelengkapan Data</h2>
        <p>Bank Informasi Data Kabupaten Layak Anak Terintegrasi Ogan Ilir (BIDADARI OI)</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table class="info">
        <tr><td><strong>Organisasi</strong></td><td>: {{ $user->organisasi ?? '-' }}</td></tr>
        <tr><td><strong>Nama User</strong></td><td>: {{ $user->name }}</td></tr>
        <tr><td><strong>Kategori</strong></td><td>: {{ $user->kategoriLabel() }}</td></tr>
    </table>

    @foreach($pertanyaans as $klasterKey => $groupByKlaster)
        @php $klasterNama = explode('|', $klasterKey)[1]; @endphp
        <div class="klaster-title">{{ $klasterNama }}</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Indikator / Pertanyaan</th>
                    <th>Status</th>
                    <th>Isi / Nama File</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupByKlaster as $p)
                    @php $sub = $submissions->get($p->id); @endphp
                    <tr>
                        <td>{{ $p->indikator->nama }}: {{ $p->teks }}</td>
                        <td>
                            @if($sub)<span class="badge-ok">Sudah Diisi</span>@else<span class="badge-no">Belum Diisi</span>@endif
                        </td>
                        <td>
                            @if($sub){{ $p->tipe === 'file' ? $sub->file_original_name : $sub->value }}@else - @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <div class="footer">BIDADARI OI &mdash; Dokumen ini dihasilkan otomatis oleh sistem.</div>
</body>
</html>
