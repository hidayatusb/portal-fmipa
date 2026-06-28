<?php

namespace App\Livewire\Demo1;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Sidebar extends Component
{
    public function render(): View
    {
        return view('livewire.demo1.sidebar', [
            'user' => Auth::user(),
        ]);
    }
}
