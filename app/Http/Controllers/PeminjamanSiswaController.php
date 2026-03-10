<?php

namespace App\Http\Controllers;

use App\Http\Requests\KembalikanSiswaRequest;
use App\Http\Requests\PeminjamanSiswaRequest;
use App\Models\Barang;
use App\Models\DetailPeminjaman;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Peminjaman;
use App\Services\PeminjamanService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanSiswaController extends Controller
{
    public function __construct(
        protected PeminjamanService $peminjamanService
    ) {}

    public function index(): View
    {
        return view('siswa.pinjam', [
            'kelas' => Kelas::query()
                ->orderBy('id')
                ->get(['id', 'nama']),

            'jurusan' => Jurusan::query()
                ->orderBy('nama')
                ->get(['id', 'nama']),
        ]);
    }

    public function cariBarang(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $items = Barang::query()
            ->with([
                'kategori:id,nama',
                'merek:id,nama',
            ])
            ->withCount([
                'unitBarang as unit_tersedia_count' => fn(Builder $sub) => $sub->where('status', 'tersedia'),
            ])
            ->withMax([
                'unitBarang as kondisi_terbaik' => fn(Builder $sub) => $sub->where('status', 'tersedia'),
            ], 'kondisi')
            ->where('aktif', true)
            ->where(function (Builder $query) {
                $query->where(function (Builder $aset) {
                    $aset->where('tipe', 'aset')
                        ->whereHas('unitBarang', fn(Builder $sub) => $sub->where('status', 'tersedia'));
                })->orWhere(function (Builder $stok) {
                    $stok->where('tipe', 'stok')
                        ->where('qty_tersedia', '>', 0);
                });
            })
            ->where(function (Builder $query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhereHas('kategori', fn(Builder $sub) => $sub->where('nama', 'like', '%' . $q . '%'))
                    ->orWhereHas('merek', fn(Builder $sub) => $sub->where('nama', 'like', '%' . $q . '%'))
                    ->orWhere('merek_manual', 'like', '%' . $q . '%');
            })
            ->orderBy('nama')
            ->limit(10)
            ->get()
            ->map(function (Barang $barang) {
                $kondisi = $barang->tipe === 'aset'
                    ? (int) ($barang->kondisi_terbaik ?? 100)
                    : (int) ($barang->kondisi_stok ?? 100);

                return [
                    'id' => $barang->id,
                    'nama' => $barang->nama,
                    'tipe' => $barang->tipe,
                    'kategori' => $barang->kategori?->nama,
                    'merek' => $barang->label_merek,
                    'kondisi' => $kondisi,
                    'label_kondisi' => $this->labelKondisi($kondisi),
                    'unit_tersedia' => $barang->tipe === 'aset' ? (int) $barang->unit_tersedia_count : null,
                    'qty_tersedia' => $barang->tipe === 'stok' ? (int) $barang->qty_tersedia : null,
                ];
            })
            ->values();

        return response()->json($items);
    }

    public function ajukan(PeminjamanSiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $hasil = $this->peminjamanService->buatPeminjamanPublik(
            data: $data,
            items: $data['items'] ?? []
        );

        return redirect()
            ->route('siswa.pinjam')
            ->with('tab_aktif', 'peminjaman')
            ->with('sukses', 'Peminjaman berhasil diajukan.')
            ->with('peminjaman_sukses', [
                'kode_pinjam' => $hasil['kode_pinjam'],
                'items' => $hasil['items'],
            ]);
    }

    public function cekKode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_pinjam' => ['required', 'string', 'max:20'],
        ], [
            'kode_pinjam.required' => 'Kode peminjaman wajib diisi.',
        ]);

        $kode = mb_strtoupper(trim($validated['kode_pinjam']));

        $peminjaman = $this->loadPeminjamanByKode($kode);

        if (!$peminjaman || $peminjaman->status === 'selesai') {
            return redirect()
                ->route('siswa.pinjam')
                ->with('tab_aktif', 'pengembalian')
                ->with('galat_kode', 'Kode tidak ditemukan atau peminjaman sudah selesai.');
        }

        return redirect()
            ->route('siswa.pinjam')
            ->with('tab_aktif', 'pengembalian')
            ->with('hasil_kode', $this->formatHasilKode($peminjaman));
    }

    public function kembalikan(KembalikanSiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $detail = DetailPeminjaman::query()
            ->findOrFail($data['detail_id']);

        $this->peminjamanService->prosesPengembalianDetail(
            detail: $detail,
            kondisiKembali: (int) $data['kondisi_kembali'],
            catatanKembali: $data['catatan_kembali'] ?? null
        );

        $detailFresh = DetailPeminjaman::query()->findOrFail($data['detail_id']);

        $peminjaman = Peminjaman::query()
            ->with([
                'kelas:id,nama',
                'jurusan:id,nama',
                'detailPeminjaman' => fn($query) => $query->orderBy('id'),
                'detailPeminjaman.barang:id,nama,tipe',
                'detailPeminjaman.unitBarang:id,nomor_unit',
            ])
            ->findOrFail($detailFresh->peminjaman_id);

        return redirect()
            ->route('siswa.pinjam')
            ->with('tab_aktif', 'pengembalian')
            ->with('sukses', 'Item berhasil dikembalikan.')
            ->with('hasil_kode', $this->formatHasilKode($peminjaman));
    }

    protected function loadPeminjamanByKode(string $kode): ?Peminjaman
    {
        return Peminjaman::query()
            ->with([
                'kelas:id,nama',
                'jurusan:id,nama',
                'detailPeminjaman' => fn($query) => $query->orderBy('id'),
                'detailPeminjaman.barang:id,nama,tipe',
                'detailPeminjaman.unitBarang:id,nomor_unit',
            ])
            ->where('kode_pinjam', $kode)
            ->first();
    }

    protected function formatHasilKode(Peminjaman $peminjaman): array
    {
        return [
            'id' => $peminjaman->id,
            'kode_pinjam' => $peminjaman->kode_pinjam,
            'nama_peminjam' => $peminjaman->nama_peminjam,
            'kelas' => $peminjaman->kelas?->nama,
            'jurusan' => $peminjaman->jurusan?->nama,
            'tanggal_pinjam' => optional($peminjaman->tanggal_pinjam)->format('d M Y'),
            'status' => $peminjaman->status,
            'items' => $peminjaman->detailPeminjaman
                ->map(function (DetailPeminjaman $detail) {
                    return [
                        'detail_id' => $detail->id,
                        'barang' => $detail->barang?->nama ?? '-',
                        'tipe' => $detail->barang?->tipe ?? 'aset',
                        'unit_qty' => $detail->unitBarang?->nomor_unit ?? ('Qty ' . $detail->jumlah),
                        'status_item' => $detail->status_item,
                        'kondisi_awal' => $detail->kondisi_awal,
                        'kondisi_kembali' => $detail->kondisi_kembali,
                        'waktu_kembali' => optional($detail->waktu_kembali)->format('d M Y H:i'),
                        'catatan_kembali' => $detail->catatan_kembali,
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    protected function labelKondisi(int $kondisi): string
    {
        return match (true) {
            $kondisi >= 80 => 'Baik',
            $kondisi >= 60 => 'Lumayan',
            $kondisi >= 35 => 'Rusak',
            default => 'Rusak Parah',
        };
    }
}
