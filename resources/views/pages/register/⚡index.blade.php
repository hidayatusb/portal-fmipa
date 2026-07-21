<?php

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::login')] class extends Component {
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $role = 'mahasiswa';

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'NIM / NIDN / NIP wajib diisi.',
            'username.unique' => 'NIM / NIDN / NIP sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ];
    }

    public function register()
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([UserRole::Dosen->value, UserRole::Mahasiswa->value])],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'approval_status' => UserApprovalStatus::Pending,
        ]);

        session()->flash('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan admin.');

        return redirect()->route('login');
    }
};
?>

<div>
    <form wire:submit.prevent="register" class="kt-card-content flex flex-col gap-5 p-10" id="sign_up_form">
        <div class="text-center mb-2.5">
            <h3 class="text-lg font-medium text-mono leading-none mb-2.5">
                Daftar Akun
            </h3>
            <div class="flex items-center justify-center font-medium">
                <span class="text-sm text-secondary-foreground me-1.5">
                    Sudah punya akun?
                </span>
                <a class="text-sm kt-link" href="{{ route('login') }}" wire:navigate>
                    Masuk
                </a>
            </div>
            <p class="text-center text-xs text-secondary-foreground">
                Akun baru akan ditinjau admin sebelum dapat digunakan.
            </p>
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="name">Nama Lengkap</label>
            <div class="kt-input">
                <input id="name" type="text" class="kt-input" placeholder="Masukkan nama lengkap"
                    wire:model="name" />
            </div>
            @error('name')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="username">NIM / NIDN / NIP</label>
            <div class="kt-input">
                <input id="username" type="text" class="kt-input" placeholder="Masukkan NIM / NIDN / NIP"
                    wire:model="username" />
            </div>
            @error('username')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="email">Email</label>
            <div class="kt-input">
                <input id="email" type="email" class="kt-input" placeholder="nama@email.com"
                    wire:model="email" />
            </div>
            @error('email')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="role">Role</label>
            <select id="role" class="kt-select" wire:model="role">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="dosen">Dosen</option>
            </select>
            @error('role')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="password">Password</label>
            <div class="kt-input" data-kt-toggle-password="true">
                <input id="password" type="password" class="kt-input" placeholder="Minimal 8 karakter"
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
            @error('password')
                <div class="kt-form-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="kt-form-item">
            <label class="kt-form-label" for="password_confirmation">Konfirmasi Password</label>
            <div class="kt-input" data-kt-toggle-password="true">
                <input id="password_confirmation" type="password" class="kt-input"
                    placeholder="Ulangi password" wire:model="password_confirmation" />
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
        </div>

        <button type="submit" class="kt-btn kt-btn-primary flex justify-center grow" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">Daftar</span>
            <span wire:loading wire:target="register">Memproses...</span>
        </button>
    </form>

    <div class="px-10 pb-8 text-center">
        <a href="{{ route('privacy') }}" class="text-xs text-secondary-foreground hover:text-primary" wire:navigate>
            Kebijakan Privasi
        </a>
    </div>
</div>
