@extends('layouts.guest')

@section('title', 'Peminjaman Lab RPL')
@section('meta_description', 'Halaman publik peminjaman dan pengembalian barang Lab RPL.')

@section('content')
    @php
        $hasilKode = session('hasil_kode');
        $hasilSukses = session('peminjaman_sukses');
        $tabAktif = session('tab_aktif', 'peminjaman');

        $kelasData = $kelas
            ->map(
                fn($item) => [
                    'id' => $item->id,
                    'nama' => $item->nama,
                ],
            )
            ->values();

        $jurusanData = $jurusan
            ->map(
                fn($item) => [
                    'id' => $item->id,
                    'nama' => $item->nama,
                ],
            )
            ->values();

        $oldItems = [];
        $oldItemsJson = old('items_json');

        if (is_string($oldItemsJson) && trim($oldItemsJson) !== '') {
            $decodedOldItems = json_decode($oldItemsJson, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedOldItems)) {
                $oldItems = collect($decodedOldItems)
                    ->filter(fn($item) => is_array($item))
                    ->map(function (array $item) {
                        return [
                            'barang_id' => isset($item['barang_id']) ? (int) $item['barang_id'] : null,
                            'nama' => $item['nama'] ?? '',
                            'tipe' => $item['tipe'] ?? 'aset',
                            'jumlah' => isset($item['jumlah']) ? max(1, (int) $item['jumlah']) : 1,
                            'max' => isset($item['max']) ? max(1, (int) $item['max']) : 1,
                            'unit_tersedia' => isset($item['unit_tersedia']) ? (int) $item['unit_tersedia'] : null,
                            'qty_tersedia' => isset($item['qty_tersedia']) ? (int) $item['qty_tersedia'] : null,
                            'label_kondisi' => $item['label_kondisi'] ?? 'Baik',
                            'kondisi' => isset($item['kondisi']) ? (int) $item['kondisi'] : 100,
                        ];
                    })
                    ->values()
                    ->all();
            }
        }
    @endphp

    <div x-data="{
        tab: @js($tabAktif === 'pengembalian' ? 'pengembalian' : 'peminjaman'),
        search: '',
        results: [],
        cart: @js($oldItems),
        searchTimeout: null,
        loadingAjukan: false,
        kelasId: @js(old('kelas_id')),
        jurusanId: @js(old('jurusan_id')),
        kelasList: @js($kelasData),
        jurusanList: @js($jurusanData),
    
        get jurusanFiltered() {
            if (!this.kelasId) {
                return [];
            }
    
            return this.jurusanList;
        },
    
        init() {
            if (!this.kelasId) {
                this.jurusanId = '';
            }
        },
    
        async cariBarang() {
            clearTimeout(this.searchTimeout);
    
            this.searchTimeout = setTimeout(async () => {
                if (this.search.trim().length < 2) {
                    this.results = [];
                    return;
                }
    
                try {
                    const response = await fetch(`/pinjam/cari-barang?q=${encodeURIComponent(this.search)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
    
                    if (!response.ok) {
                        this.results = [];
                        return;
                    }
    
                    this.results = await response.json();
                } catch (error) {
                    this.results = [];
                }
            }, 250);
        },
    
        tambahKeDaftar(item) {
            const existingIndex = this.cart.findIndex((cartItem) => Number(cartItem.barang_id) === Number(item.id));
    
            if (existingIndex !== -1) {
                if (item.tipe === 'aset') {
                    this.cart[existingIndex].jumlah = Number(this.cart[existingIndex].jumlah || 1) + 1;
                } else {
                    const jumlahBaru = Number(this.cart[existingIndex].jumlah || 1) + 1;
                    this.cart[existingIndex].jumlah = Math.min(jumlahBaru, Number(this.cart[existingIndex].max || 1));
                }
    
                this.search = '';
                this.results = [];
                return;
            }
    
            this.cart.push({
                barang_id: Number(item.id),
                nama: item.nama,
                tipe: item.tipe,
                jumlah: 1,
                max: item.tipe === 'stok' ?
                    Number(item.qty_tersedia || 1) :
                    Number(item.unit_tersedia || 1),
                unit_tersedia: item.unit_tersedia ?? null,
                qty_tersedia: item.qty_tersedia ?? null,
                label_kondisi: item.label_kondisi,
                kondisi: Number(item.kondisi || 100),
            });
    
            this.search = '';
            this.results = [];
        },
    
        hapusItem(index) {
            this.cart.splice(index, 1);
        },
    
        salinKode(kode) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(kode);
            }
        }
    }" class="min-h-screen">
        <header class="sticky top-0 z-20 h-12 border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="mx-auto flex h-full max-w-2xl items-center justify-between px-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo SMKN 9 Malang" class="h-6 w-6 object-contain">
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                        Peminjaman Lab RPL — SMKN 9 Malang
                    </p>
                </div>

                <button type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                    @click="toggleDark()" :title="isDark ? 'Ubah ke mode terang' : 'Ubah ke mode gelap'">
                    <i class="bi text-sm" :class="isDark ? 'bi-sun' : 'bi-moon'"></i>
                </button>
            </div>
        </header>

        <div class="mx-auto max-w-2xl px-4 py-4">
            <div class="space-y-4">
                <div class="inline-flex w-full rounded-lg bg-gray-100 p-1 dark:bg-gray-700">
                    <button type="button" @click="tab = 'peminjaman'"
                        :class="tab === 'peminjaman'
                            ?
                            'bg-white text-gray-800 shadow dark:bg-gray-800 dark:text-gray-100' :
                            'text-gray-500 dark:text-gray-300'"
                        class="flex-1 rounded-md px-3 py-2 text-xs font-medium">
                        <i class="bi bi-box-arrow-down mr-1"></i>
                        Peminjaman Baru
                    </button>

                    <button type="button" @click="tab = 'pengembalian'"
                        :class="tab === 'pengembalian'
                            ?
                            'bg-white text-gray-800 shadow dark:bg-gray-800 dark:text-gray-100' :
                            'text-gray-500 dark:text-gray-300'"
                        class="flex-1 rounded-md px-3 py-2 text-xs font-medium">
                        <i class="bi bi-arrow-return-left mr-1"></i>
                        Pengembalian
                    </button>
                </div>

                <div x-cloak x-show="tab === 'peminjaman'" x-transition class="space-y-4">
                    @if ($hasilSukses)
                        <div
                            class="mx-auto max-w-sm rounded-xl border border-gray-200 bg-white p-6 shadow-md animate-slide-up dark:border-gray-700 dark:bg-gray-800">
                            <div class="text-center">
                                <i class="bi bi-check-circle-fill text-4xl text-emerald-500"></i>

                                <h2 class="mt-3 text-base font-bold text-gray-800 dark:text-gray-100">
                                    Peminjaman Berhasil!
                                </h2>

                                <div
                                    class="mx-auto mt-4 inline-flex rounded-xl border border-blue-200 px-4 py-3 animate-pulse-ring dark:border-blue-800/40">
                                    <span class="font-mono text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $hasilSukses['kode_pinjam'] }}
                                    </span>
                                </div>

                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Simpan kode ini untuk pengembalian barang.
                                </p>

                                <div class="mt-4 flex justify-center gap-2">
                                    <button type="button"
                                        class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-1.5 text-xs text-white hover:bg-blue-700"
                                        @click="salinKode(@js($hasilSukses['kode_pinjam']))">
                                        <i class="bi bi-clipboard"></i>
                                        <span>Salin Kode</span>
                                    </button>

                                    <button type="button"
                                        class="inline-flex items-center gap-2 rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                                        onclick="window.print()">
                                        <i class="bi bi-printer"></i>
                                        <span>Cetak</span>
                                    </button>
                                </div>

                                <div class="mt-4 space-y-2 text-left">
                                    @foreach ($hasilSukses['items'] as $item)
                                        <div
                                            class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40 dark:text-gray-200">
                                            {{ $item['barang'] }} — {{ $item['unit_qty'] }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('siswa.ajukan') }}"
                        class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800"
                        @submit="loadingAjukan = true">
                        @csrf

                        <input type="hidden" name="items_json" :value="JSON.stringify(cart)">

                        <section class="space-y-3">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                    Data Diri
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="nama_peminjam"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input id="nama_peminjam" name="nama_peminjam" type="text"
                                        value="{{ old('nama_peminjam') }}"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @error('nama_peminjam')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="kelas_id"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Tingkat Kelas <span class="text-red-500">*</span>
                                    </label>
                                    <select id="kelas_id" name="kelas_id" x-model="kelasId"
                                        @change="if (!kelasId) jurusanId = ''"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">Pilih kelas</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" @selected((string) old('kelas_id') === (string) $item->id)>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kelas_id')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="jurusan_id"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Jurusan <span class="text-red-500">*</span>
                                    </label>
                                    <select id="jurusan_id" name="jurusan_id" x-model="jurusanId" :disabled="!kelasId"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm disabled:cursor-not-allowed disabled:opacity-60 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                        <option value="">Pilih jurusan</option>
                                        <template x-for="item in jurusanFiltered" :key="item.id">
                                            <option :value="item.id" x-text="item.nama"></option>
                                        </template>
                                    </select>
                                    @error('jurusan_id')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="mata_pelajaran"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Mata Pelajaran
                                    </label>
                                    <input id="mata_pelajaran" name="mata_pelajaran" type="text"
                                        value="{{ old('mata_pelajaran') }}"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @error('mata_pelajaran')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="no_hp"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        No. HP
                                    </label>
                                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                    @error('no_hp')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="catatan"
                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                        Catatan
                                    </label>
                                    <textarea id="catatan" name="catatan" rows="3"
                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('catatan') }}</textarea>
                                    @error('catatan')
                                        <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <div>
                                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                    Barang Dipinjam
                                </h2>
                            </div>

                            <div class="relative">
                                <label for="barang_search"
                                    class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                    Cari Barang
                                </label>

                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-gray-400">
                                        <i class="bi bi-search text-sm"></i>
                                    </span>

                                    <input id="barang_search" type="text" x-model="search" @input="cariBarang()"
                                        autocomplete="off" placeholder="Ketik minimal 2 huruf..."
                                        class="block w-full rounded-md border-gray-300 py-1.5 pl-8 pr-2.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                </div>

                                <div x-cloak x-show="results.length > 0"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="absolute z-20 mt-2 w-full rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                    <ul class="max-h-64 overflow-y-auto py-1">
                                        <template x-for="item in results" :key="item.id">
                                            <li>
                                                <button type="button"
                                                    class="flex w-full items-start justify-between gap-3 px-3 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700/40"
                                                    @click="tambahKeDaftar(item)">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100"
                                                            x-text="item.nama"></p>
                                                        <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                                            <span x-text="item.kategori"></span> ·
                                                            <span x-text="item.merek"></span> ·
                                                            <span x-text="item.tipe === 'aset' ? 'Aset' : 'Stok'"></span>
                                                        </p>
                                                    </div>

                                                    <div
                                                        class="shrink-0 text-right text-xs text-gray-500 dark:text-gray-400">
                                                        <p>
                                                            <span x-text="item.label_kondisi"></span>
                                                            <span x-text="item.kondisi + '%'"></span>
                                                        </p>
                                                        <p
                                                            x-text="item.tipe === 'aset'
                                                            ? ((item.unit_tersedia ?? 0) + ' unit')
                                                            : ((item.qty_tersedia ?? 0) + ' stok')">
                                                        </p>
                                                    </div>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            @error('items_json')
                                <p class="text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror

                            <div
                                class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                                <template x-if="cart.length === 0">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Belum ada barang di daftar.
                                    </p>
                                </template>

                                <template x-if="cart.length > 0">
                                    <div class="space-y-3">
                                        <template x-for="(item, index) in cart" :key="`${item.barang_id}-${index}`">
                                            <div
                                                class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div class="min-w-0">
                                                        <p class="truncate text-sm font-medium text-gray-800 dark:text-gray-100"
                                                            x-text="item.nama"></p>
                                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                            <span x-text="item.tipe === 'aset' ? 'Aset' : 'Stok'"></span>
                                                            ·
                                                            <span x-text="item.label_kondisi"></span>
                                                            <span x-text="item.kondisi + '%'"></span>
                                                        </p>
                                                    </div>

                                                    <button type="button"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-red-50 text-sm text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                                        @click="hapusItem(index)" title="Hapus dari daftar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <div class="mt-3" x-show="item.tipe === 'aset'">
                                                    <p class="text-xs text-blue-600 dark:text-blue-400">
                                                        Unit terbaik tersedia akan dipilih otomatis.
                                                    </p>
                                                </div>

                                                <div class="mt-3" x-show="item.tipe === 'stok'">
                                                    <label
                                                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                        Jumlah
                                                    </label>
                                                    <input type="number" min="1" :max="item.max"
                                                        x-model.number="item.jumlah"
                                                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                                                    <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                        Maksimal <span x-text="item.max"></span>.
                                                    </p>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </section>

                        <div>
                            <button type="submit" :disabled="loadingAjukan || cart.length === 0"
                                :class="loadingAjukan || cart.length === 0 ? 'opacity-70 cursor-not-allowed' : ''"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                                <span x-show="!loadingAjukan">Ajukan Peminjaman</span>
                                <span x-show="loadingAjukan" class="inline-flex items-center gap-2">
                                    <i class="bi bi-arrow-repeat animate-spin-smooth"></i>
                                    <span>Menyimpan...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <div x-cloak x-show="tab === 'pengembalian'" x-transition class="space-y-4">
                    <form method="POST" action="{{ route('siswa.cek-kode') }}"
                        class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        @csrf

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_auto]">
                            <div>
                                <label for="kode_pinjam"
                                    class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                    Kode Peminjaman
                                </label>
                                <input id="kode_pinjam" name="kode_pinjam" type="text"
                                    value="{{ old('kode_pinjam') }}"
                                    class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 font-mono text-sm uppercase dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                            </div>

                            <div class="self-end">
                                <button type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm text-white hover:bg-blue-700">
                                    <i class="bi bi-search"></i>
                                    <span>Cek Kode</span>
                                </button>
                            </div>
                        </div>
                    </form>

                    @if (session('galat_kode'))
                        <div
                            class="rounded-lg border border-red-200 bg-red-50 px-3 py-2.5 text-xs text-red-600 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-400">
                            {{ session('galat_kode') }}
                        </div>
                    @endif

                    @if ($hasilKode)
                        <div
                            class="rounded-lg border border-blue-200 bg-blue-50 p-3 dark:border-blue-900/30 dark:bg-blue-900/10">
                            <p class="text-sm font-semibold text-blue-800 dark:text-blue-300">
                                {{ $hasilKode['kode_pinjam'] }}
                            </p>
                            <p class="mt-1 text-xs text-blue-700 dark:text-blue-400">
                                {{ $hasilKode['nama_peminjam'] }} · {{ $hasilKode['kelas'] }} /
                                {{ $hasilKode['jurusan'] }} · {{ $hasilKode['tanggal_pinjam'] }}
                            </p>
                        </div>

                        <div class="space-y-3">
                            @foreach ($hasilKode['items'] as $item)
                                @php
                                    $labelKondisiAwal = match (true) {
                                        ($item['kondisi_awal'] ?? null) >= 80 => 'Baik',
                                        ($item['kondisi_awal'] ?? null) >= 60 => 'Lumayan',
                                        ($item['kondisi_awal'] ?? null) >= 35 => 'Rusak',
                                        default => 'Rusak Parah',
                                    };
                                @endphp

                                <div
                                    class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                {{ $item['barang'] }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $item['unit_qty'] }}
                                            </p>
                                        </div>

                                        <x-status-badge :status="$item['status_item']" />
                                    </div>

                                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        @if (!is_null($item['kondisi_awal']))
                                            Kondisi awal: {{ $labelKondisiAwal }} {{ $item['kondisi_awal'] }}%
                                        @endif

                                        @if (!is_null($item['kondisi_kembali']))
                                            <span class="mx-1">·</span>
                                            Kondisi kembali: {{ $item['kondisi_kembali'] }}%
                                        @endif
                                    </div>

                                    @if ($item['status_item'] === 'dipinjam')
                                        <div x-data="{
                                            open: false,
                                            kondisi: {{ is_numeric($item['kondisi_awal'] ?? null) ? (int) $item['kondisi_awal'] : 100 }},
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
                                        }" class="mt-3">
                                            <button type="button"
                                                class="inline-flex items-center gap-2 rounded-md bg-teal-600 px-3 py-1.5 text-xs text-white hover:bg-teal-700"
                                                @click="open = !open">
                                                <i class="bi bi-arrow-return-left"></i>
                                                <span>Kembalikan Item Ini</span>
                                            </button>

                                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 max-h-0"
                                                x-transition:enter-end="opacity-100 max-h-40"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 max-h-40"
                                                x-transition:leave-end="opacity-0 max-h-0"
                                                class="mt-3 overflow-hidden rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                                                <form method="POST" action="{{ route('siswa.kembalikan') }}"
                                                    class="space-y-3">
                                                    @csrf

                                                    <input type="hidden" name="detail_id"
                                                        value="{{ $item['detail_id'] }}">

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
                                                            max="100" x-model="kondisi" :style="warnaSlider"
                                                            class="block w-full">

                                                        <div x-show="kondisi <= 34" x-transition
                                                            class="mt-2 text-xs text-red-600 dark:text-red-400">
                                                            Rusak Parah — unit akan otomatis dikunci.
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                                                            Catatan Kerusakan
                                                        </label>
                                                        <textarea name="catatan_kembali" rows="1"
                                                            class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
                                                    </div>

                                                    <div class="flex justify-end">
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-2 rounded-md bg-teal-600 px-3 py-1.5 text-xs text-white hover:bg-teal-700">
                                                            <i class="bi bi-check-lg"></i>
                                                            <span>Simpan Pengembalian</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                                            @if ($item['waktu_kembali'])
                                                Dikembalikan: {{ $item['waktu_kembali'] }}
                                            @endif

                                            @if ($item['catatan_kembali'])
                                                <div class="mt-1">
                                                    Catatan: {{ $item['catatan_kembali'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
