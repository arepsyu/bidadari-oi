<?php

namespace App\Exports;

use App\Models\DataRequirement;
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
        $requirements = DataRequirement::orderBy('urutan')->get();
        $submissions = Submission::where('user_id', $this->user->id)->get()->keyBy('data_requirement_id');

        return $requirements->map(function ($req) use ($submissions) {
            $sub = $submissions->get($req->id);
            return [
                'judul' => $req->judul,
                'status' => $sub ? 'Sudah Diisi' : 'Belum Diisi',
                'isi' => $sub ? ($req->tipe === 'file' ? $sub->file_original_name : $sub->value) : '-',
                'terakhir_update' => $sub?->updated_at?->format('d-m-Y H:i') ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['Jenis Data', 'Status', 'Isi / Nama File', 'Terakhir Update'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '005093']]],
        ];
    }
}
