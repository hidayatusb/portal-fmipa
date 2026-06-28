<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TopbarNotificationDropdown extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('notifications-updated')]
    public function refreshCount(): void
    {
        $this->unreadCount = Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    public function render()
    {
        return view('livewire.shared.topbar-notification-dropdown');
    }
}
