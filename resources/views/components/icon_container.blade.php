@props([
    'icon' => null,
    'color' => 'blue',
])

@php
    $colors = [
        'd-blue' => [
            'bg' => 'bg-[#071f45]/15',
            'text' => 'text-[#071f45]',
        ],
        'blue' => [
            'bg' => 'bg-blue-50',
            'text' => 'text-blue-700',
        ],
        'green' => [
            'bg' => 'bg-green-50',
            'text' => 'text-green-700',
        ],
        'red' => [
            'bg' => 'bg-red-50',
            'text' => 'text-red-700',
        ],
        'yellow' => [
            'bg' => 'bg-yellow-50',
            'text' => 'text-yellow-700',
        ],
        'orange' => [
            'bg' => 'bg-orange-50',
            'text' => 'text-orange-700',
        ],
        'purple' => [
            'bg' => 'bg-purple-50',
            'text' => 'text-purple-700',
        ],
        'gray' => [
            'bg' => 'bg-gray-50',
            'text' => 'text-gray-700',
        ],
        'dark' => [
            'bg' => 'bg-gray-100',
            'text' => 'text-gray-800',
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => "inline-flex items-center justify-center w-14 h-14 rounded-md {$selectedColor['bg']} {$selectedColor['text']}"]) }}>
    @if($icon)
        <i class="{{ $icon }} text-2xl"></i>
    @endif
</div>
