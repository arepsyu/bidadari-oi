<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected int $no = 0;

    public function collection()
    {
        return User::where('role', 'user')
            ->withCount('submissions')
            ->withCount(['submissions as menunggu_count' => function ($q) {
                $q->where('status', 'menunggu');
            }])
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Kategori', 'Organisasi', 'Nama User', 'Email', 'Data Terisi', 'Total Data Relevan', 'Persentase (%)', 'Menunggu Verifikasi'];
    }

    public function map($user): array
    {
        $this->no++;

        $totalRelevan = $user->pertanyaanRelevan()->count();
        $percentage = $totalRelevan > 0
            ? round(($user->submissions_count / $totalRelevan) * 100)
            : 0;

        return [
            $this->no,
            $user->kategoriLabel(),
            $user->organisasi ?? '-',
            $user->name,
            $user->email,
            $user->submissions_count,
            $totalRelevan,
            $percentage,
            $user->menunggu_count,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '005093']]],
        ];
    }
}
