<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indikator;
use App\Models\Klaster;
use App\Models\Opd;
use App\Models\Pertanyaan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PertanyaanController extends Controller
{
    public function index(Request $request): View
    {
        $klasterId = $request->get('klaster_id');
        $search = trim((string) $request->get('search'));

        $query = Pertanyaan::with('indikator.klaster', 'opds')->withCount('submissions');

        if ($klasterId) {
            $query->whereHas('indikator', fn ($q) => $q->where('klaster_id', $klasterId));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('teks', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhereHas('indikator', fn ($iq) => $iq->where('nama', 'like', "%{$search}%"));
            });
        }

        $pertanyaans = $query->orderBy('indikator_id')->orderBy('urutan')->paginate(25)->withQueryString();
        $klasters = Klaster::orderBy('urutan')->get();

        return view('admin.pertanyaan.index', compact('pertanyaans', 'klasters', 'klasterId', 'search'));
    }

    public function create(): View
    {
        $klasters = Klaster::with('indikators')->orderBy('urutan')->get();
        $opds = Opd::orderBy('nama')->get();
        return view('admin.pertanyaan.create', compact('klasters', 'opds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'indikator_id' => ['required', 'exists:indikators,id'],
            'kode' => ['nullable', 'string', 'max:100'],
            'teks' => ['required', 'string'],
            'tipe' => ['required', 'in:text,textarea,number,date,file'],
            'wajib' => ['nullable', 'boolean'],
            'untuk_kecamatan' => ['nullable', 'boolean'],
            'untuk_desa' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer'],
            'opds' => ['nullable', 'array'],
            'opds.*' => ['exists:opds,id'],
        ]);

        $data['wajib'] = $request->boolean('wajib');
        $data['untuk_kecamatan'] = $request->boolean('untuk_kecamatan');
        $data['untuk_desa'] = $request->boolean('untuk_desa');
        $data['urutan'] = $data['urutan'] ?? 0;

        $pertanyaan = Pertanyaan::create($data);

        if (! empty($data['opds'])) {
            $pertanyaan->opds()->sync($data['opds']);
        }

        return redirect()->route('admin.pertanyaan.index')->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function edit(Pertanyaan $pertanyaan): View
    {
        $klasters = Klaster::with('indikators')->orderBy('urutan')->get();
        $opds = Opd::orderBy('nama')->get();
        $selectedOpds = $pertanyaan->opds->pluck('id')->toArray();

        return view('admin.pertanyaan.edit', compact('pertanyaan', 'klasters', 'opds', 'selectedOpds'));
    }

    public function update(Request $request, Pertanyaan $pertanyaan): RedirectResponse
    {
        $data = $request->validate([
            'indikator_id' => ['required', 'exists:indikators,id'],
            'kode' => ['nullable', 'string', 'max:100'],
            'teks' => ['required', 'string'],
            'tipe' => ['required', 'in:text,textarea,number,date,file'],
            'wajib' => ['nullable', 'boolean'],
            'untuk_kecamatan' => ['nullable', 'boolean'],
            'untuk_desa' => ['nullable', 'boolean'],
            'urutan' => ['nullable', 'integer'],
            'opds' => ['nullable', 'array'],
            'opds.*' => ['exists:opds,id'],
        ]);

        $data['wajib'] = $request->boolean('wajib');
        $data['untuk_kecamatan'] = $request->boolean('untuk_kecamatan');
        $data['untuk_desa'] = $request->boolean('untuk_desa');

        $pertanyaan->update($data);
        $pertanyaan->opds()->sync($data['opds'] ?? []);

        return redirect()->route('admin.pertanyaan.index')->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroy(Pertanyaan $pertanyaan): RedirectResponse
    {
        $pertanyaan->delete();
        return redirect()->route('admin.pertanyaan.index')->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
