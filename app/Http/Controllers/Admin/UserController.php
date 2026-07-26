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
    public function index(): View
    {
        $users = User::with('opd')->orderBy('role')->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
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
            'email' => ['required', 'email', 'unique:users,email' . ($user ? ',' . $user->id : '')],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
            'kategori' => ['nullable', 'required_if:role,user', 'in:opd,kecamatan,desa'],
            'opd_id' => ['nullable', 'required_if:kategori,opd', 'exists:opds,id'],
            'organisasi' => ['nullable', 'string', 'max:255'],
        ];

        $data = $request->validate($rules);

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

        return $data;
    }
}
