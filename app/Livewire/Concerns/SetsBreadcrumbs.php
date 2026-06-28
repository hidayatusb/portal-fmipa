<?php

namespace App\Livewire\Concerns;

trait SetsBreadcrumbs
{
    /** @var list<array{label: string, url?: string, wire?: bool}> */
    protected array $pageBreadcrumbs = [];

    protected function setBreadcrumbs(array $items): void
    {
        $this->pageBreadcrumbs = $items;

        $this->dispatch('breadcrumbs-updated', items: $items);
    }

    /** @return list<array{label: string, url?: string, wire?: bool}> */
    public function breadcrumbsForLayout(): array
    {
        return $this->pageBreadcrumbs;
    }
}
