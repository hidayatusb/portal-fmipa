<!-- Sidebar -->
<div class="kt-sidebar fixed bottom-0 top-0 z-20 hidden h-screen shrink-0 flex-col items-stretch border-e border-e-border bg-background [--kt-drawer-enable:true] lg:flex lg:[--kt-drawer-enable:false]"
    data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0" id="sidebar">
    <div class="kt-sidebar-header relative hidden shrink-0 items-center justify-between px-3 lg:flex lg:px-6"
        id="sidebar_header">
        <div class="kt-sidebar-logo min-w-0">
            <a class="dark:hidden" href="{{ route('dashboard.index') }}">
                <img class="default-logo min-h-[22px] max-w-none" style="height: 30px;"
                    src="{{ asset('assets/media/app/logo-portal.png') }}" />
                <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/usb.png') }}" style="height: 30px;" />
            </a>
            <a class="hidden dark:block" href="{{ route('dashboard.index') }}">
                <img class="default-logo min-h-[22px] max-w-none"
                    src="{{ asset('assets/media/app/logo-portal-dark.png') }}" style="height: 30px;" />
                <img class="small-logo min-h-[22px] max-w-none" src="{{ asset('assets/media/app/usb.png') }}" style="height: 30px;" />
            </a>
        </div>
        <button
            class="kt-btn kt-btn-outline kt-btn-icon absolute start-full top-2/4 z-40 size-[30px] -translate-x-2/4 -translate-y-2/4 rtl:translate-x-2/4"
            data-kt-toggle="body" data-kt-toggle-class="kt-sidebar-collapse" id="sidebar_toggle">
            <i
                class="ki-filled ki-black-left-line kt-toggle-active:rotate-180 rtl:translate rtl:kt-toggle-active:rotate-0 transition-all duration-300 rtl:rotate-180">
            </i>
        </button>
    </div>
    <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
        <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3" data-kt-scrollable="true"
            data-kt-scrollable-dependencies="#sidebar_header" data-kt-scrollable-height="auto"
            data-kt-scrollable-offset="0px" data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
            <div class="kt-menu flex grow flex-col gap-1" data-kt-menu="true" data-kt-menu-accordion-expand-all="false"
                id="sidebar_menu">
                <div class="kt-menu-item pt-2.25 pb-px">
                    <span
                        class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                        Menu
                    </span>
                </div>
                <div class="kt-menu-item">
                    <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('dashboard.*') ? 'kt-menu-item-active' : '' }}"
                        href="{{ route('dashboard.index') }}" wire:navigate tabindex="0">
                        <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                            <i class="ki-filled ki-element-11 text-lg"></i>
                        </span>
                        <span
                            class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                            Dashboard
                        </span>
                    </a>
                </div>

                @if ($user?->isDosen())
                    <div class="kt-menu-item pt-2.25 pb-px">
                        <span
                            class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                            E-Learning
                        </span>
                    </div>
                    <div class="kt-menu-item">
                        <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('dosen.elearning.*') ? 'kt-menu-item-active' : '' }}"
                            href="{{ route('dosen.elearning.index') }}" wire:navigate tabindex="0">
                            <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                <i class="ki-filled ki-book-open text-lg"></i>
                            </span>
                            <span
                                class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                Mata Kuliah
                            </span>
                        </a>
                    </div>
                @endif

                @if ($user?->isMahasiswa())
                    <div class="kt-menu-item pt-2.25 pb-px">
                        <span
                            class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                            E-Learning
                        </span>
                    </div>
                    <div class="kt-menu-item">
                        <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('mahasiswa.elearning.*') ? 'kt-menu-item-active' : '' }}"
                            href="{{ route('mahasiswa.elearning.index') }}" wire:navigate tabindex="0">
                            <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                <i class="ki-filled ki-book-open text-lg"></i>
                            </span>
                            <span
                                class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                Mata Kuliah Saya
                            </span>
                        </a>
                    </div>
                @endif

                @if ($user?->isAdmin())
                    <div class="kt-menu-item pt-2.25 pb-px">
                        <span
                            class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                            Administrasi
                        </span>
                    </div>
                    <div class="kt-menu-item">
                        <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('admin.user-approvals.*') ? 'kt-menu-item-active' : '' }}"
                            href="{{ route('admin.user-approvals.index') }}" wire:navigate tabindex="0">
                            <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                <i class="ki-filled ki-people text-lg"></i>
                            </span>
                            <span
                                class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                Review Akun
                            </span>
                        </a>
                    </div>
                    <div class="kt-menu-item">
                        <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('admin.pengumuman.*') ? 'kt-menu-item-active' : '' }}"
                            href="{{ route('admin.pengumuman.index') }}" wire:navigate tabindex="0">
                            <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                <i class="ki-filled ki-notification-status text-lg"></i>
                            </span>
                            <span
                                class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                Pengumuman
                            </span>
                        </a>
                    </div>
                @endif

                @if ($user)
                    <div class="kt-menu-item pt-2.25 pb-px">
                        <span
                            class="kt-menu-heading pe-[10px] ps-[10px] text-xs font-medium uppercase text-muted-foreground">
                            Akun
                        </span>
                    </div>
                    <div class="kt-menu-item">
                        <a class="kt-menu-link kt-menu-item-active:bg-accent/60 dark:menu-item-active:border-border kt-menu-item-active:rounded-lg hover:bg-accent/60 grow items-center gap-[10px] border border-transparent py-[6px] pe-[10px] ps-[10px] hover:rounded-lg {{ request()->routeIs('profile.*') ? 'kt-menu-item-active' : '' }}"
                            href="{{ route('profile.edit') }}" wire:navigate tabindex="0">
                            <span class="kt-menu-icon w-[20px] items-start text-muted-foreground">
                                <i class="ki-filled ki-profile-circle text-lg"></i>
                            </span>
                            <span
                                class="kt-menu-title kt-menu-item-active:text-primary kt-menu-item-active:font-semibold kt-menu-link-hover:!text-primary text-sm font-medium text-foreground">
                                Profil
                            </span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
