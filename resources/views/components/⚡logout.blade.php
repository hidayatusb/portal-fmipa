<?php

use Livewire\Component;

new class extends Component {
    
    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
};
?>

<div>
    <button wire:click="logout" class="kt-btn kt-btn-outline w-full justify-center">
        Log out
    </button>
</div>
