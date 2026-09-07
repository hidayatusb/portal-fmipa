@props([
    'id' => 'trix_'.uniqid(),
])

@php
    $model = $attributes->wire('model')->value();
@endphp

<div
    wire:ignore
    class="trix-livewire-wrapper"
    x-data="{
        value: @entangle($model),
        setEditorContent() {
            this.$nextTick(() => {
                if (this.$refs.editor?.editor) {
                    this.$refs.editor.editor.loadHTML(this.value || '')
                }
            })
        },
    }"
    x-init="setEditorContent(); $watch('value', () => {
        if (document.activeElement !== this.$refs.editor) {
            setEditorContent()
        }
    })"
    x-on:trix-change="value = $event.target.value"
>
    <input
        type="hidden"
        id="{{ $id }}_input"
        x-bind:value="value"
    />

    <trix-toolbar id="{{ $id }}_toolbar"></trix-toolbar>

    <trix-editor
        x-ref="editor"
        id="{{ $id }}"
        class="trix-content {{ $attributes->get('class') }}"
        toolbar="{{ $id }}_toolbar"
        input="{{ $id }}_input"
    ></trix-editor>
</div>
