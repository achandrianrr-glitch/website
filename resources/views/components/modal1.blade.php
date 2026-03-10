@props(['name', 'title' => null, 'maxWidth' => 'max-w-lg', 'closeOnOverlay' => true])

<div x-cloak x-show="{{ $name }}" class="fixed inset-0 z-[90] overflow-y-auto" aria-modal="true" role="dialog"
    @keydown.escape.window="{{ $name }} = false">
    <div class="fixed inset-0 bg-gray-900/60"
        @if ($closeOnOverlay) @click="{{ $name }} = false" @endif></div>

    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div x-show="{{ $name }}" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full {{ $maxWidth }} rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
            @click.stop>
            @if ($title || isset($header))
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <div class="min-w-0">
                        @isset($header)
                            {{ $header }}
                        @else
                            <h3 class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ $title }}
                            </h3>
                        @endisset
                    </div>

                    <button type="button"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200"
                        @click="{{ $name }} = false" aria-label="Tutup modal" title="Tutup">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
            @endif

            <div class="p-4">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
