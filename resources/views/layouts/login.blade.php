
<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
<head>
    @include('layouts.partials.head')
    @livewireStyles
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background">
   <script>
    const defaultThemeMode = 'light'; // light|dark|system
    let themeMode;

    if (document.documentElement) {
        if (localStorage.getItem('kt-theme')) {
            themeMode = localStorage.getItem('kt-theme');
        } else if (
        document.documentElement.hasAttribute('data-kt-theme-mode')
        ) {
            themeMode =
            document.documentElement.getAttribute('data-kt-theme-mode');
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
    <style>
    .page-bg {
        background-image: url('https://keenthemes.com/metronic/tailwind/dist/assets/media/images/2600x1200/bg-10.png');
    }

    .dark .page-bg {
        background-image: url('https://keenthemes.com/metronic/tailwind/dist/assets/media/images/2600x1200/bg-10-dark.png');
    }
    </style>
     <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
        <div class="kt-card max-w-[370px] w-full">
            {{ $slot }}
        </div>
    </div>

    <livewire:toast />
    <script>
        document.addEventListener('livewire:navigated', () => {

            KTComponents.init();

        });
    </script>

  
    @livewireScripts
</body>
</html>
