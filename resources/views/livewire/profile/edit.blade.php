<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Profil Saya
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Kelola informasi akun Anda
                </p>
            </div>
        </div>
    </div>

    <div class="kt-container-fixed">
        @if (session('success'))
            <div class="kt-alert kt-alert-success mb-5 flex items-center gap-2">
                <i class="ki-filled ki-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="save" class="grid gap-5 lg:grid-cols-3">
            <div class="kt-card lg:col-span-1">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Foto Profil</h3>
                </div>
                <div class="kt-card-content flex flex-col items-center gap-4">
                    @if ($profilePicture)
                        <img src="{{ $profilePicture->temporaryUrl() }}" alt="Preview foto profil"
                            class="size-28 rounded-full border-2 border-border object-cover" />
                    @elseif ($removeProfilePicture || ! $user->hasProfilePicture())
                        <span
                            class="inline-flex size-28 items-center justify-center rounded-full bg-primary/10 text-3xl font-semibold text-primary px-7 py-5">
                            {{ $user->initials() }}
                        </span>
                    @else
                        <img src="{{ $user->profilePictureUrl() }}" alt="{{ $user->name }}"
                            class="size-28 rounded-full border-2 border-border object-cover" />
                    @endif

                    <div class="flex w-full flex-col gap-2">
                        <input type="file" class="kt-input" wire:model="profilePicture" accept="image/*" />
                        @error('profilePicture')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($profilePicture)
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline w-full" wire:click="removePhoto">
                            Batal Upload
                        </button>
                    @elseif ($user->hasProfilePicture() && ! $removeProfilePicture)
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline w-full"
                            wire:click="markRemoveProfilePicture">
                            Hapus Foto
                        </button>
                    @elseif ($removeProfilePicture)
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline w-full"
                            wire:click="undoRemoveProfilePicture">
                            Batalkan Hapus
                        </button>
                    @endif

                    <div class="w-full border-t border-border pt-4 text-center">
                        <p class="truncate text-sm font-medium text-mono" title="{{ $user->name }}">{{ $user->name }}</p>
                        <p class="text-xs text-secondary-foreground">{{ $user->username }}</p>
                        <span class="kt-badge kt-badge-sm kt-badge-outline mt-2">{{ $user->role->label() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-5 lg:col-span-2">
                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Informasi Akun</h3>
                    </div>
                    <div class="kt-card-content flex flex-col gap-5">
                        <div class="grid gap-5 lg:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="name">Nama Lengkap</label>
                                <input id="name" type="text" class="kt-input" wire:model="name" />
                                @error('name')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="username">Username</label>
                                <input id="username" type="text" class="kt-input" wire:model="username" />
                                @error('username')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="email">Email</label>
                            <input id="email" type="email" class="kt-input" wire:model="email" />
                            @error('email')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <h3 class="kt-card-title">Ubah Password</h3>
                    </div>
                    <div class="kt-card-content flex flex-col gap-5">
                        <p class="text-sm text-secondary-foreground">
                            Kosongkan jika tidak ingin mengubah password.
                        </p>
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-mono" for="currentPassword">Password Saat Ini</label>
                            <input id="currentPassword" type="password" class="kt-input" wire:model="currentPassword"
                                autocomplete="current-password" />
                            @error('currentPassword')
                                <span class="text-xs text-destructive">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid gap-5 lg:grid-cols-2">
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="password">Password Baru</label>
                                <input id="password" type="password" class="kt-input" wire:model="password"
                                    autocomplete="new-password" />
                                @error('password')
                                    <span class="text-xs text-destructive">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-medium text-mono" for="password_confirmation">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" type="password" class="kt-input"
                                    wire:model="password_confirmation" autocomplete="new-password" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <button type="submit" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-check"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
