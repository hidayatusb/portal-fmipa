@props([
    'dueDate',
    'createdAt',
])

<div
    wire:ignore
    x-data="assignmentCountdown(@js($dueDate->getTimestamp() * 1000), @js($createdAt->getTimestamp() * 1000))"
    {{ $attributes }}
>
    {{ $slot }}
</div>
