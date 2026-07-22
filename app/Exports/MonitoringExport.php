<?php

namespace App\Exports;

use App\Models\DataRequirement;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected int $totalRequirements;
    protected int $no = 0;

    public function __construct()
    {
        $this->totalRequirements = DataRequirement::count();
    }

    public function collection()
    {
        return User::where('role', 'user')
            ->withCount('submissions')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['No', 'Organisasi', 'Nama User', 'Email', 'Data Terisi', 'Total Data', 'Persentase (%)'];
    }

    public function map($user): array
    {
        $this->no++;

        $percentage = $this->totalRequirements > 0
            ? round(($user->submissions_count / $this->totalRequirements) * 100)
            : 0;

        return [
            $this->no,
            $user->organisasi ?? '-',
            $user->name,
            $user->email,
            $user->submissions_count,
            $this->totalRequirements,
            $percentage,
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
