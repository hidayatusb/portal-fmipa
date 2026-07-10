@if ($announcements->isNotEmpty())
    <div class="kt-card {{ $class ?? 'mt-7.5' }}">
        <div class="kt-card-header">
            <h3 class="kt-card-title">Pengumuman</h3>
        </div>
        <div class="kt-card-content flex flex-col gap-5 p-5">
            @foreach ($announcements as $announcement)
                <article class="flex flex-col gap-3 border-b border-border pb-5 last:border-b-0 last:pb-0"
                    wire:key="dash-announcement-{{ $announcement->id }}">
                    @if ($announcement->hasImage())
                        <img src="{{ $announcement->imageUrl() }}" alt="{{ $announcement->title }}"
                            class="max-h-64 w-full rounded-lg border border-border object-cover" />
                    @endif
                    <div class="flex flex-col gap-1.5">
                        <h4 class="text-base font-semibold text-mono">{{ $announcement->title }}</h4>
                        @if ($announcement->isUrlContent())
                            <a href="{{ $announcement->body }}" target="_blank" rel="noopener"
                                class="text-sm text-primary break-all">
                                {{ $announcement->body }}
                            </a>
                        @else
                            <p class="whitespace-pre-line text-sm text-secondary-foreground">{{ $announcement->body }}</p>
                        @endif
                        <span class="text-xs text-muted-foreground">
                            {{ optional($announcement->published_at)->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endif
