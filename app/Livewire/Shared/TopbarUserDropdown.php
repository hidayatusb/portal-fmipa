<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TopbarUserDropdown extends Component
{
    public function refreshUser(): void
    {
        Auth::user()?->refresh();
    }

    #[On('profile-updated')]
    public function onProfileUpdated(): void
    {
        $this->refreshUser();
    }

    public function render()
    {
        return view('livewire.shared.topbar-user-dropdown', [
            'user' => Auth::user(),
        ]);
    }
}
