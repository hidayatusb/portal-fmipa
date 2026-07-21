<?php

namespace App\Livewire\Shared;

use App\Models\User;
use App\Support\NotificationLink;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationsDrawer extends Component
{
    #[On('notifications-updated')]
    public function refreshNotifications(): void
    {
        //
    }

    public function open(string $notificationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();

        if (! $notification) {
            return;
        }

        if ($notification->unread()) {
            $notification->markAsRead();
            $this->dispatch('notifications-updated');
        }

        $url = NotificationLink::resolve($notification);

        if ($url) {
            $this->redirect($url, navigate: true);
        }
    }

    public function markAsRead(string $notificationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $notification = $user->notifications()->whereKey($notificationId)->first();

        if ($notification && $notification->unread()) {
            $notification->markAsRead();
            $this->dispatch('notifications-updated');
        }
    }

    public function markAllAsRead(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->dispatch('notifications-updated');
    }

    public function render(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        /** @var Collection<int, \Illuminate\Notifications\DatabaseNotification> $notifications */
        $notifications = $user
            ? $user->notifications()->latest()->limit(30)->get()
            : collect();

        return view('livewire.shared.notifications-drawer', [
            'notifications' => $notifications,
            'unreadCount' => $user?->unreadNotifications()->count() ?? 0,
        ]);
    }
}
