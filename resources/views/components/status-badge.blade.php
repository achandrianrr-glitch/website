@props([
    'status' => 'tersedia',
])

@php
    $status = strtolower((string) $status);

    [$label, $class] = match ($status) {
        'tersedia' => [
            'Tersedia',
            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400',
        ],
        'dipinjam' => [
            'Dipinjam',
            'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400',
        ],
        'rusak' => ['Rusak', 'bg-red-50 text-red-600 ring-red-500/20 dark:bg-red-900/20 dark:text-red-400'],
        'keluar' => ['Keluar', 'bg-gray-100 text-gray-600 ring-gray-400/20 dark:bg-gray-700 dark:text-gray-300'],
        'dikembalikan' => [
            'Dikembalikan',
            'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400',
        ],
        'aktif' => ['Aktif', 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/20 dark:text-blue-400'],
        'selesai' => ['Selesai', 'bg-gray-100 text-gray-600 ring-gray-400/20 dark:bg-gray-700 dark:text-gray-300'],
        'nonaktif' => ['Nonaktif', 'bg-gray-100 text-gray-600 ring-gray-400/20 dark:bg-gray-700 dark:text-gray-300'],
        default => [
            ucfirst(str_replace('_', ' ', $status)),
            'bg-gray-100 text-gray-600 ring-gray-400/20 dark:bg-gray-700 dark:text-gray-300',
        ],
    };
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium ring-1 {$class}",
    ]) }}>
    {{ $label }}
</span>
