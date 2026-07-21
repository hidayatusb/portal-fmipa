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

        <div class="kt-card mt-5 border-destructive/30">
            <div class="kt-card-header">
                <h3 class="kt-card-title text-destructive">Zona Berbahaya</h3>
            </div>
            <div class="kt-card-content flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-mono">Hapus Akun</p>
                    <p class="mt-1 text-sm text-secondary-foreground">
                        Menghapus akun bersifat permanen. Semua data terkait
                        @if ($user->isDosen())
                            (kelas, materi, tugas, dan nilai)
                        @elseif ($user->isMahasiswa())
                            (pendaftaran kelas dan pengumpulan tugas)
                        @endif
                        akan ikut terhapus.
                    </p>
                </div>
                <button type="button" class="kt-btn kt-btn-destructive shrink-0"
                    data-kt-modal-toggle="#delete_account_modal" wire:click="resetDeleteAccountForm">
                    <i class="ki-filled ki-trash"></i>
                    Hapus Akun
                </button>
            </div>
        </div>
    </div>

    <div class="kt-modal kt-modal-center" data-kt-modal="true" id="delete_account_modal" wire:ignore.self>
        <div class="kt-modal-content max-w-[400px] w-full">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">Hapus Akun</h3>
                <button type="button" class="kt-modal-close" aria-label="Close modal"
                    data-kt-modal-dismiss="#delete_account_modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x" aria-hidden="true">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <form wire:submit.prevent="deleteAccount">
                <div class="kt-modal-body flex flex-col gap-4">
                    <div class="kt-alert kt-alert-warning flex items-start gap-2">
                        <i class="ki-filled ki-information-2 mt-0.5"></i>
                        <div class="text-sm">
                            Anda akan menghapus akun <span class="font-semibold text-mono">{{ $user->username }}</span>.
                            Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-mono" for="deleteAccountPassword">
                            Masukkan password Anda untuk konfirmasi
                        </label>
                        <input id="deleteAccountPassword" type="password" class="kt-input"
                            wire:model="deleteAccountPassword" autocomplete="current-password"
                            placeholder="Password akun Anda" />
                        @error('deleteAccountPassword')
                            <span class="text-xs text-destructive">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="kt-modal-footer gap-2.5">
                    <button type="button" class="kt-btn kt-btn-outline" data-kt-modal-dismiss="#delete_account_modal"
                        wire:click="resetDeleteAccountForm">
                        Batal
                    </button>
                    <button type="submit" class="kt-btn kt-btn-destructive" wire:loading.attr="disabled"
                        wire:target="deleteAccount">
                        <span wire:loading.remove wire:target="deleteAccount">Hapus Akun</span>
                        <span wire:loading wire:target="deleteAccount">Menghapus...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
