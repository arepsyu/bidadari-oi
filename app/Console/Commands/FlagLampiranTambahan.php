<?php

namespace App\Console\Commands;

use App\Models\Pertanyaan;
use Illuminate\Console\Command;

class FlagLampiranTambahan extends Command
{
    protected $signature = 'pertanyaan:flag-lampiran';
    protected $description = 'Tandai otomatis pertanyaan yang butuh isian + lampiran dokumen sekaligus';

    public function handle(): int
    {
        $target = [
            ['klaster' => 'KLASTER I', 'indikator_kode' => 'Indikator 4', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER I', 'indikator_kode' => 'Indikator 4', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER I', 'indikator_kode' => 'Indikator 4', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER I', 'indikator_kode' => 'Indikator 5', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER II', 'indikator_kode' => 'Indikator 8', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER II', 'indikator_kode' => 'Indikator 8', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER II', 'indikator_kode' => 'Indikator 10', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER II', 'indikator_kode' => 'Indikator 10', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 12', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 12', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 12', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 12', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 12', 'pertanyaan_kode' => 'Pertanyaan 5'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 13', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 13', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 13', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 5'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 6'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 7'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 14', 'pertanyaan_kode' => 'Pertanyaan 8'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 15', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 15', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 15', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 16', 'pertanyaan_kode' => 'Pertanyaan 5'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 17', 'pertanyaan_kode' => 'Pertanyaan 3a'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 17', 'pertanyaan_kode' => 'Pertanyaan 3b'],
            ['klaster' => 'KLASTER III', 'indikator_kode' => 'Indikator 17', 'pertanyaan_kode' => 'Pertanyaan 3c'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 2a'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 2b'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 2c'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 2d'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 2e'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 19', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 20', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 20', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER IV', 'indikator_kode' => 'Indikator 20', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KLASTER V', 'indikator_kode' => 'Indikator 24 A', 'pertanyaan_kode' => 'Pertanyaan 1'],
            ['klaster' => 'KLASTER V', 'indikator_kode' => 'Indikator 24 A', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KLASTER V', 'indikator_kode' => 'Indikator 24 A', 'pertanyaan_kode' => 'Pertanyaan 3'],
            ['klaster' => 'KLASTER V', 'indikator_kode' => 'Indikator 24 A', 'pertanyaan_kode' => 'Pertanyaan 5 '],
            ['klaster' => 'KLASTER V', 'indikator_kode' => 'Indikator 24 A', 'pertanyaan_kode' => 'Pertanyaan 6'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 5'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 10'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 11'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 25', 'pertanyaan_kode' => 'Pertanyaan 12'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 2'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 4'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 6'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 10'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 11'],
            ['klaster' => 'KELANA DEKELA', 'indikator_kode' => 'Indikator 26', 'pertanyaan_kode' => 'Pertanyaan 15'],
        ];

        $updated = 0;
        $notFound = [];

        foreach ($target as $t) {
            $pertanyaan = Pertanyaan::whereHas('indikator', function ($q) use ($t) {
                $q->where('kode', $t['indikator_kode'])
                    ->whereHas('klaster', fn ($kq) => $kq->where('nama', $t['klaster']));
            })->where('kode', $t['pertanyaan_kode'])->first();

            if ($pertanyaan) {
                $pertanyaan->update(['wajib_lampiran' => true]);
                $updated++;
            } else {
                $notFound[] = $t['klaster'] . ' / ' . $t['indikator_kode'] . ' / ' . $t['pertanyaan_kode'];
            }
        }

        $this->info("Berhasil ditandai: {$updated} pertanyaan.");

        if (! empty($notFound)) {
            $this->warn('Gak ketemu (' . count($notFound) . '):');
            foreach ($notFound as $nf) {
                $this->line('- ' . $nf);
            }
        }

        return self::SUCCESS;
    }
}