@props(['items' => [], 'class' => 'mb-4'])

<div
    x-data="{
        items: @js($items),
        setItems(newItems) { this.items = newItems ?? []; },
    }"
    x-on:breadcrumbs-updated.window="setItems($event.detail.items ?? $event.detail[0]?.items ?? [])"
    class="min-w-0"
>
    <ol x-show="items.length > 0" x-cloak {{ $attributes->merge(['class' => 'kt-breadcrumb '.$class]) }}>
        <template x-for="(item, index) in items" :key="index">
            <li class="kt-breadcrumb-item">
                <span class="kt-breadcrumb-separator" x-show="index > 0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-chevron-right" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg>
                </span>
                <template x-if="item.url && item.wire !== false">
                    <a :href="item.url" class="kt-breadcrumb-link" wire:navigate x-text="item.label"></a>
                </template>
                <template x-if="item.url && item.wire === false">
                    <a :href="item.url" class="kt-breadcrumb-link" x-text="item.label"></a>
                </template>
                <template x-if="!item.url">
                    <span class="kt-breadcrumb-page" x-text="item.label"></span>
                </template>
            </li>
        </template>
    </ol>
</div>
