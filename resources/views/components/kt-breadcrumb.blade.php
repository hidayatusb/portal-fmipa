@props(['items' => [], 'class' => 'mb-4'])

<ol {{ $attributes->merge(['class' => 'kt-breadcrumb '.$class]) }}>
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <li class="kt-breadcrumb-separator">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-right" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"></path>
                </svg>
            </li>
        @endif
        <li class="kt-breadcrumb-item">
            @if (! empty($item['url']))
                <a href="{{ $item['url'] }}" class="kt-breadcrumb-link"
                    @if ($item['wire'] ?? true) wire:navigate @endif>
                    {{ $item['label'] }}
                </a>
            @else
                <span class="kt-breadcrumb-page">{{ $item['label'] }}</span>
            @endif
        </li>
    @endforeach
</ol>
