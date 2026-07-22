<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DataRequirement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DataRequirementController extends Controller
{
    public function index(): View
    {
        $requirements = DataRequirement::withCount('submissions')->orderBy('urutan')->orderBy('id')->get();
        return view('admin.requirements.index', compact('requirements'));
    }

    public function create(): View
    {
        return view('admin.requirements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tipe' => ['required', 'in:text,textarea,number,date,file'],
            'wajib' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer'],
        ]);

        $data['wajib'] = $request->boolean('wajib');
        $data['urutan'] = $data['urutan'] ?? (DataRequirement::max('urutan') + 1);

        DataRequirement::create($data);

        return redirect()->route('admin.requirements.index')->with('success', 'Jenis data berhasil ditambahkan.');
    }

    public function edit(DataRequirement $requirement): View
    {
        return view('admin.requirements.edit', compact('requirement'));
    }

    public function update(Request $request, DataRequirement $requirement): RedirectResponse
    {
        $data = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tipe' => ['required', 'in:text,textarea,number,date,file'],
            'wajib' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer'],
        ]);

        $data['wajib'] = $request->boolean('wajib');

        $requirement->update($data);

        return redirect()->route('admin.requirements.index')->with('success', 'Jenis data berhasil diperbarui.');
    }

    public function destroy(DataRequirement $requirement): RedirectResponse
    {
        $requirement->delete();

        return redirect()->route('admin.requirements.index')->with('success', 'Jenis data berhasil dihapus.');
    }
}
