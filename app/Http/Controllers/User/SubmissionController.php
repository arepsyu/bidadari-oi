<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Models\Submission;
use App\Models\SubmissionHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $pertanyaans = $user->pertanyaanRelevan()
            ->with('indikator.klaster')
            ->get()
            ->sortBy([
                fn ($p) => $p->indikator->klaster->urutan,
                fn ($p) => $p->indikator->urutan,
                fn ($p) => $p->urutan,
            ])
            ->groupBy(fn ($p) => $p->indikator->klaster->id . '|' . $p->indikator->klaster->nama);

        $submissions = Submission::where('user_id', $user->id)->get()->keyBy('pertanyaan_id');

        $total = $user->pertanyaanRelevan()->count();
        $filled = $submissions->count();
        $progress = $total > 0 ? round(($filled / $total) * 100) : 0;

        return view('user.dashboard', compact('pertanyaans', 'submissions', 'filled', 'total', 'progress'));
    }

    public function store(Request $request, Pertanyaan $pertanyaan): RedirectResponse
    {
        // Pastikan pertanyaan ini memang relevan buat user yang login (jaga-jaga akses langsung)
        if (! $pertanyaan->isVisibleFor(Auth::user())) {
            abort(403);
        }

        $rules = [];
        $maxKb = (int) env('UPLOAD_MAX_KB', 10240);

        if ($pertanyaan->tipe === 'file') {
            $rules['file'] = ['required', 'file', 'max:' . $maxKb, 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'];
        } elseif ($pertanyaan->tipe === 'number') {
            $rules['value'] = ['required', 'numeric'];
        } elseif ($pertanyaan->tipe === 'date') {
            $rules['value'] = ['required', 'date'];
        } else {
            $rules['value'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $submission = Submission::firstOrNew([
            'user_id' => Auth::id(),
            'pertanyaan_id' => $pertanyaan->id,
        ]);

        // Kalau ini update dari data yang udah ada sebelumnya, simpen dulu versi lama ke riwayat
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

        if ($pertanyaan->tipe === 'file' && $request->hasFile('file')) {
            // Simpan langsung ke public/uploads/submissions (TANPA symlink storage:link),
            // supaya kompatibel di hosting yang gak dukung symlink (misal InfinityFree).
            // Catatan: file versi lama SENGAJA gak dihapus dari disk, biar link riwayat lama masih bisa dibuka.
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $destinationDir = public_path('uploads/submissions');

            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $file->move($destinationDir, $filename);

            $submission->file_path = 'uploads/submissions/' . $filename;
            $submission->file_original_name = $file->getClientOriginalName();
        } else {
            $submission->value = $validated['value'] ?? null;
        }

        // Data berubah = perlu diverifikasi ulang oleh admin
        $submission->status = 'menunggu';
        $submission->catatan_admin = null;
        $submission->verified_by = null;
        $submission->verified_at = null;

        $submission->user_id = Auth::id();
        $submission->pertanyaan_id = $pertanyaan->id;
        $submission->save();

        return back()->with('success', 'Data berhasil disimpan dan menunggu verifikasi admin.');
    }
}
