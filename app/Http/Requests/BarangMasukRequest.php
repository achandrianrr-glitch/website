<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarangMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        if ($this->routeIs('transaksi.simpan-masuk')) {
            return $this->rulesUntukTransaksiMasuk();
        }

        return $this->rulesUntukBarang();
    }

    protected function rulesUntukBarang(): array
    {
        $isStore = $this->isMethod('post');

        $rules = [
            'nama' => ['required', 'string', 'max:200'],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],

            'merek_id' => ['nullable', 'integer', 'exists:merek,id'],
            'merek_manual' => ['nullable', 'string', 'max:100'],

            'lokasi_id' => ['nullable', 'integer', 'exists:lokasi,id'],
            'lokasi_manual' => ['nullable', 'string', 'max:100'],

            'tipe' => ['required', Rule::in(['aset', 'stok'])],
            'spesifikasi' => ['nullable', 'string'],
            'tahun_pengadaan' => ['nullable', 'integer', 'digits:4', 'min:2000', 'max:' . (now()->year + 1)],
            'catatan' => ['nullable', 'string'],

            'kondisi_awal' => ['required', 'integer', 'between:0,100'],
        ];

        if ($isStore) {
            $rules['jumlah_unit'] = ['nullable', 'integer', 'min:1', 'max:100', 'required_if:tipe,aset'];
            $rules['serial_number_list'] = ['nullable', 'string'];
            $rules['qty_total'] = ['nullable', 'integer', 'min:1', 'required_if:tipe,stok'];
        } else {
            $rules['jumlah_unit'] = ['nullable'];
            $rules['serial_number_list'] = ['nullable'];
            $rules['qty_total'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    protected function rulesUntukTransaksiMasuk(): array
    {
        $modeBarang = $this->input('mode_barang', 'baru');

        $rules = [
            'mode_barang' => ['required', Rule::in(['baru', 'existing'])],

            'barang_id' => ['nullable', 'integer', 'required_if:mode_barang,existing', 'exists:barang,id'],

            'jumlah_masuk' => ['required', 'integer', 'min:1', 'max:1000'],
            'kondisi_saat_itu' => ['required', 'integer', 'between:0,100'],
            'sumber_tujuan' => ['nullable', 'string', 'max:200'],
            'tanggal_transaksi' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'serial_number_list' => ['nullable', 'string'],
        ];

        if ($modeBarang === 'baru') {
            $rules = array_merge($rules, [
                'nama' => ['required', 'string', 'max:200'],
                'kategori_id' => ['required', 'integer', 'exists:kategori,id'],

                'merek_id' => ['nullable', 'integer', 'exists:merek,id'],
                'merek_manual' => ['nullable', 'string', 'max:100'],

                'lokasi_id' => ['nullable', 'integer', 'exists:lokasi,id'],
                'lokasi_manual' => ['nullable', 'string', 'max:100'],

                'tipe' => ['required', Rule::in(['aset', 'stok'])],
                'spesifikasi' => ['nullable', 'string'],
                'tahun_pengadaan' => ['nullable', 'integer', 'digits:4', 'min:2000', 'max:' . (now()->year + 1)],
            ]);
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $merekId = $this->input('merek_id');
        $lokasiId = $this->input('lokasi_id');

        $this->merge([
            'nama' => is_string($this->nama) ? trim($this->nama) : $this->nama,
            'merek_manual' => is_string($this->merek_manual) ? trim($this->merek_manual) : $this->merek_manual,
            'lokasi_manual' => is_string($this->lokasi_manual) ? trim($this->lokasi_manual) : $this->lokasi_manual,
            'spesifikasi' => is_string($this->spesifikasi) ? trim($this->spesifikasi) : $this->spesifikasi,
            'catatan' => is_string($this->catatan) ? trim($this->catatan) : $this->catatan,
            'sumber_tujuan' => is_string($this->sumber_tujuan) ? trim($this->sumber_tujuan) : $this->sumber_tujuan,
            'mode_barang' => is_string($this->mode_barang) ? trim($this->mode_barang) : $this->mode_barang,

            'merek_id' => ($merekId === 'lainnya' || $merekId === '') ? null : $merekId,
            'lokasi_id' => ($lokasiId === 'lainnya' || $lokasiId === '') ? null : $lokasiId,
        ]);
    }

    public function messages(): array
    {
        return [
            'mode_barang.required' => 'Mode barang wajib dipilih.',
            'mode_barang.in' => 'Mode barang tidak valid.',

            'barang_id.required_if' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang yang dipilih tidak valid.',

            'nama.required' => 'Nama barang wajib diisi.',
            'nama.max' => 'Nama barang maksimal 200 karakter.',

            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori tidak valid.',

            'merek_id.exists' => 'Merek tidak valid.',
            'merek_manual.max' => 'Merek manual maksimal 100 karakter.',

            'lokasi_id.exists' => 'Lokasi tidak valid.',
            'lokasi_manual.max' => 'Lokasi manual maksimal 100 karakter.',

            'tipe.required' => 'Tipe barang wajib dipilih.',
            'tipe.in' => 'Tipe barang harus aset atau stok.',

            'tahun_pengadaan.digits' => 'Tahun pengadaan harus 4 digit.',
            'tahun_pengadaan.min' => 'Tahun pengadaan tidak valid.',
            'tahun_pengadaan.max' => 'Tahun pengadaan tidak valid.',

            'kondisi_awal.required' => 'Kondisi awal wajib diisi.',
            'kondisi_awal.between' => 'Kondisi awal harus antara 0 sampai 100.',

            'jumlah_unit.required_if' => 'Jumlah unit wajib diisi untuk tipe aset.',
            'jumlah_unit.min' => 'Jumlah unit minimal 1.',
            'jumlah_unit.max' => 'Jumlah unit maksimal 100.',

            'qty_total.required_if' => 'Jumlah total wajib diisi untuk tipe stok.',
            'qty_total.min' => 'Jumlah total minimal 1.',

            'jumlah_masuk.required' => 'Jumlah barang masuk wajib diisi.',
            'jumlah_masuk.min' => 'Jumlah barang masuk minimal 1.',
            'jumlah_masuk.max' => 'Jumlah barang masuk maksimal 1000.',

            'kondisi_saat_itu.required' => 'Kondisi saat itu wajib diisi.',
            'kondisi_saat_itu.between' => 'Kondisi saat itu harus antara 0 sampai 100.',

            'sumber_tujuan.max' => 'Sumber maksimal 200 karakter.',
            'tanggal_transaksi.required' => 'Tanggal transaksi wajib diisi.',
            'tanggal_transaksi.date' => 'Tanggal transaksi tidak valid.',
        ];
    }
}
