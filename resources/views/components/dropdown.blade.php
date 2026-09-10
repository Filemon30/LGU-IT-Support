@props([
    'name' => null,
    'options' => [],
    'placeholder' => 'Select an option',
    'selected' => null,
    'label' => null,
    'sublabel' => null,
    'size' => 'md',

    // Colors
    'backgroundColor' => '#ffffff',
    'borderColor' => '#e5e7eb',
    'focusColor' => '#2c51ec',
    'textColor' => '#1f2937',
    'placeholderColor' => '#9ca3af',
    'hoverColor' => '#eef4ff',
    'iconColor' => '#6b7280',
])

@php
    $sizes = [
        'sm' => 'h-8 py-1.5 pl-3 text-xs',
        'md' => 'h-9 p-2.5 text-sm',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $normalized = [];

    foreach ($options as $value => $text) {
        $normalized[] = [
            'value' => (string) $value,
            'label' => $text,
        ];
    }

    $selectedText = null;

    foreach ($normalized as $option) {
        if ((string) $option['value'] === (string) $selected) {
            $selectedText = $option['label'];
            break;
        }
    }
@endphp

<div
    style="
        --dd-bg: {{ $backgroundColor }};
        --dd-border: {{ $borderColor }};
        --dd-focus: {{ $focusColor }};
        --dd-text: {{ $textColor }};
        --dd-placeholder: {{ $placeholderColor }};
        --dd-hover: {{ $hoverColor }};
        --dd-icon: {{ $iconColor }};
    "
>

    {{-- Label --}}
    @if($label || $sublabel)
        <div class="inline-flex mb-1">
            @if($label)
                <h1 class="text-xs font-semibold" style="color: {{ $textColor }}">
                    {{ $label }}
                </h1>
            @endif

            @if($sublabel)
                <h1 class="text-xs ml-1" style="color: {{ $placeholderColor }}">
                    {{ $sublabel }}
                </h1>
            @endif
        </div>
    @endif

    {{-- Dropdown --}}
    <details
        data-dropdown
        {{ $attributes->merge([
            'class' => '
                group
                relative
                h-fit
                w-full
            ',
        ]) }}
    >
        <input
            type="hidden"
            data-dropdown-input
            name="{{ $name }}"
            value="{{ $selected ?? '' }}"
        />

        <summary
            class="
                flex
                w-full
                items-center
                justify-between
                gap-2
                cursor-pointer
                select-none
                list-none
                rounded-lg
                border
                pr-3
                outline-none
                transition
                focus:ring-2
                [&::-webkit-details-marker]:hidden
                {{ $sizeClass }}
            "
            style="
                border-color: var(--dd-border);
                background-color: var(--dd-bg);
                color: var(--dd-text);
                --tw-ring-color: var(--dd-focus);
            "
            onfocus="this.style.borderColor='var(--dd-focus)'"
            onblur="this.style.borderColor='var(--dd-border)'"
        >
            <span
                data-dropdown-label
                class="truncate {{ $selectedText ? '' : '' }}"
                style="color: {{ $selectedText ? $textColor : $placeholderColor }}"
            >
                {{ $selectedText ?? $placeholder }}
            </span>

            <i
                class="
                    ti
                    ti-chevron-down
                    pointer-events-none
                    shrink-0
                    transition-transform
                    duration-200
                    group-open:rotate-180
                "
                style="color: var(--dd-icon)"
            ></i>
        </summary>

        <div
            class="
                absolute
                right-0
                top-full
                z-20
                mt-1.5
                min-w-full
                w-fit
                max-h-60
                overflow-y-auto
                rounded-lg
                border
                shadow-lg
            "
            style="
                border-color: var(--dd-border);
                background-color: var(--dd-bg);
            "
        >

            <div
                class="
                    absolute
                    -top-[5px]
                    right-3
                    h-2.5
                    w-2.5
                    rotate-45
                    rounded-[2px]
                    border-l
                    border-t
                "
                style="
                    border-color: var(--dd-border);
                    background-color: var(--dd-bg);
                "
            ></div>

            @if ($placeholder !== false)
                <button
                    type="button"
                    data-dropdown-option
                    data-value=""
                    data-label="{{ $placeholder }}"
                    class="
                        flex
                        w-full
                        items-center
                        whitespace-nowrap
                        pr-3
                        text-left
                        transition-colors
                        {{ $selectedText ? '' : '' }}
                        {{ $sizeClass }}
                    "
                    style="
                        color: {{ $placeholderColor }};
                        {{ !$selectedText ? "background-color: var(--dd-hover);" : '' }}
                    "
                    onmouseenter="this.style.backgroundColor='var(--dd-hover)'"
                    onmouseleave="this.style.backgroundColor='{{ !$selectedText ? 'var(--dd-hover)' : 'transparent' }}'"
                >
                    {{ $placeholder }}
                </button>
            @endif

            @foreach ($normalized as $option)
                <button
                    type="button"
                    data-dropdown-option
                    data-value="{{ $option['value'] }}"
                    data-label="{{ $option['label'] }}"
                    class="
                        flex
                        w-full
                        items-center
                        whitespace-nowrap
                        pr-3
                        text-left
                        transition-colors
                        {{ $sizeClass }}
                    "
                    style="
                        color: var(--dd-text);
                        {{ (string) $option['value'] === (string) $selected ? 'background-color: var(--dd-hover);' : '' }}
                    "
                    onmouseenter="this.style.backgroundColor='var(--dd-hover)'"
                    onmouseleave="this.style.backgroundColor='{{ (string) $option['value'] === (string) $selected ? 'var(--dd-hover)' : 'transparent' }}'"
                >
                    {{ $option['label'] }}
                </button>
            @endforeach

        </div>

    </details>

</div>

<script>
    if (!window.__xDropdownInit) {
        window.__xDropdownInit = true;

        window.xDropdownSelect = function (root, value) {

            var option = root.querySelector(
                '[data-dropdown-option][data-value="' + value + '"]'
            );

            if (!option) {
                return;
            }

            var input = root.querySelector('[data-dropdown-input]');
            var label = root.querySelector('[data-dropdown-label]');

            if (input) {
                input.value = option.dataset.value;
            }

            if (label) {
                label.textContent = option.dataset.label;
                label.style.color = root.style.getPropertyValue('--dd-text');
            }

            root.querySelectorAll('[data-dropdown-option]').forEach(function (item) {
                const isSelected = item === option;
                item.style.backgroundColor = isSelected
                    ? root.style.getPropertyValue('--dd-hover')
                    : 'transparent';
            });

            root.removeAttribute('open');

            root.dispatchEvent(
                new CustomEvent('dropdown-change', {
                    bubbles: true
                })
            );
        };

        document.addEventListener('click', function (event) {

            var option = event.target.closest(
                '[data-dropdown-option]'
            );

            if (option) {

                var root = option.closest(
                    'details[data-dropdown]'
                );

                window.xDropdownSelect(
                    root,
                    option.dataset.value
                );

                return;
            }

            document
                .querySelectorAll('details[data-dropdown][open]')
                .forEach(function (root) {

                    if (!root.contains(event.target)) {
                        root.removeAttribute('open');
                    }

                });
        });
    }
</script>
