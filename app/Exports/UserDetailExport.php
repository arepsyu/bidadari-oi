<?php

namespace App\Exports;

use App\Models\Submission;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserDetailExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection()
    {
        $pertanyaans = $this->user->pertanyaanRelevan()->with('indikator.klaster')->get();
        $submissions = Submission::where('user_id', $this->user->id)->get()->keyBy('pertanyaan_id');

        return $pertanyaans->map(function ($p) use ($submissions) {
            $sub = $submissions->get($p->id);
            return [
                'klaster' => $p->indikator->klaster->nama,
                'indikator' => $p->indikator->nama,
                'pertanyaan' => $p->teks,
                'status' => $sub ? 'Sudah Diisi' : 'Belum Diisi',
                'status_verifikasi' => $sub ? $sub->statusLabel() : '-',
                'isi' => $sub ? ($p->tipe === 'file' ? $sub->file_original_name : $sub->value) : '-',
                'terakhir_update' => $sub?->updated_at?->format('d-m-Y H:i') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Klaster', 'Indikator', 'Pertanyaan', 'Status', 'Status Verifikasi', 'Isi / Nama File', 'Terakhir Update'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '005093']]],
        ];
    }
}
