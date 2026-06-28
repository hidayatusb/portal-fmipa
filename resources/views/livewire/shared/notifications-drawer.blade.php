<div class="flex h-full min-h-0 flex-col">
    <div class="flex items-center justify-between gap-2.5 border-b border-border px-5 py-3">
        <div class="flex items-center gap-2">
            <h3 class="text-sm font-semibold text-mono">Notifikasi</h3>
            @if ($unreadCount > 0)
                <span class="kt-badge kt-badge-sm kt-badge-primary kt-badge-outline">{{ $unreadCount }} baru</span>
            @endif
        </div>
        <div class="flex items-center gap-1">
            @if ($unreadCount > 0)
                <button type="button" class="kt-btn kt-btn-xs kt-btn-outline" wire:click="markAllAsRead">
                    Tandai dibaca
                </button>
            @endif
            <button type="button" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
    </div>

    <div class="kt-scrollable-y-auto grow" data-kt-scrollable="true">
        @if ($notifications->isEmpty())
            <div class="flex flex-col items-center gap-3 px-5 py-16 text-center">
                <i class="ki-filled ki-notification-status text-4xl text-muted-foreground"></i>
                <p class="text-sm text-secondary-foreground">Belum ada notifikasi.</p>
            </div>
        @else
            <div class="divide-y divide-border">
                @foreach ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = $notification->unread();
                    @endphp
                    <div class="px-5 py-4 transition-colors {{ $isUnread ? 'bg-primary/5' : '' }}"
                        wire:key="notification-{{ $notification->id }}">
                        <div class="flex gap-3">
                            <span
                                class="inline-flex size-10 shrink-0 items-center justify-center rounded-full {{ $isUnread ? 'bg-primary/15 text-primary' : 'bg-accent text-muted-foreground' }}">
                                <i class="ki-filled ki-clipboard text-sm"></i>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="mb-1 flex flex-wrap items-center gap-2">
                                    <p class="text-sm font-semibold text-mono">{{ $data['title'] ?? 'Notifikasi' }}</p>
                                    @if ($data['is_late'] ?? false)
                                        <span class="kt-badge kt-badge-xs kt-badge-warning kt-badge-outline">Terlambat</span>
                                    @endif
                                    @if ($isUnread)
                                        <span class="size-2 rounded-full bg-primary"></span>
                                    @endif
                                </div>
                                <p class="text-sm leading-relaxed text-secondary-foreground">
                                    {{ $data['message'] ?? '' }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $notification->created_at->locale('id')->diffForHumans() }}
                                </p>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    @if (! empty($data['url']))
                                        <a href="{{ $data['url'] }}" target="_blank" rel="noopener"
                                            class="kt-btn kt-btn-xs kt-btn-primary"
                                            wire:click="markAsRead('{{ $notification->id }}')">
                                            Lihat Jawaban
                                        </a>
                                    @endif
                                    @if ($isUnread)
                                        <button type="button" class="kt-btn kt-btn-xs kt-btn-outline"
                                            wire:click="markAsRead('{{ $notification->id }}')">
                                            Tandai dibaca
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
