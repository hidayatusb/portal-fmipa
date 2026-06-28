<?php

namespace App\Livewire\Profile;

use App\Livewire\Concerns\SetsBreadcrumbs;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class Edit extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $password = '';

    public string $password_confirmation = '';

    public $profilePicture = null;

    public bool $removeProfilePicture = false;

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Profil'],
        ]);
    }

    public function removePhoto(): void
    {
        $this->reset('profilePicture');
        $this->resetValidation('profilePicture');
    }

    public function markRemoveProfilePicture(): void
    {
        $this->removeProfilePicture = true;
        $this->reset('profilePicture');
    }

    public function undoRemoveProfilePicture(): void
    {
        $this->removeProfilePicture = false;
    }

    public function save(): void
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'username' => ['required', 'string', 'min:3', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'profilePicture' => ['nullable', 'image', 'max:2048'],
        ];

        if ($this->password !== '') {
            $rules['currentPassword'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $this->validate($rules, [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'currentPassword.required' => 'Password saat ini wajib diisi untuk mengganti password.',
            'currentPassword.current_password' => 'Password saat ini tidak sesuai.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'profilePicture.image' => 'Foto profil harus berupa gambar.',
            'profilePicture.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
        ];

        if ($this->password !== '') {
            $data['password'] = $this->password;
        }

        if ($this->removeProfilePicture && $user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $data['profile_picture'] = null;
        } elseif ($this->profilePicture) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $data['profile_picture'] = $this->profilePicture->store('profile-pictures/'.$user->id, 'public');
        }

        $user->update($data);

        $this->reset(['profilePicture', 'currentPassword', 'password', 'password_confirmation', 'removeProfilePicture']);
        $this->resetValidation();

        session()->flash('success', 'Profil berhasil diperbarui.');

        $this->dispatch('profile-updated');
    }

    public function render(): View
    {
        return view('livewire.profile.edit', [
            'user' => Auth::user(),
        ]);
    }
}
