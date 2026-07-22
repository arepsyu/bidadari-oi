<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DataRequirement;
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
        $requirements = DataRequirement::orderBy('urutan')->orderBy('id')->get();
        $submissions = Submission::where('user_id', Auth::id())
            ->get()
            ->keyBy('data_requirement_id');

        $filled = $submissions->count();
        $total = $requirements->count();
        $progress = $total > 0 ? round(($filled / $total) * 100) : 0;

        return view('user.dashboard', compact('requirements', 'submissions', 'filled', 'total', 'progress'));
    }

    public function store(Request $request, DataRequirement $requirement): RedirectResponse
    {
        $rules = [];

        if ($requirement->tipe === 'file') {
            $rules['file'] = ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx'];
        } elseif ($requirement->tipe === 'number') {
            $rules['value'] = ['required', 'numeric'];
        } elseif ($requirement->tipe === 'date') {
            $rules['value'] = ['required', 'date'];
        } else {
            $rules['value'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $submission = Submission::firstOrNew([
            'user_id' => Auth::id(),
            'data_requirement_id' => $requirement->id,
        ]);

        if ($requirement->tipe === 'file' && $request->hasFile('file')) {
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
        $submission->data_requirement_id = $requirement->id;
        $submission->save();

        return back()->with('success', 'Data "' . $requirement->judul . '" berhasil disimpan.');
    }
}
