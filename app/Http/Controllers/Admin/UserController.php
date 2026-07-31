<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $kategori = $request->get('kategori');

        $query = User::with('opd')->withCount('submissions')->orderBy('role')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('organisasi', 'like', "%{$search}%");
            });
        }

        if ($kategori === 'admin') {
            $query->where('role', 'admin');
        } elseif (in_array($kategori, ['opd', 'kecamatan', 'desa'])) {
            $query->where('kategori', $kategori);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'search', 'kategori'));
    }

    public function create(): View
    {
        $opds = Opd::orderBy('nama')->get();
        return view('admin.users.create', compact('opds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = true;

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $opds = Opd::orderBy('nama')->get();
        return view('admin.users.edit', compact('user', 'opds'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus admin terakhir.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/',
                'unique:users,username' . ($user ? ',' . $user->id : ''),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
            'kategori' => ['nullable', 'required_if:role,user', 'in:opd,kecamatan,desa'],
            'opd_id' => ['nullable', 'required_if:kategori,opd', 'exists:opds,id'],
            'organisasi' => ['nullable', 'string', 'max:255'],
        ];

        $messages = [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.regex' => 'Username cuma boleh diisi huruf, angka, titik (.), strip (-), atau underscore (_), tanpa spasi.',
            'username.unique' => 'Username ini udah dipakai akun lain, coba pakai yang lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'kategori.required_if' => 'Kategori Akun wajib dipilih buat role User.',
            'opd_id.required_if' => 'OPD/Dinas wajib dipilih buat kategori OPD.',
            'opd_id.exists' => 'OPD/Dinas yang dipilih gak valid.',
        ];

        $data = $request->validate($rules, $messages);

        // Admin gak butuh kategori/opd
        if ($data['role'] === 'admin') {
            $data['kategori'] = null;
            $data['opd_id'] = null;
        }

        // Kalau kategori OPD, sinkronkan nama organisasi dengan master OPD yang dipilih
        if (($data['kategori'] ?? null) === 'opd' && ! empty($data['opd_id'])) {
            $data['organisasi'] = Opd::find($data['opd_id'])->nama;
        } else {
            $data['opd_id'] = null;
        }

        // Kolom email masih ada di database (bawaan Laravel), tapi gak dipakai buat login lagi.
        // Diisi otomatis dari username biar gak perlu ditanyain ke admin.
        $data['email'] = $data['username'] . '@bidadarioi.local';

        return $data;
    }
}
