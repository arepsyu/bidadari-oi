<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pertanyaan;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        if ($pertanyaan->tipe === 'file') {
            $rules['file'] = ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'];
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

        if ($pertanyaan->tipe === 'file' && $request->hasFile('file')) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $path = $request->file('file')->store('submissions', 'public');
            $submission->file_path = $path;
            $submission->file_original_name = $request->file('file')->getClientOriginalName();
        } else {
            $submission->value = $validated['value'] ?? null;
        }

        $submission->user_id = Auth::id();
        $submission->pertanyaan_id = $pertanyaan->id;
        $submission->save();

        return back()->with('success', 'Data berhasil disimpan.');
    }
}
