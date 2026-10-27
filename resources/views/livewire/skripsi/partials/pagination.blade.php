@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="kt-datatable-pagination">
        <button type="button"
            class="kt-datatable-pagination-button kt-datatable-pagination-prev{{ $paginator->onFirstPage() ? ' disabled' : '' }}"
            @disabled($paginator->onFirstPage())
            @if (! $paginator->onFirstPage()) wire:click="previousPage" wire:loading.attr="disabled" @endif
            aria-label="Previous">
            <svg class="size-3.5 shrink-0 rtl:rotate-180" width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M15.05 19.47L8.53003 12.95C8.24003 12.66 8.24003 12.19 8.53003 11.9L15.05 5.38"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        @foreach ($elements as $element)
            @if (is_string($element))
                <button type="button" class="kt-datatable-pagination-button kt-datatable-pagination-more" disabled>
                    ...
                </button>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <button type="button"
                        class="kt-datatable-pagination-button{{ $page == $paginator->currentPage() ? ' active disabled' : '' }}"
                        @disabled($page == $paginator->currentPage())
                        @if ($page != $paginator->currentPage()) wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" @endif
                        aria-label="Page {{ $page }}"
                        @if ($page == $paginator->currentPage()) aria-current="page" @endif>
                        {{ $page }}
                    </button>
                @endforeach
            @endif
        @endforeach

        <button type="button"
            class="kt-datatable-pagination-button kt-datatable-pagination-next{{ $paginator->hasMorePages() ? '' : ' disabled' }}"
            @disabled(! $paginator->hasMorePages())
            @if ($paginator->hasMorePages()) wire:click="nextPage" wire:loading.attr="disabled" @endif
            aria-label="Next">
            <svg class="size-3.5 shrink-0 rtl:rotate-180" width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M8.95001 5.38L15.47 11.9C15.76 12.19 15.76 12.66 15.47 12.95L8.95001 19.47"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
    </nav>
@endif
