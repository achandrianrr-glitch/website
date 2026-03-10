@extends('layouts.app')

@section('title', 'Detail Peminjaman')
@section('meta_description', 'Detail data peminjaman inventaris Shiro.')

@section('content')
    <div class="space-y-3">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    Detail Peminjaman
                </h1>
                <p class="mt-1 font-mono text-sm text-gray-500 dark:text-gray-400">
                    {{ $peminjaman->kode_pinjam }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-status-badge :status="$peminjaman->status" />

                <a href="{{ route('peminjaman.index') }}"
                    class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Kembali
                </a>
            </div>
        </div>

        <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Nama</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->nama_peminjam }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Kelas</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->kelas?->nama ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Jurusan</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->jurusan?->nama ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Mata Pelajaran</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->mata_pelajaran ?: '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal Pinjam</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->tanggal_pinjam ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') : '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">No. HP</p>
                    <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-100">
                        {{ $peminjaman->no_hp ?: '—' }}
                    </p>
                </div>
            </div>

            @if ($peminjaman->catatan)
                <div class="mt-4">
                    <p class="text-[11px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Catatan</p>
                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                        {{ $peminjaman->catatan }}
                    </p>
                </div>
            @endif
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    Daftar Item
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-0">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Barang
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Unit/Qty
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Kondisi Awal
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Status Item
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Kondisi Kembali
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Waktu
                            </th>
                            <th scope="col"
                                class="border-b border-gray-200 px-3 py-2 text-right text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($peminjaman->detailPeminjaman as $detail)
                            @php
                                $kondisiAwal = is_numeric($detail->kondisi_awal) ? (int) $detail->kondisi_awal : null;
                                $kondisiKembali = is_numeric($detail->kondisi_kembali)
                                    ? (int) $detail->kondisi_kembali
                                    : null;

                                $selisihKondisi =
                                    !is_null($kondisiAwal) && !is_null($kondisiKembali)
                                        ? $kondisiAwal - $kondisiKembali
                                        : 0;

                                $highlight =
                                    $selisihKondisi >= 10 || (!is_null($kondisiKembali) && $kondisiKembali <= 34);

                                $modalShouldOpen =
                                    old('detail_id') && (string) old('detail_id') === (string) $detail->id;
                                $initialKondisi = $modalShouldOpen
                                    ? (int) old('kondisi_kembali', $kondisiAwal ?? 100)
                                    : $kondisiAwal ?? 100;
                            @endphp

                            <tr
                                class="{{ $highlight ? 'bg-amber-50 dark:bg-amber-900/10' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td
                                    class="border-b border-gray-100 px-3 py-2 text-sm font-medium text-gray-800 dark:border-gray-700 dark:text-gray-100">
                                    {{ $detail->barang?->nama ?? '-' }}
                                </td>

                                <td
                                    class="border-b border-gray-100 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    {{ $detail->unitBarang?->nomor_unit ?? 'Qty ' . $detail->jumlah }}
                                </td>

                                <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                    @if (!is_null($kondisiAwal))
                                        <x-kondisi-badge :kondisi="$kondisiAwal" :show-value="true" />
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                    <x-status-badge :status="$detail->status_item" />
                                </td>

                                <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                    @if (!is_null($kondisiKembali))
                                        <x-kondisi-badge :kondisi="$kondisiKembali" :show-value="true" />
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>

                                <td
                                    class="border-b border-gray-100 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    {{ $detail->waktu_kembali ? \Illuminate\Support\Carbon::parse($detail->waktu_kembali)->format('d M Y H:i') : '—' }}
                                </td>

                                <td class="border-b border-gray-100 px-3 py-2 text-right dark:border-gray-700">
                                    @if ($detail->status_item === 'dipinjam')
                                        <div x-data="{
                                            open: @js($modalShouldOpen),
                                            kondisi: {{ $initialKondisi }},
                                            get labelKondisi() {
                                                if (this.kondisi >= 80) return 'Baik';
                                                if (this.kondisi >= 60) return 'Lumayan';
                                                if (this.kondisi >= 35) return 'Rusak';
                                                return 'Rusak Parah';
                                            },
                                            get warnaKondisiText() {
                                                if (this.kondisi >= 80) return 'text-emerald-600';
                                                if (this.kondisi >= 60) return 'text-blue-600';
                                                if (this.kondisi >= 35) return 'text-amber-600';
                                                return 'text-red-600';
                                            }
                                        }">
                                            <button type="button"
                                                class="inline-flex items-center gap-2 rounded-md bg-teal-600 px-3 py-1.5 text-xs text-white hover:bg-teal-700"
                                                @click="open = true">
                                                <i class="bi bi-arrow-return-left"></i>
                                                <span>Kembalikan</span>
                                            </button>

                                            <x-modal name="open" title="Proses Pengembalian" max-width="max-w-lg">
                                                <form method="POST"
                                                    action="{{ route('peminjaman.kembalikan', $peminjaman) }}"
                                                    class="space-y-3">
                                                    @csrf
                                                    @method('PATCH')

                                                    <input type="hidden" name="detail_id" value="{{ $detail->id }}">

                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                            {{ $detail->barang?->nama ?? '-' }}
                                                        </p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $detail->unitBarang?->nomor_unit ?? 'Qty ' . $detail->jumlah }}
                                                        </p>
                                                    </div>

                                                    <div>
                                                        <div class="mb-1 flex items-center justify-between gap-3">
                                                            <label
                                                                class="block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                                Kondisi Saat Kembali
                                                            </label>

                                                            <span class="text-sm font-semibold" :class="warnaKondisiText">
                                                                <span x-text="labelKondisi"></span>
                                                                <span x-text="kondisi + '%'"></span>
                                                            </span>
                                                        </div>

                                                        <input name="kondisi_kembali" type="range" min="0"
                                                            max="100" x-model="kondisi" class="block w-full">

                                                        <div x-show="kondisi <= 34" x-transition
                                                            class="mt-2 text-xs text-red-600 dark:text-red-400">
                                                            Kondisi ≤34% akan otomatis mengubah status unit menjadi rusak.
                                                        </div>

                                                        @if ($modalShouldOpen)
                                                            @error('kondisi_kembali')
                                                                <p class="mt-2 text-[11px] text-red-600 dark:text-red-400">
                                                                    {{ $message }}</p>
                                                            @enderror
                                                        @endif
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                            Catatan Kembali
                                                        </label>
                                                        <textarea name="catatan_kembali" rows="3"
                                                            class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ $modalShouldOpen ? old('catatan_kembali') : '' }}</textarea>

                                                        @if ($modalShouldOpen)
                                                            @error('catatan_kembali')
                                                                <p class="mt-2 text-[11px] text-red-600 dark:text-red-400">
                                                                    {{ $message }}</p>
                                                            @enderror
                                                        @endif
                                                    </div>

                                                    <div class="flex justify-end gap-2">
                                                        <button type="button"
                                                            class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                                            @click="open = false">
                                                            Batal
                                                        </button>

                                                        <button type="submit"
                                                            class="rounded-md bg-teal-600 px-3 py-1.5 text-xs text-white hover:bg-teal-700">
                                                            Simpan Pengembalian
                                                        </button>
                                                    </div>
                                                </form>
                                            </x-modal>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
