<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

new #[Layout('layouts::login')] class extends Component {
    #[Validate('required|min:3')]
    public $username = '';

    #[Validate('required|min:3')]
    public $password = '';

    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi!',
            'password.required' => 'Password wajib diisi!',
        ];
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->regenerate();
            session()->flash('success', 'Login berhasil!');
            return redirect()->intended('/dashboard');
        }
        session()->flash('error', 'Login gagal! Pastikan username dan password benar.');
        return back();
    }
};
?>

<div>



    <form wire:submit.prevent="login" class="kt-card-content flex flex-col gap-5 p-10" id="sign_in_form">
        <div class="text-center mb-2.5">
            <h3 class="text-lg font-medium text-mono leading-none mb-2.5">
                Sign in
            </h3>
            <div class="flex items-center justify-center font-medium">
                <span class="text-sm text-secondary-foreground me-1.5">
                    Need an account?
                </span>
                <a class="text-sm link" href="/metronic/tailwind/demo1/authentication/classic/sign-up/">
                    Sign up
                </a>
            </div>
        </div>


        <div class="kt-form-item">
            <label class="kt-form-label">Username:</label>
            <div class="kt-input">
                <input type="text" class="kt-input" placeholder="Masukkan Username" aria-invalid="true"
                    wire:model="username" />

            </div>
            <div class="kt-form-description">Enter your username to proceed.</div>

            @error('username')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>
        <div class="kt-form-item">
            <label class="kt-form-label">Password:</label>
            <div class="kt-input" data-kt-toggle-password="true">
                <input type="password" class="kt-input" placeholder="Password" aria-invalid="true"
                     wire:model="password" />
                <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                    data-kt-toggle-password-trigger="true" type="button">
                    <span class="kt-toggle-password-active:hidden">
                        <i class="ki-filled ki-eye text-muted-foreground"></i>
                    </span>
                    <span class="hidden kt-toggle-password-active:block">
                        <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                    </span>
                </button>
            </div>
            <div class="kt-form-description">Enter your password to proceed.</div>

            @error('password')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>
        <label class="kt-label">
            <input class="kt-checkbox kt-checkbox-sm" name="check" type="checkbox" value="1" />
            <span class="kt-checkbox-label">
                Remember me
            </span>
        </label>
       <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow" wire:loading.attr="disabled">
            <span wire:loading.remove>Sign In</span>
            <span wire:loading>Processing...</span>
        </button>
    </form>

    
</div>
