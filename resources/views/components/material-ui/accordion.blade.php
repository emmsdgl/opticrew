{{--
    Accordion Component (Alpine.js)

    Usage:
    <x-material-ui.accordion type="single" collapsible>
        <x-material-ui.accordion-item value="item-1" title="Section 1">
            Content here...
        </x-material-ui.accordion-item>
    </x-material-ui.accordion>
--}}
@props([
    'type' => 'single',       // 'single' or 'multiple'
    'collapsible' => true,
    'defaultOpen' => null,     // string or array of values to open by default
])

@php
    $defaultArr = is_array($defaultOpen) ? $defaultOpen : ($defaultOpen ? [$defaultOpen] : []);
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}
     x-data="{
         type: '{{ $type }}',
         collapsible: {{ $collapsible ? 'true' : 'false' }},
         openItems: {{ json_encode($defaultArr) }},

         isOpen(val) {
             return this.openItems.includes(val);
         },

         toggle(val) {
             const isOpen = this.openItems.includes(val);
             if (this.type === 'single') {
                 if (isOpen) {
                     this.openItems = this.collapsible ? [] : [val];
                 } else {
                     this.openItems = [val];
                 }
             } else {
                 if (isOpen) {
                     this.openItems = this.openItems.filter(v => v !== val);
                 } else {
                     this.openItems = [...this.openItems, val];
                 }
             }
         }
     }">
    {{ $slot }}
</div>

@once
<style>
.accordion-icon-plus,
.accordion-icon-minus {
    position: absolute;
    transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
}
.accordion-icon-plus.is-open {
    opacity: 0;
    transform: rotate(90deg);
}
.accordion-icon-plus.is-closed {
    opacity: 1;
    transform: rotate(0deg);
}
.accordion-icon-minus.is-open {
    opacity: 1;
    transform: rotate(0deg);
}
.accordion-icon-minus.is-closed {
    opacity: 0;
    transform: rotate(-90deg);
}
</style>
@endonce
