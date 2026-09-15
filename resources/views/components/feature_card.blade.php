@props([
    'icon' => 'ti ti-ticket',
    'title' => 'Feature',
    'description' => 'Add a helpful description for this feature.',
    'color' => 'blue',
])

@php
$colors = [
    'blue' => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-200',
        'icon' => 'text-blue-600',
    ],
    'orange' => [
        'bg' => 'bg-orange-50',
        'border' => 'border-orange-200',
        'icon' => 'text-orange-600',
    ],
    'green' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-200',
        'icon' => 'text-green-600',
    ],
    'purple' => [
        'bg' => 'bg-purple-50',
        'border' => 'border-purple-200',
        'icon' => 'text-purple-600',
    ],
];

$theme = $colors[$color] ?? $colors['blue'];
@endphp

<div class="feature-card">
    <div class="feature-card-top">
        <div class="feature-icon {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['icon'] }}">
            <i class="{{ $icon }}"></i>
        </div>
        <h3>{{ $title }}</h3>
    </div>
    <p>{{ $description }}</p>
</div>