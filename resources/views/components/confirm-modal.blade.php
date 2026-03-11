@props([
    'name',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'bg-red-500 hover:bg-red-600 text-white',
    'icon' => 'bi-exclamation-triangle-fill',
])

<div x-cloak x-show="{{ $name }}" x-transition.opacity.duration.200ms class="fixed inset-0 z-[95] overflow-y-auto"
    aria-modal="true" role="dialog" @keydown.escape.window="{{ $name }} = false">
    <div class="fixed inset-0 bg-gray-900/60" @click="{{ $name }} = false"></div>

    <div class="relative flex min-h-screen items-center justify-center p-4">
        <div x-show="{{ $name }}" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800"
            @click.stop>
            <div class="p-4">
                <div class="flex items-start gap-3">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400">
                        <i class="bi {{ $icon }} text-base"></i>
                    </div>

                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $title }}
                        </h3>
                        <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                            {{ $message }}
                        </p>
                    </div>
                </div>

                @if (trim($slot) !== '')
                    <div class="mt-4">
                        {{ $slot }}
                    </div>
                @endif

                <div class="mt-4 flex justify-end gap-2">
                    <button type="button"
                        class="rounded-md bg-gray-100 px-3 py-1.5 text-xs text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        @click="{{ $name }} = false">
                        {{ $cancelText }}
                    </button>

                    @isset($footer)
                        {{ $footer }}
                    @else
                        <button type="button"
                            class="rounded-md px-3 py-1.5 text-xs transition-colors {{ $confirmClass }}">
                            {{ $confirmText }}
                        </button>
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
