<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Pertanyaan;
use App\Models\Submission;
use App\Models\SubmissionHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DesaController extends Controller
{
    /**
     * Halaman "Monitoring Desa" — rekap keterisian data tiap desa di wilayah kecamatan ini.
     */
    public function monitoring(): View
    {
        $user = Auth::user();
        $this->pastikanAkunKecamatan($user);

        $totalPertanyaanDesa = Pertanyaan::where('untuk_desa', true)->count();

        $desas = Desa::where('kecamatan_id', $user->kecamatan_id)
            ->orderBy('nama')
            ->get()
            ->map(function ($desa) use ($user, $totalPertanyaanDesa) {
                $terisi = Submission::where('user_id', $user->id)
                    ->where('desa_id', $desa->id)
                    ->count();

                $desa->terisi_count = $terisi;
                $desa->total_pertanyaan = $totalPertanyaanDesa;
                $desa->progress = $totalPertanyaanDesa > 0 ? round(($terisi / $totalPertanyaanDesa) * 100) : 0;

                return $desa;
            });

        $rataProgress = $desas->count() > 0 ? round($desas->avg('progress')) : 0;
        $desaLengkap = $desas->where('progress', '>=', 100)->count();

        return view('user.desa.monitoring', compact('desas', 'totalPertanyaanDesa', 'rataProgress', 'desaLengkap'));
    }

    /**
     * Halaman pemilihan desa sebelum input data (dropdown filter).
     */
    public function pilihDesa(): View
    {
        $user = Auth::user();
        $this->pastikanAkunKecamatan($user);

        $desas = Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama')->get();

        return view('user.desa.pilih', compact('desas'));
    }

    /**
     * Form input data buat 1 desa tertentu.
     */
    public function show(Desa $desa): View
    {
        $user = Auth::user();
        $this->pastikanAkunKecamatan($user);
        $this->pastikanDesaMiliknya($user, $desa);

        $semuaDesa = Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama')->get();

        $pertanyaans = Pertanyaan::where('untuk_desa', true)
            ->with('indikator.klaster')
            ->get()
            ->sortBy([
                fn ($p) => $p->indikator->klaster->urutan,
                fn ($p) => $p->indikator->urutan,
                fn ($p) => $p->urutan,
            ])
            ->groupBy(fn ($p) => $p->indikator->klaster->id . '|' . $p->indikator->klaster->nama);

        $submissions = Submission::where('user_id', $user->id)
            ->where('desa_id', $desa->id)
            ->get()
            ->keyBy('pertanyaan_id');

        $total = Pertanyaan::where('untuk_desa', true)->count();
        $filled = $submissions->count();
        $progress = $total > 0 ? round(($filled / $total) * 100) : 0;

        return view('user.desa.show', compact('desa', 'semuaDesa', 'pertanyaans', 'submissions', 'filled', 'total', 'progress'));
    }

    public function store(Request $request, Desa $desa, Pertanyaan $pertanyaan): RedirectResponse
    {
        $user = Auth::user();
        $this->pastikanAkunKecamatan($user);
        $this->pastikanDesaMiliknya($user, $desa);

        if (! $pertanyaan->untuk_desa) {
            abort(403);
        }

        $rules = [];
        $maxKb = (int) env('UPLOAD_MAX_KB', 10240);
        $butuhLampiranTambahan = $pertanyaan->tipe !== 'file' && $pertanyaan->wajib_lampiran;

        if ($pertanyaan->tipe === 'file') {
            $rules['file'] = ['required', 'file', 'max:' . $maxKb, 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'];
        } elseif ($pertanyaan->tipe === 'number') {
            $rules['value'] = ['required', 'numeric'];
        } elseif ($pertanyaan->tipe === 'date') {
            $rules['value'] = ['required', 'date'];
        } else {
            $rules['value'] = ['required', 'string'];
        }

        if ($butuhLampiranTambahan) {
            $rules['file'] = ['required', 'file', 'max:' . $maxKb, 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'];
        }

        $validated = $request->validate($rules);

        $submission = Submission::firstOrNew([
            'user_id' => $user->id,
            'pertanyaan_id' => $pertanyaan->id,
            'desa_id' => $desa->id,
        ]);

        if ($submission->exists && ($submission->value !== null || $submission->file_path !== null)) {
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'user_id' => $submission->user_id,
                'value' => $submission->value,
                'file_path' => $submission->file_path,
                'file_original_name' => $submission->file_original_name,
                'status_saat_itu' => $submission->status,
                'diganti_at' => now(),
            ]);
        }

        if ($pertanyaan->tipe !== 'file') {
            $submission->value = $validated['value'] ?? null;
        }

        if (($pertanyaan->tipe === 'file' || $butuhLampiranTambahan) && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destinationDir = public_path('uploads/submissions');

            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $file->move($destinationDir, $filename);

            $submission->file_path = 'uploads/submissions/' . $filename;
            $submission->file_original_name = $file->getClientOriginalName();
        }

        $submission->status = 'menunggu';
        $submission->catatan_admin = null;
        $submission->verified_by = null;
        $submission->verified_at = null;

        $submission->user_id = $user->id;
        $submission->pertanyaan_id = $pertanyaan->id;
        $submission->desa_id = $desa->id;
        $submission->save();

        return back()->with('success', 'Data untuk ' . $desa->nama . ' berhasil disimpan dan menunggu verifikasi admin.');
    }

    private function pastikanAkunKecamatan($user): void
    {
        if (! $user->isKecamatan() || ! $user->kecamatan_id) {
            abort(403, 'Fitur ini cuma buat akun Kecamatan.');
        }
    }

    private function pastikanDesaMiliknya($user, Desa $desa): void
    {
        if ($desa->kecamatan_id !== $user->kecamatan_id) {
            abort(403, 'Desa ini bukan di wilayah kecamatan Anda.');
        }
    }
}
