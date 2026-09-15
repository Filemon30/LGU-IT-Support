@props([
    'type' => 'info',
    'title' => null,
    'description' => null,
    'icon' => null,
])

@php
    $config = match($type) {
        'success' => [
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'icon_color' => 'text-emerald-600',
            'title_color' => 'text-emerald-800',
            'desc_color' => 'text-emerald-700',
            'default_icon' => 'ti ti-circle-check-filled',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'icon_color' => 'text-red-600',
            'title_color' => 'text-red-800',
            'desc_color' => 'text-red-700',
            'default_icon' => 'ti ti-circle-x-filled',
        ],
        'warning' => [
            'bg' => 'bg-amber-50',
            'border' => 'border-amber-200',
            'icon_color' => 'text-amber-600',
            'title_color' => 'text-amber-800',
            'desc_color' => 'text-amber-700',
            'default_icon' => 'ti ti-alert-triangle-filled',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'icon_color' => 'text-blue-600',
            'title_color' => 'text-blue-800',
            'desc_color' => 'text-blue-700',
            'default_icon' => 'ti ti-info-circle-filled',
        ],
        default => [
            'bg' => 'bg-gray-50',
            'border' => 'border-gray-200',
            'icon_color' => 'text-gray-600',
            'title_color' => 'text-gray-800',
            'desc_color' => 'text-gray-700',
            'default_icon' => 'ti ti-info-circle-filled',
        ],
    };

    $finalIcon = $icon ?? $config['default_icon'];
@endphp

<div
    {{ $attributes->merge([
        'class' => '
            flex items-start gap-3
            rounded-lg border p-4
            ' . $config['bg'] . ' ' . $config['border'] . '
        ',
    ]) }}
>
    <i class="{{ $finalIcon }} text-xl mt-0.5 shrink-0 {{ $config['icon_color'] }}"></i>

    <div class="flex-1 min-w-0">
        @if ($title)
            <h5 class="text-sm font-semibold {{ $config['title_color'] }}">
                {{ $title }}
            </h5>
        @endif

        @if ($description)
            <p class="text-xs mt-1 {{ $config['desc_color'] }}">
                {{ $description }}
            </p>
        @endif

        @if ($slot->isNotEmpty())
            <div class="text-xs mt-1 {{ $config['desc_color'] }}">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
