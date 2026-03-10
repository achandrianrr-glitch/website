<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->resolveFilters($request);

        $inventaris = $this->getInventarisData();
        $transaksi = $this->getTransaksiData($filters['dari'], $filters['sampai']);
        $peminjaman = $this->getPeminjamanData($filters['dari'], $filters['sampai']);

        return view('laporan.index', [
            'filters' => $filters,
            'inventaris' => $inventaris,
            'inventarisSummary' => $this->getInventarisSummary($inventaris),
            'transaksi' => $transaksi,
            'transaksiSummary' => $this->getTransaksiSummary($transaksi),
            'peminjaman' => $peminjaman,
            'peminjamanSummary' => $this->getPeminjamanSummary($peminjaman),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->resolveFilters($request);

        $inventaris = $this->getInventarisData();
        $transaksi = $this->getTransaksiData($filters['dari'], $filters['sampai']);
        $peminjaman = $this->getPeminjamanData($filters['dari'], $filters['sampai']);

        $logoBase64 = null;
        $logoPath = public_path('logo-sekolah.png');

        if (file_exists($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('laporan.pdf', [
            'filters' => $filters,
            'inventaris' => $inventaris,
            'inventarisSummary' => $this->getInventarisSummary($inventaris),
            'transaksi' => $transaksi,
            'transaksiSummary' => $this->getTransaksiSummary($transaksi),
            'peminjaman' => $peminjaman,
            'peminjamanSummary' => $this->getPeminjamanSummary($peminjaman),
            'logoBase64' => $logoBase64,
            'tanggalCetak' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-shiro-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }

    protected function resolveFilters(Request $request): array
    {
        return [
            'dari' => (string) $request->query('dari', now()->startOfMonth()->format('Y-m-d')),
            'sampai' => (string) $request->query('sampai', now()->format('Y-m-d')),
        ];
    }

    protected function getInventarisData()
    {
        return Barang::query()
            ->with([
                'kategori:id,nama',
                'merek:id,nama',
                'lokasi:id,nama',
            ])
            ->withCount([
                'unitBarang',
                'unitBarang as unit_tersedia_count' => fn($q) => $q->where('status', 'tersedia'),
                'unitBarang as unit_dipinjam_count' => fn($q) => $q->where('status', 'dipinjam'),
                'unitBarang as unit_rusak_count' => fn($q) => $q->where('status', 'rusak'),
                'unitBarang as unit_keluar_count' => fn($q) => $q->where('status', 'keluar'),
            ])
            ->withAvg('unitBarang as rata_kondisi_unit', 'kondisi')
            ->orderBy('nama')
            ->get();
    }

    protected function getTransaksiData(string $dari, string $sampai)
    {
        return Transaksi::query()
            ->with([
                'barang:id,nama,tipe',
                'pengguna:id,nama',
                'unitBarang:id,nomor_unit',
                'lokasiTujuan:id,nama',
            ])
            ->whereDate('tanggal_transaksi', '>=', $dari)
            ->whereDate('tanggal_transaksi', '<=', $sampai)
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->get();
    }

    protected function getPeminjamanData(string $dari, string $sampai)
    {
        return Peminjaman::query()
            ->with([
                'kelas:id,nama',
                'jurusan:id,nama',
            ])
            ->withCount('detailPeminjaman')
            ->whereDate('tanggal_pinjam', '>=', $dari)
            ->whereDate('tanggal_pinjam', '<=', $sampai)
            ->latest('tanggal_pinjam')
            ->latest('id')
            ->get();
    }

    protected function getInventarisSummary($inventaris): array
    {
        return [
            'total' => $inventaris->count(),
            'aset' => $inventaris->where('tipe', 'aset')->count(),
            'stok' => $inventaris->where('tipe', 'stok')->count(),
            'aktif' => $inventaris->where('aktif', true)->count(),
        ];
    }

    protected function getTransaksiSummary($transaksi): array
    {
        return [
            'masuk' => $transaksi->where('jenis', 'masuk')->count(),
            'keluar' => $transaksi->where('jenis', 'keluar')->count(),
        ];
    }

    protected function getPeminjamanSummary($peminjaman): array
    {
        return [
            'aktif' => $peminjaman->where('status', 'aktif')->count(),
            'selesai' => $peminjaman->where('status', 'selesai')->count(),
        ];
    }
}
