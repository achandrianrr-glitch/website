@props([
    'kondisi' => 100,
    'showValue' => false,
])

@php
    $nilai = max(0, min(100, (int) $kondisi));

    $label = match (true) {
        $nilai >= 80 => 'Baik',
        $nilai >= 60 => 'Lumayan',
        $nilai >= 35 => 'Rusak',
        default => 'Rusak Parah',
    };

    $class = match (true) {
        $nilai >= 80
            => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400',
        $nilai >= 60 => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-900/20 dark:text-blue-400',
        $nilai >= 35 => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400',
        default => 'bg-red-50 text-red-600 ring-red-500/20 dark:bg-red-900/20 dark:text-red-400',
    };
@endphp

<span
    {{ $attributes->merge([
        'class' => "inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[10px] font-medium ring-1 {$class}",
    ]) }}>
    <span>{{ $label }}</span>

    @if ($showValue)
        <span>{{ $nilai }}%</span>
    @endif
</span>
