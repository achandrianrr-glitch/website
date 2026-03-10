<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenggunaRequest;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PenggunaController extends Controller
{
    public function index(): View
    {
        $pengguna = Pengguna::query()
            ->withCount([
                'transaksi',
                'peminjaman',
            ])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pengguna.index', [
            'pengguna' => $pengguna,
        ]);
    }

    public function store(PenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Pengguna::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('pengguna.index')
            ->with('sukses', 'Pengguna berhasil ditambahkan.');
    }

    public function update(PenggunaRequest $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $request->validated();

        $pengguna->update([
            'nama' => $data['nama'],
            'email' => $data['email'],
        ]);

        return redirect()
            ->route('pengguna.index')
            ->with('sukses', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna): RedirectResponse
    {
        $currentUserId = Auth::id();

        if ($currentUserId !== null && $currentUserId === $pengguna->id) {
            return redirect()
                ->route('pengguna.index')
                ->with('galat', 'Akun yang sedang digunakan tidak bisa dihapus.');
        }

        if ($pengguna->transaksi()->exists()) {
            return redirect()
                ->route('pengguna.index')
                ->with('galat', 'Pengguna tidak bisa dihapus karena sudah memiliki riwayat transaksi.');
        }

        $pengguna->delete();

        return redirect()
            ->route('pengguna.index')
            ->with('sukses', 'Pengguna berhasil dihapus.');
    }

    public function resetPassword(PenggunaRequest $request, Pengguna $pengguna): RedirectResponse
    {
        $data = $request->validated();

        $pengguna->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('pengguna.index')
            ->with('sukses', 'Password pengguna berhasil direset.');
    }
}
