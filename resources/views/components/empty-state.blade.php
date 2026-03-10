@props([
    'icon' => 'bi-inbox',
    'title' => 'Belum ada data',
    'message' => 'Data yang Anda cari belum tersedia.',
])

<div
    {{ $attributes->merge([
        'class' =>
            'rounded-lg border border-dashed border-gray-300 bg-white px-4 py-8 text-center dark:border-gray-700 dark:bg-gray-800',
    ]) }}>
    <div
        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-300">
        <i class="bi {{ $icon }} text-lg"></i>
    </div>

    <h3 class="mt-3 text-sm font-semibold text-gray-800 dark:text-gray-100">
        {{ $title }}
    </h3>

    <p class="mx-auto mt-1 max-w-sm text-sm text-gray-500 dark:text-gray-400">
        {{ $message }}
    </p>

    @if (trim($slot))
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
</div>
