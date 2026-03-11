<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{
    isDark: false,

    init() {
        const temaTersimpan = localStorage.getItem('tema');

        if (temaTersimpan === 'gelap') {
            this.isDark = true;
        } else if (temaTersimpan === 'terang') {
            this.isDark = false;
        } else {
            this.isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        }

        this.applyDark();
        window.chartInstances = window.chartInstances || [];
    },

    toggleDark() {
        this.isDark = !this.isDark;
        localStorage.setItem('tema', this.isDark ? 'gelap' : 'terang');
        this.applyDark();

        if (window.chartInstances && Array.isArray(window.chartInstances)) {
            window.chartInstances.forEach((chart) => {
                if (chart && typeof chart.update === 'function') {
                    chart.update();
                }
            });
        }
    },

    applyDark() {
        const root = document.documentElement;
        root.classList.toggle('dark', this.isDark);
        root.style.colorScheme = this.isDark ? 'dark' : 'light';
    },
}" x-init="init()" :class="{ dark: isDark }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title', 'Website')) }} — {{ config('app.name', 'Shiro') }}</title>
    <meta name="description" content="@yield('meta_description', 'Website — Sistem Inventaris Laboratorium RPL SMKN 9 Malang')">
    <meta name="color-scheme" content="light dark">

    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="h-full bg-gray-100 font-sans antialiased text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    <main class="min-h-screen animate-page-enter">
        <div class="pointer-events-none fixed right-4 top-4 z-[100] space-y-2">
            @if (session('sukses'))
                <div x-data="{ tampil: true }" x-cloak x-show="tampil" x-init="setTimeout(() => tampil = false, 3500)"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4"
                    class="pointer-events-auto flex max-w-sm items-start gap-2.5 rounded-lg border border-emerald-200 bg-white px-4 py-3 shadow-lg dark:border-emerald-900/40 dark:bg-gray-800">
                    <i class="bi bi-check-circle-fill mt-0.5 text-emerald-500"></i>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                            Berhasil
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ session('sukses') }}
                        </p>
                    </div>

                    <button type="button" class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        @click="tampil = false" aria-label="Tutup notifikasi sukses" title="Tutup">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
            @endif

            @if (session('galat'))
                <div x-data="{ tampil: true }" x-cloak x-show="tampil" x-init="setTimeout(() => tampil = false, 3500)"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-x-4"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-4"
                    class="pointer-events-auto flex max-w-sm items-start gap-2.5 rounded-lg border border-red-200 bg-white px-4 py-3 shadow-lg dark:border-red-900/40 dark:bg-gray-800">
                    <i class="bi bi-exclamation-circle-fill mt-0.5 text-red-500"></i>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-red-700 dark:text-red-400">
                            Terjadi Kesalahan
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ session('galat') }}
                        </p>
                    </div>

                    <button type="button" class="ml-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                        @click="tampil = false" aria-label="Tutup notifikasi galat" title="Tutup">
                        <i class="bi bi-x-lg text-xs"></i>
                    </button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
