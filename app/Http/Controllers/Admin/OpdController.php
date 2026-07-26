<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpdController extends Controller
{
    public function index(): View
    {
        $opds = Opd::withCount(['users', 'pertanyaans'])->orderBy('nama')->get();
        return view('admin.opd.index', compact('opds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:opds,nama'],
        ]);

        Opd::create($data);

        return back()->with('success', 'OPD berhasil ditambahkan.');
    }

    public function update(Request $request, Opd $opd): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:opds,nama,' . $opd->id],
        ]);

        $opd->update($data);

        return back()->with('success', 'OPD berhasil diperbarui.');
    }

    public function destroy(Opd $opd): RedirectResponse
    {
        if ($opd->users()->exists() || $opd->pertanyaans()->exists()) {
            return back()->with('error', 'OPD ini masih dipakai oleh akun/pertanyaan, tidak bisa dihapus.');
        }

        $opd->delete();

        return back()->with('success', 'OPD berhasil dihapus.');
    }
}
