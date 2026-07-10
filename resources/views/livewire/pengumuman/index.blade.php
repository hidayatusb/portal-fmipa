<div>
    <div class="kt-container-fixed">
        <div class="flex flex-wrap items-center justify-between gap-5 pb-7.5 lg:items-end">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-mono">
                    Pengumuman
                </h1>
                <p class="text-sm font-normal text-secondary-foreground">
                    Informasi dan pengumuman resmi dari fakultas
                </p>
            </div>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('admin.pengumuman.index') }}" class="kt-btn kt-btn-outline" wire:navigate>
                    <i class="ki-filled ki-setting-2"></i>
                    Kelola
                </a>
            @endif
        </div>
    </div>

    <div class="kt-container-fixed">
        @if ($announcements->isEmpty())
            <div class="kt-card">
                <div class="kt-card-content flex flex-col items-center gap-3 py-12 text-center">
                    <i class="ki-filled ki-notification-status text-4xl text-muted-foreground"></i>
                    <p class="text-sm text-secondary-foreground">Belum ada pengumuman.</p>
                </div>
            </div>
        @else
            <div class="flex flex-col gap-5">
                @foreach ($announcements as $announcement)
                    <article class="kt-card" wire:key="pengumuman-{{ $announcement->id }}">
                        @if ($announcement->hasImage())
                            <img src="{{ $announcement->imageUrl() }}" alt="{{ $announcement->title }}"
                                class="max-h-80 w-full rounded-t-xl object-cover" />
                        @endif
                        <div class="kt-card-content flex flex-col gap-3 p-5">
                            <div class="flex flex-col gap-1">
                                <h2 class="text-lg font-semibold text-mono">{{ $announcement->title }}</h2>
                                <p class="text-xs text-muted-foreground">
                                    {{ optional($announcement->published_at)->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') }}
                                    @if ($announcement->author)
                                        · {{ $announcement->author->name }}
                                    @endif
                                </p>
                            </div>
                            <p class="whitespace-pre-line text-sm leading-relaxed text-secondary-foreground">
                                {{ $announcement->body }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</div>
