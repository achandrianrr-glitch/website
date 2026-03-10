<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Transaksi;
use App\Models\UnitBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        Carbon::setLocale('id');

        $kpi = [
            'total_barang' => Barang::where('aktif', true)->count(),

            'barang_tersedia' => UnitBarang::where('status', 'tersedia')->count()
                + Barang::where('tipe', 'stok')->sum('qty_tersedia'),

            'barang_dipinjam' => UnitBarang::where('status', 'dipinjam')->count()
                + Barang::where('tipe', 'stok')->sum('qty_dipinjam'),

            'barang_rusak' => UnitBarang::where('status', 'rusak')->count()
                + Barang::where('tipe', 'stok')->sum('qty_rusak'),

            'total_kategori' => Kategori::count(),
        ];

        $kondisiChart = [
            'baik' => UnitBarang::where('kondisi', '>=', 80)->count(),
            'lumayan' => UnitBarang::whereBetween('kondisi', [60, 79])->count(),
            'rusak' => UnitBarang::whereBetween('kondisi', [35, 59])->count(),
            'rusak_parah' => UnitBarang::where('kondisi', '<=', 34)->count(),
        ];

        $bulanAwal = now()->copy()->startOfMonth()->subMonths(5);
        $rentangBulan = collect(range(0, 5))->map(function ($index) use ($bulanAwal) {
            return $bulanAwal->copy()->addMonths($index);
        });

        $peminjamanBulananRaw = Peminjaman::query()
            ->selectRaw('YEAR(tanggal_pinjam) as tahun, MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereDate('tanggal_pinjam', '>=', $bulanAwal->toDateString())
            ->groupByRaw('YEAR(tanggal_pinjam), MONTH(tanggal_pinjam)')
            ->orderByRaw('YEAR(tanggal_pinjam), MONTH(tanggal_pinjam)')
            ->get()
            ->keyBy(function ($item) {
                return sprintf('%04d-%02d', $item->tahun, $item->bulan);
            });

        $lineChart = [
            'labels' => $rentangBulan
                ->map(fn($bulan) => $bulan->translatedFormat('M Y'))
                ->values()
                ->all(),

            'data' => $rentangBulan
                ->map(function ($bulan) use ($peminjamanBulananRaw) {
                    $key = $bulan->format('Y-m');

                    return (int) ($peminjamanBulananRaw[$key]->total ?? 0);
                })
                ->values()
                ->all(),
        ];

        $kategoriChartRaw = Kategori::query()
            ->withCount([
                'barang as barang_count' => function ($query) {
                    $query->where('aktif', true);
                },
            ])
            ->orderByDesc('barang_count')
            ->orderBy('nama')
            ->get();

        $barChart = [
            'labels' => $kategoriChartRaw->pluck('nama')->values()->all(),
            'data' => $kategoriChartRaw->pluck('barang_count')->map(fn($jumlah) => (int) $jumlah)->values()->all(),
        ];

        $aktivitasTerbaru = $this->susunAktivitasTerbaru();

        return view('dashboard.index', [
            'kpi' => $kpi,
            'kondisiChart' => $kondisiChart,
            'lineChart' => $lineChart,
            'barChart' => $barChart,
            'aktivitasTerbaru' => $aktivitasTerbaru,
        ]);
    }

    protected function susunAktivitasTerbaru()
    {
        $transaksi = Transaksi::query()
            ->with([
                'barang:id,nama',
                'pengguna:id,nama',
            ])
            ->latest('created_at')
            ->get()
            ->map(function ($item) {
                $waktu = $item->created_at ?? Carbon::parse($item->tanggal_transaksi);

                return [
                    'waktu' => $waktu,
                    'tanggal_label' => $waktu->format('d M Y H:i'),
                    'jenis' => $item->jenis,
                    'barang' => $item->barang?->nama ?? '-',
                    'keterangan' => match ($item->jenis) {
                        'masuk' => 'Barang masuk ' . $item->jumlah . ' item oleh ' . ($item->pengguna?->nama ?? 'Admin'),
                        'keluar' => 'Barang keluar ' . $item->jumlah . ' item'
                            . ($item->alasan_keluar ? ' · alasan: ' . str_replace('_', ' ', $item->alasan_keluar) : ''),
                        default => 'Transaksi barang',
                    },
                ];
            });

        $peminjaman = Peminjaman::query()
            ->with([
                'detailPeminjaman.barang:id,nama',
            ])
            ->latest('created_at')
            ->get()
            ->map(function ($item) {
                $waktu = $item->created_at ?? Carbon::parse($item->tanggal_pinjam . ' ' . $item->waktu_pinjam);

                return [
                    'waktu' => $waktu,
                    'tanggal_label' => $waktu->format('d M Y H:i'),
                    'jenis' => 'dipinjam',
                    'barang' => $this->ringkasBarangPeminjaman($item),
                    'keterangan' => 'Peminjaman oleh ' . $item->nama_peminjam
                        . ' · ' . $item->detailPeminjaman->count() . ' item',
                ];
            });

        $pengembalian = DetailPeminjaman::query()
            ->with([
                'barang:id,nama',
                'peminjaman:id,nama_peminjam',
            ])
            ->where('status_item', 'dikembalikan')
            ->whereNotNull('waktu_kembali')
            ->latest('waktu_kembali')
            ->get()
            ->map(function ($item) {
                $waktu = $item->waktu_kembali instanceof Carbon
                    ? $item->waktu_kembali
                    : Carbon::parse($item->waktu_kembali);

                return [
                    'waktu' => $waktu,
                    'tanggal_label' => $waktu->format('d M Y H:i'),
                    'jenis' => 'kembali',
                    'barang' => $item->barang?->nama ?? '-',
                    'keterangan' => 'Dikembalikan oleh ' . ($item->peminjaman?->nama_peminjam ?? '-')
                        . ($item->kondisi_kembali !== null ? ' · kondisi ' . $item->kondisi_kembali . '%' : ''),
                ];
            });

        return $transaksi
            ->merge($peminjaman)
            ->merge($pengembalian)
            ->sortByDesc('waktu')
            ->take(15)
            ->values();
    }

    protected function ringkasBarangPeminjaman(Peminjaman $peminjaman): string
    {
        $namaBarang = $peminjaman->detailPeminjaman
            ->map(fn($detail) => $detail->barang?->nama)
            ->filter()
            ->unique()
            ->values();

        if ($namaBarang->isEmpty()) {
            return '-';
        }

        if ($namaBarang->count() === 1) {
            return $namaBarang->first();
        }

        if ($namaBarang->count() === 2) {
            return $namaBarang->implode(', ');
        }

        return $namaBarang->take(2)->implode(', ') . ' +' . ($namaBarang->count() - 2) . ' lagi';
    }
}
