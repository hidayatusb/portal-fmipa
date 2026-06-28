<div>
    <button
        class="kt-btn kt-btn-ghost kt-btn-icon hover:bg-primary/10 hover:[&_i]:text-primary relative size-9 rounded-full"
        data-kt-drawer-toggle="#notifications_drawer">
        <i class="ki-filled ki-notification-status text-lg"></i>
        @if ($unreadCount > 0)
            <span
                class="absolute end-0.5 top-0.5 inline-flex min-h-[18px] min-w-[18px] items-center justify-center rounded-full bg-destructive px-1 text-[10px] font-semibold leading-none text-destructive-foreground">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
</div>
