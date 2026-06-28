<!-- Header -->
<header class="kt-header fixed end-0 start-0 top-0 z-10 flex shrink-0 items-stretch bg-background" data-kt-sticky="true"
    data-kt-sticky-class="border-b border-border" data-kt-sticky-name="header" id="header">
    <!-- Container -->
    <div class="kt-container-fixed flex items-center justify-between gap-4" id="headerContainer">
        <!-- Mobile Logo -->
        <div class="-ms-1 flex items-center gap-2.5 lg:hidden">
            <a class="shrink-0" href="html/demo1.html">
                <img class="max-h-[25px] w-full" src="{{ asset('assets/media/app/mini-logo.svg') }}" />
            </a>
            <div class="flex items-center">
                <button class="kt-btn kt-btn-icon kt-btn-ghost" data-kt-drawer-toggle="#sidebar">
                    <i class="ki-filled ki-menu">
                    </i>
                </button>
               
            </div>
        </div>
        <!-- End of Mobile Logo -->

        <div class="flex min-w-0 flex-1 items-center">
            <x-header-breadcrumb-nav :items="$breadcrumbs ?? []" class="mb-0 min-w-0" />
        </div>

        <!-- Topbar -->
        <div class="flex items-center gap-2.5">
       
            <livewire:shared.topbar-notification-dropdown />

            <livewire:shared.topbar-user-dropdown />
        </div>
        <!-- End of Topbar -->
    </div>
    <!-- End of Container -->
</header>
<!-- End of Header -->
