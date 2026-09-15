@props([
    'label' => '',
    'placeholder' => '',

    // Form integration
    'name' => null,
    'value' => null,
    'autocomplete' => null,

    // Optional icons
    'leftIcon' => null,
    'rightIcon' => null,

    // Colors
    'backgroundColor' => '#0f172a',
    'strokeColor' => null,
    'focusColor' => '#635BFF',

    'iconColor' => '#64748b',
    'iconFocusColor' => null,

    // Input
    'type' => 'text',

    // Validation
    'error' => null,
])

@php
    $iconFocusColor = $iconFocusColor ?? $focusColor;

    // Default stroke color if none is provided
    $strokeColor = $strokeColor ?? 'rgba(255, 255, 255, 0.12)';

    $inputClass = 'floating-input';

    if ($leftIcon) {
        $inputClass .= ' has-left-icon';
    }

    if ($rightIcon) {
        $inputClass .= ' has-right-icon';
    }

    if (!is_null($value) && $value !== '') {
        $inputClass .= ' has-value';
    }
@endphp

<div
    {{ $attributes->merge([
        'class' => $inputClass, 'h-9',
        'style' => "
            --input-bg: {$backgroundColor};
            --input-stroke: {$strokeColor};
            --input-focus: {$focusColor};
            --input-icon: {$iconColor};
            --input-icon-focus: {$iconFocusColor};
        ",
    ]) }}
>

    {{-- Input --}}
    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        @if ($name)
            name="{{ $name }}"
        @endif
        @if (!is_null($value))
            value="{{ $value }}"
        @endif
        @if ($autocomplete)
            autocomplete="{{ $autocomplete }}"
        @endif
    >

    {{-- Floating Label --}}
    @if ($label)
        <label>
            {{ $label }}
        </label>
    @endif

    {{-- Left Icon --}}
    @if ($leftIcon)
        <span class="input-icon input-icon-left">
            <i class="{{ $leftIcon }}"></i>
        </span>
    @endif

    {{-- Right Icon --}}
    @if ($rightIcon)
        <span class="input-icon input-icon-right">
            <i class="{{ $rightIcon }}"></i>
        </span>
    @endif

    {{-- Validation Error --}}
    @if($error)
        <span class="input-error-message">
            <i class="ti ti-alert-circle"></i>

            <span class="error-text">
                {{ $error }}
            </span>
        </span>
    @endif

</div>