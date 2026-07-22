<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Monitoring BIDADARI OI</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #222; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #005093; }
        .header p { margin: 2px 0; color: #444; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background-color: #005093; color: #fff; }
        tr:nth-child(even) { background-color: #eaf4fb; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Rekap Monitoring Kelengkapan Data</h2>
        <p>Bank Informasi Data Kabupaten Layak Anak Terintegrasi Ogan Ilir (BIDADARI OI)</p>
        <p>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Organisasi</th>
                <th>Nama User</th>
                <th>Email</th>
                <th class="text-center">Data Terisi</th>
                <th class="text-center">Total Data</th>
                <th class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $i => $user)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $user->organisasi ?? '-' }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">{{ $user->submissions_count }}</td>
                <td class="text-center">{{ $totalRequirements }}</td>
                <td class="text-center">{{ $user->progress }}%</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center">Belum ada data user.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">BIDADARI OI &mdash; Dokumen ini dihasilkan otomatis oleh sistem.</div>
</body>
</html>
