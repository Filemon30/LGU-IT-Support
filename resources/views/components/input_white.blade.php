@props([
    'name',
    'type' => 'text',
    'placeholder' => '',
    'label' => null,
    'sublabel' => null,
    'icon' => null,
    'editable' => true,
    'error' => null,
])

@php
    $hasError = !empty($error);
@endphp

<div>

    {{-- Label --}}
    @if($label || $sublabel)
        <div class="inline-flex mb-1">

            @if($label)
                <h1 class="text-xs font-semibold {{ $hasError ? 'text-red-600' : '' }}">
                    {{ $label }}
                </h1>
            @endif

            @if($sublabel)
                <h1 class="text-xs text-gray-400 ml-1">
                    {{ $sublabel }}
                </h1>
            @endif

        </div>
    @endif

    {{-- Input --}}
    <div class="relative">

        {{-- Icon --}}
        @if($icon)
            <div
                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"
            >
                <i class="{{ $icon }}"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            value="{{ $attributes->get('value', '') }}"

            @if(!$editable)
                readonly
            @endif

            {{ $attributes->merge([
                'class' => '
                    w-full
                    h-9
                    rounded-lg
                    border
                    bg-white
                    px-3
                    py-2.5
                    text-sm
                    font-regular
                    text-gray-900
                    placeholder-gray-400
                    outline-none
                    transition
                    focus:ring-2
                    focus:ring-gray-100
                    ' . ($hasError ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-gray-400') . '
                    ' . ($icon ? 'pl-10' : '') . '
                    ' . (!$editable ? 'bg-gray-50 text-gray-500 cursor-default' : '') . '
                '
            ]) }}
        >

    </div>

    {{-- Error Message --}}
    @if($hasError)
        <p class="mt-1 text-xs text-red-500">{{ $error }}</p>
    @endif

</div>
