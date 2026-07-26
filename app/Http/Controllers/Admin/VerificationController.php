<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function verify(Request $request, Submission $submission): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:disetujui,ditolak'],
            'catatan_admin' => ['nullable', 'string', 'max:1000', 'required_if:status,ditolak'],
        ]);

        $submission->update([
            'status' => $data['status'],
            'catatan_admin' => $data['catatan_admin'] ?? null,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Status verifikasi berhasil diperbarui.');
    }

    public function history(Submission $submission): View
    {
        $submission->load('histories.user', 'pertanyaan.indikator.klaster', 'user');

        return view('admin.monitoring.history', compact('submission'));
    }
}
