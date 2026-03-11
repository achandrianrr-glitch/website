@extends('layouts.app')

@section('title', 'Kelola Unit')
@section('meta_description', 'Kelola unit aset inventaris Shiro.')

@section('content')
    @php
        $semuaUnit = $barang->relationLoaded('unitBarang') ? $barang->unitBarang : $barang->unitBarang()->get();

        $totalUnit = $semuaUnit->count();
        $jumlahTersedia = $semuaUnit->where('status', 'tersedia')->count();
        $jumlahDipinjam = $semuaUnit->where('status', 'dipinjam')->count();
        $jumlahRusak = $semuaUnit->where('status', 'rusak')->count();
    @endphp

    <div class="space-y-3">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    Kelola Unit
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $barang->nama }} · {{ $barang->kategori?->nama }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('barang.show', $barang) }}"
                    class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Kembali ke Detail
                </a>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Total Unit</p>
                    <p class="mt-1 text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ $totalUnit }}
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Tersedia</p>
                    <p class="mt-1 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ $jumlahTersedia }}
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Dipinjam</p>
                    <p class="mt-1 text-sm font-semibold text-amber-600 dark:text-amber-400">
                        {{ $jumlahDipinjam }}
                    </p>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                    <p class="text-[11px] text-gray-500 dark:text-gray-400">Rusak</p>
                    <p class="mt-1 text-sm font-semibold text-red-600 dark:text-red-400">
                        {{ $jumlahRusak }}
                    </p>
                </div>
            </div>
        </div>

        @if ($unit->count() > 0)
            <div class="space-y-3">
                @foreach ($unit as $item)
                    @php
                        $formKey = 'unit-' . $item->id;
                        $isCurrentForm = old('_form') === $formKey;

                        $nilaiSerial = $isCurrentForm
                            ? old('serial_number', $item->serial_number)
                            : $item->serial_number;
                        $nilaiStatus = $isCurrentForm ? old('status', $item->status) : $item->status;
                        $nilaiCatatan = $isCurrentForm ? old('catatan', $item->catatan) : $item->catatan;
                        $nilaiKondisi = (int) ($isCurrentForm ? old('kondisi', $item->kondisi) : $item->kondisi);
                    @endphp

                    <div x-data="{
                        kondisi: {{ $nilaiKondisi }},
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
                        },
                    
                        get warnaProgress() {
                            if (this.kondisi >= 80) return 'bg-emerald-500';
                            if (this.kondisi >= 60) return 'bg-blue-500';
                            if (this.kondisi >= 35) return 'bg-amber-500';
                            return 'bg-red-500';
                        }
                    }"
                        class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $item->nomor_unit }}
                                    </h2>
                                    <x-kondisi-badge :kondisi="$nilaiKondisi" />
                                    <x-status-badge :status="$nilaiStatus" />
                                </div>

                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Serial: {{ $item->serial_number ?: 'Belum diisi' }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-600 dark:text-gray-300" x-text="kondisi + '%'"></span>

                                <div class="w-full max-w-[72px]">
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-1.5 rounded-full transition-all duration-700 ease-out"
                                            :class="warnaProgress" :style="`width: ${kondisi}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('barang.unit.update', [$barang, $item]) }}"
                            class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2" @submit="loading = true">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="_form" value="{{ $formKey }}">

                            <div class="space-y-3">
                                <div>
                                    <label for="serial_number_{{ $item->id }}"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Serial Number
                                    </label>
                                    <input id="serial_number_{{ $item->id }}" name="serial_number" type="text"
                                        value="{{ $nilaiSerial }}" maxlength="100"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @if ($isCurrentForm)
                                        @error('serial_number')
                                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="status_{{ $item->id }}"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Status Operasional
                                    </label>
                                    <select id="status_{{ $item->id }}" name="status"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="tersedia" @selected($nilaiStatus === 'tersedia')>Tersedia</option>
                                        <option value="dipinjam" @selected($nilaiStatus === 'dipinjam')>Dipinjam</option>
                                        <option value="rusak" @selected($nilaiStatus === 'rusak')>Rusak</option>
                                        <option value="keluar" @selected($nilaiStatus === 'keluar')>Keluar</option>
                                    </select>
                                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                        Ini adalah status operasional, terpisah dari kondisi fisik.
                                    </p>
                                    @if ($isCurrentForm)
                                        @error('status')
                                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </div>

                                <div>
                                    <label for="catatan_{{ $item->id }}"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Catatan
                                    </label>
                                    <textarea id="catatan_{{ $item->id }}" name="catatan" rows="3"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ $nilaiCatatan }}</textarea>
                                    @if ($isCurrentForm)
                                        @error('catatan')
                                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <div class="mb-1 flex items-center justify-between gap-3">
                                        <label for="kondisi_{{ $item->id }}"
                                            class="block text-xs font-medium text-gray-600 dark:text-gray-300">
                                            Kondisi Fisik %
                                        </label>
                                        <span class="text-sm font-semibold" :class="warnaKondisiText">
                                            <span x-text="labelKondisi"></span> <span x-text="kondisi + '%'"></span>
                                        </span>
                                    </div>

                                    <input id="kondisi_{{ $item->id }}" name="kondisi" type="range" min="0"
                                        max="100" x-model="kondisi" :style="warnaSlider" class="block w-full">

                                    <div
                                        class="mt-2 flex items-center justify-between text-[10px] text-gray-500 dark:text-gray-400">
                                        <span>Rusak Parah 0%</span>
                                        <span>Rusak 35%</span>
                                        <span>Lumayan 60%</span>
                                        <span>Baik 80%</span>
                                    </div>

                                    <div x-cloak x-show="kondisi <= 34" x-transition
                                        class="mt-2 rounded-md border border-red-200 bg-red-50 px-2.5 py-2 text-[11px] text-red-600 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-400">
                                        Kondisi ≤34% akan otomatis mengubah status unit menjadi <strong>rusak</strong>.
                                    </div>

                                    @if ($isCurrentForm)
                                        @error('kondisi')
                                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    @endif
                                </div>

                                <div
                                    class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-200">
                                        Ringkasan Aturan
                                    </p>
                                    <ul class="mt-2 space-y-1 text-[11px] text-gray-500 dark:text-gray-400">
                                        <li>• Kondisi = kondisi fisik unit (0–100%).</li>
                                        <li>• Status = status operasional unit.</li>
                                        <li>• Jika kondisi ≤34%, status otomatis rusak saat disimpan.</li>
                                    </ul>
                                </div>

                                <div class="flex justify-end gap-2 pt-1">
                                    <button type="submit" :disabled="loading"
                                        :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
                                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-1.5 text-sm text-white hover:bg-blue-700">
                                        <span x-show="!loading">Simpan Perubahan</span>
                                        <span x-show="loading" class="inline-flex items-center gap-2">
                                            <i class="bi bi-arrow-repeat animate-spin-smooth"></i>
                                            <span>Menyimpan...</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="pt-1">
                {{ $unit->links('components.pagination') }}
            </div>
        @else
            <x-empty-state icon="bi-cpu" title="Belum ada unit" message="Unit aset belum tersedia untuk barang ini." />
        @endif
    </div>
@endsection
