<!DOCTYPE html>
<html lang="id" class="h-full" x-data="{
    isDark: false,
    sidebarOpen: false,
    clockTime: '',
    clockDate: '',

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
        this.updateClock();

        setInterval(() => {
            this.updateClock();
        }, 1000);

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = false;
            }
        });

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
        document.documentElement.classList.toggle('dark', this.isDark);
    },

    updateClock() {
        const sekarang = new Date();

        this.clockTime = new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        }).format(sekarang);

        this.clockDate = new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(sekarang);
    },
}" x-init="init()" :class="{ dark: isDark }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ trim($__env->yieldContent('title', 'Dashboard')) }} — Website</title>
    <meta name="description" content="@yield('meta_description', 'Website Sistem Inventaris Laboratorium RPL SMKN 9 Malang')">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-100 font-sans antialiased"
    :class="{ 'overflow-hidden lg:overflow-auto': sidebarOpen }">
    {{-- Sidebar Desktop --}}
    <x-sidebar :mobile="false" />

    {{-- Sidebar Mobile Drawer --}}
    <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-50 lg:hidden" aria-modal="true" role="dialog">
        {{-- Overlay --}}
        <div class="absolute inset-0 bg-gray-900/60" @click="sidebarOpen = false" x-show="sidebarOpen"
            x-transition:enter="transition linear duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition linear duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        {{-- Drawer --}}
        <div class="absolute inset-y-0 left-0" x-show="sidebarOpen"
            x-transition:enter="transition ease-in-out duration-200 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full">
            <x-sidebar :mobile="true" />
        </div>
    </div>

    <div class="lg:pl-60 flex min-h-screen flex-col">
        <x-topbar />

        <main class="flex-1 p-3 lg:p-5 animate-page-enter">
            {{-- Flash Toast --}}
            <div class="fixed top-4 right-4 z-[100] space-y-2">
                @if (session('sukses'))
                    <div x-data="{ tampil: true }" x-cloak x-show="tampil" x-init="setTimeout(() => tampil = false, 3500)"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-4"
                        class="animate-slide-in-right flex max-w-xs items-start gap-2.5 rounded-lg border border-emerald-200 bg-white px-3.5 py-2.5 shadow-lg dark:border-emerald-900/40 dark:bg-gray-800">
                        <i class="bi bi-check-circle-fill mt-0.5 text-emerald-500"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400">Berhasil</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ session('sukses') }}</p>
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
                        class="animate-slide-in-right flex max-w-xs items-start gap-2.5 rounded-lg border border-red-200 bg-white px-3.5 py-2.5 shadow-lg dark:border-red-900/40 dark:bg-gray-800">
                        <i class="bi bi-exclamation-circle-fill mt-0.5 text-red-500"></i>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-red-700 dark:text-red-400">Terjadi Kesalahan</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300">{{ session('galat') }}</p>
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
    </div>

    @stack('modals')
    @stack('scripts')
</body>

</html>
