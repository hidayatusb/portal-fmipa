<div wire:key="topbar-notification-{{ $unreadCount }}">
    <button type="button"
        class="kt-btn kt-btn-ghost kt-btn-icon hover:bg-primary/10 hover:[&_i]:text-primary relative size-9 overflow-visible rounded-full p-0"
        data-kt-drawer-toggle="#notifications_drawer" aria-label="Notifikasi">
        <div class="kt-avatar size-9 overflow-visible">
            <div class="kt-avatar-fallback size-9 border-0 bg-transparent text-inherit">
                <i class="ki-filled ki-notification-status text-lg"></i>
            </div>
            @if ($unreadCount > 0)
                <div class="kt-avatar-indicator -end-2 -top-2">
                    <span
                        class="kt-badge kt-badge-xs kt-badge-primary rounded-full border border-background">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                </div>
            @endif
        </div>
    </button>
</div>
