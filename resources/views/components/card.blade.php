@props([
'label' => null,
'color' => 'white',
'border' => true,
])

@php
$colors = [
'white' => [
'bg' => 'bg-white',
'border' => 'border-gray-200',
'text' => 'text-gray-800',
],

    'blue' => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'text' => 'text-blue-800',
    ],

    'd-blue' => [
        'bg' => 'bg-[#020d1d]',
        'border' => 'border-[#0f2745]',
        'text' => 'text-white',
    ],

    'green' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-200',
        'text' => 'text-green-800',
    ],

    'red' => [
        'bg' => 'bg-red-50',
        'border' => 'border-red-200',
        'text' => 'text-red-800',
    ],

    'yellow' => [
        'bg' => 'bg-yellow-50',
        'border' => 'border-yellow-200',
        'text' => 'text-yellow-800',
    ],

    'orange' => [
        'bg' => 'bg-orange-50',
        'border' => 'border-orange-200',
        'text' => 'text-orange-800',
    ],

    'gray' => [
        'bg' => 'bg-gray-50',
        'border' => 'border-gray-200',
        'text' => 'text-gray-800',
    ],

    'dark' => [
        'bg' => 'bg-gray-50',
        'border' => 'border-gray-300',
        'text' => 'text-gray-600',
    ],
];

$theme = $colors[$color] ?? $colors['white'];

$classes = implode(' ', [
    'flex',
    'h-full',
    'flex-col',
    'gap-4',
    $theme['bg'],
    $border ? 'border ' . $theme['border'] : '',
    'rounded-lg',
    'p-4',
    'shadow-sm',
]);

@endphp

<div
    {{ $attributes->merge([
        'class' => $classes,
    ]) }}
>

@if($label || isset($actions))
    <div class="flex items-center justify-between gap-3">

        @if($label)
            <p class="text-xs font-bold {{ $theme['text'] }}">
                {!! $label !!}
            </p>
        @endif

        @isset($actions)
            <div class="flex items-center gap-2">
                {{ $actions }}
            </div>
        @endisset

    </div>
@endif

<div class="{{ $theme['text'] }}">
    {{ $slot }}
</div>

</div>
