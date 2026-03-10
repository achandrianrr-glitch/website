<?php

namespace App\Http\Controllers;

use App\Http\Requests\KembalikanSiswaRequest;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Services\PeminjamanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function __construct(
        protected PeminjamanService $peminjamanService
    ) {}

    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString();
        $tab = in_array($tab, ['aktif', 'selesai', 'semua'], true) ? $tab : 'aktif';

        $q = trim((string) $request->query('q', ''));
        $dari = (string) $request->query('dari', '');
        $sampai = (string) $request->query('sampai', '');

        $query = Peminjaman::query()
            ->with([
                'kelas:id,nama',
                'jurusan:id,nama',
            ])
            ->withCount('detailPeminjaman')
            ->latest('tanggal_pinjam')
            ->latest('id');

        if ($tab !== 'semua') {
            $query->where('status', $tab);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_peminjam', 'like', '%' . $q . '%')
                    ->orWhere('kode_pinjam', 'like', '%' . $q . '%');
            });
        }

        if ($dari !== '') {
            $query->whereDate('tanggal_pinjam', '>=', $dari);
        }

        if ($sampai !== '') {
            $query->whereDate('tanggal_pinjam', '<=', $sampai);
        }

        $peminjaman = $query
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'aktif' => Peminjaman::query()->where('status', 'aktif')->count(),
            'selesai' => Peminjaman::query()->where('status', 'selesai')->count(),
            'semua' => Peminjaman::query()->count(),
        ];

        return view('peminjaman.index', [
            'peminjaman' => $peminjaman,
            'tab' => $tab,
            'counts' => $counts,
            'filters' => [
                'q' => $q,
                'dari' => $dari,
                'sampai' => $sampai,
            ],
        ]);
    }

    public function show(Peminjaman $peminjaman): View
    {
        $peminjaman->load([
            'kelas:id,nama',
            'jurusan:id,nama',
            'pengguna:id,nama',
            'detailPeminjaman' => fn($query) => $query->orderBy('id'),
            'detailPeminjaman.barang:id,nama,tipe,kondisi_stok',
            'detailPeminjaman.unitBarang:id,nomor_unit,kondisi,status',
        ]);

        return view('peminjaman.show', [
            'peminjaman' => $peminjaman,
        ]);
    }

    public function kembalikan(KembalikanSiswaRequest $request, Peminjaman $peminjaman): RedirectResponse
    {
        $data = $request->validated();

        $detail = DetailPeminjaman::query()->findOrFail($data['detail_id']);

        if ((int) $detail->peminjaman_id !== (int) $peminjaman->id) {
            throw ValidationException::withMessages([
                'detail_id' => 'Item tidak sesuai dengan data peminjaman.',
            ]);
        }

        $this->peminjamanService->prosesPengembalianDetail(
            detail: $detail,
            kondisiKembali: (int) $data['kondisi_kembali'],
            catatanKembali: $data['catatan_kembali'] ?? null
        );

        return redirect()
            ->route('peminjaman.show', $peminjaman)
            ->with('sukses', 'Item peminjaman berhasil dikembalikan.');
    }
}
