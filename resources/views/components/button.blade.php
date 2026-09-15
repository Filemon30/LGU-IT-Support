@props([
    'type' => 'button',
    'color' => 'blue',
    'icon' => null,
    'iconPosition' => 'left',
    'href' => null,
    'size' => 'md',
])

@php

    $sizes = [
        'sm' => 'h-8 py-1.5 pl-3 text-xs',
        'md' => 'h-9 p-2.5 text-sm',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $colors = [

        // Strong / Filled
        'd-blue' => [
            'bg' => 'bg-[#071f45]',
            'hover' => 'hover:bg-[#061a3a]',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-[#071f45]/30',
        ],

        'blue' => [
            'bg' => 'bg-blue-600',
            'hover' => 'hover:bg-blue-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-blue-200',
        ],

        'green' => [
            'bg' => 'bg-green-600',
            'hover' => 'hover:bg-green-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-green-200',
        ],

        'red' => [
            'bg' => 'bg-red-600',
            'hover' => 'hover:bg-red-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-red-200',
        ],

        'yellow' => [
            'bg' => 'bg-yellow-500',
            'hover' => 'hover:bg-yellow-600',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-yellow-200',
        ],

        'orange' => [
            'bg' => 'bg-orange-600',
            'hover' => 'hover:bg-orange-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-orange-200',
        ],

        'purple' => [
            'bg' => 'bg-purple-600',
            'hover' => 'hover:bg-purple-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-purple-200',
        ],

        'gray' => [
            'bg' => 'bg-gray-600',
            'hover' => 'hover:bg-gray-700',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-gray-200',
        ],

        'dark' => [
            'bg' => 'bg-gray-900',
            'hover' => 'hover:bg-gray-800',
            'text' => 'text-white',
            'border' => 'border-transparent',
            'ring' => 'focus:ring-gray-300',
        ],

        'white' => [
            'bg' => 'bg-white',
            'hover' => 'hover:bg-gray-50',
            'text' => 'text-gray-700',
            'border' => 'border-gray-200',
            'ring' => 'focus:ring-gray-200',
        ],


        // Subtle / Outline
        'outline-blue' => [
            'bg' => 'bg-blue-50',
            'hover' => 'hover:bg-blue-100',
            'text' => 'text-blue-700',
            'border' => 'border-blue-200',
            'ring' => 'focus:ring-blue-100',
        ],


        'outline-green' => [
            'bg' => 'bg-green-50',
            'hover' => 'hover:bg-green-100',
            'text' => 'text-green-700',
            'border' => 'border-green-200',
            'ring' => 'focus:ring-green-100',
        ],

        'outline-red' => [
            'bg' => 'bg-red-50',
            'hover' => 'hover:bg-red-100',
            'text' => 'text-red-700',
            'border' => 'border-red-200',
            'ring' => 'focus:ring-red-100',
        ],

        'outline-yellow' => [
            'bg' => 'bg-yellow-50',
            'hover' => 'hover:bg-yellow-100',
            'text' => 'text-yellow-700',
            'border' => 'border-yellow-200',
            'ring' => 'focus:ring-yellow-100',
        ],

        'outline-orange' => [
            'bg' => 'bg-orange-50',
            'hover' => 'hover:bg-orange-100',
            'text' => 'text-orange-700',
            'border' => 'border-orange-200',
            'ring' => 'focus:ring-orange-100',
        ],

        'outline-purple' => [
            'bg' => 'bg-purple-50',
            'hover' => 'hover:bg-purple-100',
            'text' => 'text-purple-700',
            'border' => 'border-purple-200',
            'ring' => 'focus:ring-purple-100',
        ],

        'outline-gray' => [
            'bg' => 'bg-gray-50',
            'hover' => 'hover:bg-gray-100',
            'text' => 'text-gray-700',
            'border' => 'border-gray-200',
            'ring' => 'focus:ring-gray-100',
        ],

        'outline-dark' => [
            'bg' => 'bg-gray-50',
            'hover' => 'hover:bg-gray-100',
            'text' => 'text-gray-800',
            'border' => 'border-gray-300',
            'ring' => 'focus:ring-gray-200',
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['blue'];

    $iconPosition = in_array($iconPosition, ['left', 'right'])
        ? $iconPosition
        : 'left';

    $tag = $href ? 'a' : 'button';

    $class = "
        inline-flex
        items-center
        justify-center
        gap-2
        rounded-lg
        border
        px-4
        h-9
        text-sm
        font-medium
        transition-all
        duration-200
        ease-in-out
        cursor-pointer
        lg:inline-flex 
        lg:w-auto 
        text-xs

        {$selectedColor['bg']}
        {$selectedColor['hover']}
        {$selectedColor['text']}
        {$selectedColor['border']}

        focus:outline-none
        focus:ring-2
        {$selectedColor['ring']}

        hover:shadow-sm
        active:shadow-none

        disabled:pointer-events-none
        disabled:opacity-50
        disabled:cursor-not-allowed
    ";
@endphp

<{{ $tag }}
    @if($href)
        href="{{ $href }}"
    @else
        type="{{ $type }}"
    @endif

    {{ $attributes->merge([
        'class' => $class,
    ]) }}
>

    {{-- Left Icon --}}
    @if($icon && $iconPosition === 'left')
        <i class="{{ $icon }}"></i>
    @endif

    {{-- Button Text --}}
    <span>
        {{ $slot }}
    </span>

    {{-- Right Icon --}}
    @if($icon && $iconPosition === 'right')
        <i class="{{ $icon }}"></i>
    @endif

</{{ $tag }}>