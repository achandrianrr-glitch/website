@extends('layouts.app')

@section('title', 'Kategori')
@section('meta_description', 'Kelola kategori barang inventaris.')

@section('content')
    <div class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                    Kategori
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola kategori barang inventaris Rpl.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-[10px] font-medium text-blue-700 ring-1 ring-blue-600/20 dark:bg-blue-900/20 dark:text-blue-400">
                    {{ $kategori->total() }} kategori
                </span>

                <button type="button"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                    @click="$dispatch('open-tambah-kategori')">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Kategori</span>
                </button>
            </div>
        </div>

        @if ($kategori->count() > 0)
            <div
                class="hidden overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 lg:block">
                <div class="overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-0">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    #
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Nama
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Deskripsi
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Jumlah Barang
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-left text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Dibuat
                                </th>
                                <th scope="col"
                                    class="border-b border-gray-200 px-3 py-2 text-right text-[11px] uppercase tracking-wider text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kategori as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td
                                        class="border-b border-gray-100 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ $kategori->firstItem() + $loop->index }}
                                    </td>

                                    <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">
                                                {{ $item->nama }}
                                            </p>
                                        </div>
                                    </td>

                                    <td
                                        class="border-b border-gray-100 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ $item->deskripsi ?: '—' }}
                                    </td>

                                    <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $item->barang_count }} barang
                                        </span>
                                    </td>

                                    <td
                                        class="border-b border-gray-100 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                                        {{ $item->created_at?->format('d M Y') }}
                                    </td>

                                    <td class="border-b border-gray-100 px-3 py-2 dark:border-gray-700">
                                        <div class="flex justify-end gap-1">
                                            <button type="button"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-amber-50 text-sm text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30"
                                                @click="$dispatch('open-edit-kategori-{{ $item->id }}')"
                                                title="Edit kategori" aria-label="Edit kategori">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button type="button"
                                                class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-red-50 text-sm text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                                @click="$dispatch('open-hapus-kategori-{{ $item->id }}')"
                                                title="Hapus kategori" aria-label="Hapus kategori">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid gap-3 lg:hidden">
                @foreach ($kategori as $item)
                    <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                    {{ $item->nama }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $item->deskripsi ?: 'Tanpa deskripsi' }}
                                </p>
                            </div>

                            <span
                                class="inline-flex shrink-0 items-center rounded-full bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                                {{ $item->barang_count }} barang
                            </span>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-[11px] text-gray-400 dark:text-gray-500">
                                Dibuat {{ $item->created_at?->format('d M Y') }}
                            </p>

                            <div class="flex gap-1">
                                <button type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-amber-50 text-sm text-amber-600 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:hover:bg-amber-900/30"
                                    @click="$dispatch('open-edit-kategori-{{ $item->id }}')" title="Edit kategori"
                                    aria-label="Edit kategori">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button type="button"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-red-50 text-sm text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30"
                                    @click="$dispatch('open-hapus-kategori-{{ $item->id }}')" title="Hapus kategori"
                                    aria-label="Hapus kategori">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-1">
                {{ $kategori->links('components.pagination') }}
            </div>
        @else
            <x-empty-state icon="bi-tags" title="Belum ada kategori"
                message="Tambahkan kategori pertama untuk mulai mengelompokkan data barang.">
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
                    @click="$dispatch('open-tambah-kategori')">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Kategori</span>
                </button>
            </x-empty-state>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div x-data="{ modalTambah: @js(old('_form') === 'tambah-kategori') }" @open-tambah-kategori.window="modalTambah = true">
        <x-modal1 name="modalTambah" title="Tambah Kategori" max-width="max-w-lg">
            <form method="POST" action="{{ route('kategori.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="_form" value="tambah-kategori">

                <div>
                    <label for="nama" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                        Nama Kategori
                    </label>
                    <input id="nama" name="nama" type="text"
                        value="{{ old('_form') === 'tambah-kategori' ? old('nama') : '' }}" required maxlength="100"
                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                    @if (old('_form') === 'tambah-kategori')
                        @error('nama')
                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div>
                    <label for="deskripsi" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                        Deskripsi
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('_form') === 'tambah-kategori' ? old('deskripsi') : '' }}</textarea>
                    @if (old('_form') === 'tambah-kategori')
                        @error('deskripsi')
                            <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    @endif
                </div>

                <div class="flex justify-end gap-2 pt-1">
                    <button type="button"
                        class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        @click="modalTambah = false">
                        Batal
                    </button>

                    <button type="submit"
                        class="rounded-md bg-blue-600 px-3 py-1.5 text-xs text-white hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </x-modal1>
    </div>

    {{-- Modal Edit + Hapus per item --}}
    @foreach ($kategori as $item)
        <div x-data="{ editKategori: @js(old('_form') === 'edit-kategori-' . $item->id) }" @open-edit-kategori-{{ $item->id }}.window="editKategori = true">
            <x-modal1 name="editKategori" title="Edit Kategori" max-width="max-w-lg">
                <form method="POST" action="{{ route('kategori.update', $item) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="_form" value="edit-kategori-{{ $item->id }}">

                    <div>
                        <label for="nama-{{ $item->id }}"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                            Nama Kategori
                        </label>
                        <input id="nama-{{ $item->id }}" name="nama" type="text"
                            value="{{ old('_form') === 'edit-kategori-' . $item->id ? old('nama') : $item->nama }}"
                            required maxlength="100"
                            class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        @if (old('_form') === 'edit-kategori-' . $item->id)
                            @error('nama')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label for="deskripsi-{{ $item->id }}"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">
                            Deskripsi
                        </label>
                        <textarea id="deskripsi-{{ $item->id }}" name="deskripsi" rows="3"
                            class="block w-full rounded-md border-gray-300 px-2.5 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">{{ old('_form') === 'edit-kategori-' . $item->id ? old('deskripsi') : $item->deskripsi }}</textarea>
                        @if (old('_form') === 'edit-kategori-' . $item->id)
                            @error('deskripsi')
                                <p class="mt-1 text-[11px] text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button"
                            class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            @click="editKategori = false">
                            Batal
                        </button>

                        <button type="submit"
                            class="rounded-md bg-amber-500 px-3 py-1.5 text-xs text-white hover:bg-amber-600">
                            Perbarui
                        </button>
                    </div>
                </form>
            </x-modal1>
        </div>

        <div x-data="{ hapusKategori: false }" @open-hapus-kategori-{{ $item->id }}.window="hapusKategori = true">
            <x-confirm-modal name="hapusKategori" title="Hapus Kategori"
                message="Kategori '{{ $item->nama }}' akan dihapus. Jika kategori ini masih memiliki barang, sistem akan menolak proses hapus."
                confirm-text="Ya, Hapus">
                <x-slot:footer>
                    <button type="button"
                        class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        @click="hapusKategori = false">
                        Batal
                    </button>

                    <form method="POST" action="{{ route('kategori.destroy', $item) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                            class="rounded-md bg-red-500 px-3 py-1.5 text-xs text-white hover:bg-red-600">
                            Ya, Hapus
                        </button>
                    </form>
                </x-slot:footer>
            </x-confirm-modal>
        </div>
    @endforeach
@endsection
