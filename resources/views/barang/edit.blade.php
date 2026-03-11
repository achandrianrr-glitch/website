@extends('layouts.app')

@section('title', 'Edit Barang')
@section('meta_description', 'Edit data barang inventaris Shiro.')

@section('content')
    @php
        $qtyTerpakai =
            $barang->tipe === 'stok'
                ? (int) $barang->qty_dipinjam + (int) $barang->qty_rusak + (int) $barang->qty_keluar
                : 0;
    @endphp

    <div class="space-y-3">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    Edit Barang
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui informasi barang tanpa mengubah riwayat inventaris.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('barang.show', $barang) }}"
                    class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Kembali
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('barang.update', $barang) }}"
            class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800 lg:p-4"
            x-data="{
                tipe: @js(old('tipe', $barang->tipe)),
                merekId: @js(old('merek_id', $barang->merek_id ? (string) $barang->merek_id : ($barang->merek_manual ? 'lainnya' : ''))),
                lokasiId: @js(old('lokasi_id', $barang->lokasi_id ? (string) $barang->lokasi_id : ($barang->lokasi_manual ? 'lainnya' : ''))),
                kondisi: Number(@js(old('kondisi_awal', $barang->tipe === 'stok' ? (int) $barang->kondisi_stok : 100))),
                loading: false,
            
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
                },
            
                get warnaSlider() {
                    if (this.kondisi >= 80) return 'accent-color: #059669';
                    if (this.kondisi >= 60) return 'accent-color: #2563eb';
                    if (this.kondisi >= 35) return 'accent-color: #f59e0b';
                    return 'accent-color: #ef4444';
                }
            }" @submit="loading = true">
            @csrf
            @method('PATCH')

            <div class="space-y-4">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                        Tipe Barang
                    </label>

                    <div class="inline-flex w-fit gap-2 rounded-lg bg-gray-100 p-1 dark:bg-gray-700">
                        <button type="button" disabled
                            :class="tipe === 'aset'
                                ?
                                'bg-white text-gray-800 shadow dark:bg-gray-800 dark:text-gray-100' :
                                'text-gray-500 dark:text-gray-300'"
                            class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-md px-3 py-2 text-xs opacity-90">
                            <i class="bi bi-cpu"></i>
                            <span>Aset</span>
                        </button>

                        <button type="button" disabled
                            :class="tipe === 'stok'
                                ?
                                'bg-white text-gray-800 shadow dark:bg-gray-800 dark:text-gray-100' :
                                'text-gray-500 dark:text-gray-300'"
                            class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-md px-3 py-2 text-xs opacity-90">
                            <i class="bi bi-stack"></i>
                            <span>Stok</span>
                        </button>
                    </div>

                    <input type="hidden" name="tipe" value="{{ $barang->tipe }}">

                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                        Tipe barang dikunci agar struktur data aset dan stok tetap konsisten.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                    <div class="space-y-3">
                        <div>
                            <label for="nama" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <input id="nama" name="nama" type="text" value="{{ old('nama', $barang->nama) }}"
                                required maxlength="200"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('nama')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="kategori_id"
                                class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select id="kategori_id" name="kategori_id" required
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Pilih kategori</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('kategori_id', $barang->kategori_id) === (string) $item->id)>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="merek_id" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Merek
                            </label>
                            <select id="merek_id" name="merek_id" x-model="merekId"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Pilih merek</option>
                                @foreach ($merek as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('merek_id', $barang->merek_id) === (string) $item->id)>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                                <option value="lainnya" @selected(old('merek_id', $barang->merek_manual ? 'lainnya' : '') === 'lainnya')>
                                    Lainnya
                                </option>
                            </select>

                            @error('merek_id')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <div x-cloak x-show="merekId === 'lainnya'" x-transition class="mt-2">
                                <input name="merek_manual" type="text"
                                    value="{{ old('merek_manual', $barang->merek_manual) }}"
                                    placeholder="Masukkan merek manual"
                                    class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('merek_manual')
                                    <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="lokasi_id" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Lokasi
                            </label>
                            <select id="lokasi_id" name="lokasi_id" x-model="lokasiId"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <option value="">Pilih lokasi</option>
                                @foreach ($lokasi as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('lokasi_id', $barang->lokasi_id) === (string) $item->id)>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                                <option value="lainnya" @selected(old('lokasi_id', $barang->lokasi_manual ? 'lainnya' : '') === 'lainnya')>
                                    Lainnya
                                </option>
                            </select>

                            @error('lokasi_id')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <div x-cloak x-show="lokasiId === 'lainnya'" x-transition class="mt-2">
                                <input name="lokasi_manual" type="text"
                                    value="{{ old('lokasi_manual', $barang->lokasi_manual) }}"
                                    placeholder="Masukkan lokasi manual"
                                    class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                @error('lokasi_manual')
                                    <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="spesifikasi"
                                class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Spesifikasi
                            </label>
                            <textarea id="spesifikasi" name="spesifikasi" rows="3"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('spesifikasi', $barang->spesifikasi) }}</textarea>
                            @error('spesifikasi')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="tahun_pengadaan"
                                class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Tahun Pengadaan
                            </label>
                            <input id="tahun_pengadaan" name="tahun_pengadaan" type="number" min="2000"
                                max="{{ now()->year + 1 }}" value="{{ old('tahun_pengadaan', $barang->tahun_pengadaan) }}"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            @error('tahun_pengadaan')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        @if ($barang->tipe === 'aset')
                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                                <p class="text-xs font-medium text-gray-700 dark:text-gray-200">
                                    Informasi Unit Aset
                                </p>

                                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <p>
                                        Total Unit:
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ $barang->unitBarang()->count() }}
                                        </span>
                                    </p>
                                    <p>
                                        Kelola Unit:
                                        <a href="{{ route('barang.unit', $barang) }}"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                            buka halaman unit
                                        </a>
                                    </p>
                                </div>

                                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    Penambahan, perubahan kondisi, dan status per unit dilakukan di halaman kelola unit.
                                </p>
                            </div>

                            <input type="hidden" name="kondisi_awal" value="{{ old('kondisi_awal', 100) }}">
                        @endif

                        @if ($barang->tipe === 'stok')
                            <div>
                                <label for="qty_total"
                                    class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                    Jumlah Total
                                </label>
                                <input id="qty_total" name="qty_total" type="number" min="{{ $qtyTerpakai }}"
                                    value="{{ old('qty_total', (int) $barang->qty_total) }}"
                                    class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                    Jumlah total stok dapat diperbarui, tetapi tidak boleh lebih kecil dari total stok yang
                                    sudah dipinjam, rusak, atau keluar.
                                </p>

                                <div class="mt-2 grid grid-cols-2 gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                                    <p>
                                        Dipinjam:
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ (int) $barang->qty_dipinjam }}
                                        </span>
                                    </p>
                                    <p>
                                        Rusak:
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ (int) $barang->qty_rusak }}
                                        </span>
                                    </p>
                                    <p>
                                        Keluar:
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ (int) $barang->qty_keluar }}
                                        </span>
                                    </p>
                                    <p>
                                        Minimum total:
                                        <span class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ $qtyTerpakai }}
                                        </span>
                                    </p>
                                </div>

                                @error('qty_total')
                                    <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="mb-1 flex items-center justify-between gap-3">
                                    <label for="kondisi_awal"
                                        class="block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Kondisi Stok % <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-sm font-semibold" :class="warnaKondisiText">
                                        <span x-text="labelKondisi"></span> <span x-text="kondisi + '%'"></span>
                                    </span>
                                </div>

                                <input id="kondisi_awal" name="kondisi_awal" type="range" min="0"
                                    max="100" x-model="kondisi" :style="warnaSlider" class="block w-full">

                                <div
                                    class="mt-2 flex items-center justify-between text-[10px] text-gray-500 dark:text-gray-400">
                                    <span>Rusak Parah 0%</span>
                                    <span>Rusak 35%</span>
                                    <span>Lumayan 60%</span>
                                    <span>Baik 80%</span>
                                </div>

                                @error('kondisi_awal')
                                    <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <div>
                            <label for="catatan" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                Catatan
                            </label>
                            <textarea id="catatan" name="catatan" rows="3"
                                class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('catatan', $barang->catatan) }}</textarea>
                            @error('catatan')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-200 pt-3 dark:border-gray-700">
                    <a href="{{ route('barang.show', $barang) }}"
                        class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                        Batal
                    </a>

                    <button type="submit" :disabled="loading" :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
                        class="inline-flex items-center gap-2 rounded-md bg-amber-500 px-4 py-1.5 text-sm text-white hover:bg-amber-600">
                        <span x-show="!loading">Perbarui</span>
                        <span x-show="loading" class="inline-flex items-center gap-2">
                            <i class="bi bi-arrow-repeat animate-spin-smooth"></i>
                            <span>Menyimpan...</span>
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
