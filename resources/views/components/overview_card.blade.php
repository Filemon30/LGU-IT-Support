@props([
    'icon' => 'ti ti-ticket',
    'label' => 'Label',
    'total' => 0,
    'color' => 'blue',
    'span' => null,
    'id' => null,
])

@php
    $colors = [
        'blue' => [
            'bg' => 'rgba(59, 130, 246, 0.10)',
            'text' => '#60a5fa',
        ],

        'd-blue' => [
            'bg' => 'rgba(37, 99, 235, 0.10)',
            'text' => '#071f45',
        ],

        'green' => [
            'bg' => 'rgba(34, 197, 94, 0.10)',
            'text' => '#4ade80',
        ],

        'yellow' => [
            'bg' => 'rgba(234, 179, 8, 0.10)',
            'text' => '#facc15',
        ],

        'red' => [
            'bg' => 'rgba(239, 68, 68, 0.10)',
            'text' => '#f87171',
        ],

        'cyan' => [
            'bg' => 'rgba(6, 182, 212, 0.10)',
            'text' => '#22d3ee',
        ],

        'purple' => [
            'bg' => 'rgba(168, 85, 247, 0.10)',
            'text' => '#c084fc',
        ],

        'gray' => [
            'bg' => 'rgba(107, 114, 128, 0.10)',
            'text' => '#9ca3af',
        ],

        'orange' => [
            'bg' => 'rgba(249, 115, 22, 0.10)',
            'text' => '#fb923c',
        ],
    ];

    $selectedColor = $colors[$color] ?? $colors['blue'];
@endphp

<div
    {{ $attributes->merge([
        'class' => '
            flex
            items-center
            justify-between
            gap-4
            bg-white
            rounded-lg
            p-4
            h-25
            shadow-sm
            border-l-4
        ',
    ]) }}
    style="border-left-color: {{ $selectedColor['text'] }};"
>
    <div class="space-y-1">
        <p class="text-xs font-semibold text-gray-600">
            {{ $label }}
        </p>

        <p @if($id) id="{{ $id }}" @endif class="text-xl font-extrabold text-gray-900">
            {{ $total }}
        </p>

        <a href="#" class="text-[0.65rem] font-regular text-[#60a5fa]">
            {{$span}}
        </a>
    </div>

    <span
        class="
            flex
            h-10
            w-10
            shrink-0
            items-center
            justify-center
            rounded-4xl
        "
        style="background-color: {{ $selectedColor['bg'] }};"
    >
        <i
            class="{{ $icon }} text-lg"
            style="color: {{ $selectedColor['text'] }};"
        ></i>
    </span>
</div>