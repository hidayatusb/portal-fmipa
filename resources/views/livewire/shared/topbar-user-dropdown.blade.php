<!-- User -->
<div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-offset-rtl="-20px, 10px"
    data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click"
    wire:key="topbar-user-{{ $user?->id }}-{{ $user?->updated_at?->timestamp }}">
    <div class="shrink-0 cursor-pointer" data-kt-dropdown-toggle="true">
        @if ($user?->hasProfilePicture())
            <img alt="{{ $user->name }}" class="size-9 shrink-0 rounded-full border-2 border-green-500 object-cover"
                src="{{ $user->profilePictureUrl() }}" />
        @else
            <span
                class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border-2 border-green-500 bg-primary/10 text-sm font-semibold text-primary">
                {{ $user?->initials() }}
            </span>
        @endif
    </div>
    <div class="kt-dropdown-menu w-[250px] hidden" data-kt-dropdown-menu="true">
        <div class="flex min-w-0 items-center justify-between gap-1.5 px-2.5 py-1.5">
            <div class="flex min-w-0 flex-1 items-center gap-2">
                @if ($user?->hasProfilePicture())
                    <img alt="{{ $user->name }}" class="size-9 shrink-0 rounded-full border-2 border-green-500 object-cover"
                        src="{{ $user->profilePictureUrl() }}" />
                @else
                    <span
                        class="inline-flex size-9 shrink-0 items-center justify-center rounded-full border-2 border-green-500 bg-primary/10 text-sm font-semibold text-primary">
                        {{ $user->initials() }}
                    </span>
                @endif
                <div class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold leading-none text-foreground"
                        title="{{ $user?->name }}">
                        {{ $user?->name }}
                    </span>
                    <span class="mt-1 block truncate text-xs font-medium leading-none text-secondary-foreground"
                        title="{{ $user?->username }}">
                        {{ $user?->username }}
                    </span>
                </div>
            </div>
          
        </div>
        <ul class="kt-dropdown-menu-sub">
            <li>
                <div class="kt-dropdown-menu-separator"></div>
            </li>
            <li>
                <a class="kt-dropdown-menu-link" href="{{ route('profile.edit') }}" wire:navigate>
                    <i class="ki-filled ki-profile-circle"></i>
                    Profil Saya
                </a>
            </li>
            <li>
                <div class="kt-dropdown-menu-separator"></div>
            </li>
        </ul>
        <div class="mb-2.5 flex flex-col gap-3.5 px-2.5 pt-1.5">
            <div class="flex items-center justify-between gap-2">
                <span class="flex items-center gap-2">
                    <i class="ki-filled ki-moon text-base text-muted-foreground"></i>
                    <span class="text-2sm font-medium">Dark Mode</span>
                </span>
                <input class="kt-switch" data-kt-theme-switch-state="dark" data-kt-theme-switch-toggle="true"
                    name="check" type="checkbox" value="1" />
            </div>
            <livewire:logout />
        </div>
    </div>
</div>
<!-- End of User -->
