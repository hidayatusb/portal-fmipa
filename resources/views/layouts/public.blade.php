<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="id">
<head>
    @include('layouts.partials.head')
    @livewireStyles
</head>
<body class="antialiased flex min-h-full flex-col text-base text-foreground bg-background">
    <script>
        const defaultThemeMode = 'light';
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches
                    ? 'dark'
                    : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>

    <header class="border-b border-border bg-background">
        <div class="mx-auto flex max-w-3xl items-center justify-between gap-4 px-5 py-4">
            <a href="{{ route('login') }}" class="flex items-center gap-2.5">
                <img class="h-8 dark:hidden" src="{{ asset('assets/media/app/logo-portal.png') }}"
                    alt="Portal FMIPA" />
                <img class="hidden h-8 dark:block" src="{{ asset('assets/media/app/logo-portal-dark.png') }}"
                    alt="Portal FMIPA" />
            </a>
            <a href="{{ route('login') }}" class="kt-btn kt-btn-sm kt-btn-outline">
                Masuk
            </a>
        </div>
    </header>

    <main class="mx-auto w-full max-w-3xl grow px-5 py-8 lg:py-12">
        {{ $slot }}
    </main>

    <footer class="border-t border-border">
        <div class="mx-auto flex max-w-3xl flex-wrap items-center justify-between gap-3 px-5 py-5 text-xs text-secondary-foreground">
            <span>&copy; {{ date('Y') }} Portal FMIPA</span>
            <a href="{{ route('privacy') }}" class="kt-link">Kebijakan Privasi</a>
        </div>
    </footer>

    <livewire:toast />
    <script>
        document.addEventListener('livewire:navigated', () => {
            KTComponents.init();
        });
    </script>
    @include('layouts.partials.scripts')
    @livewireScripts
</body>
</html>
