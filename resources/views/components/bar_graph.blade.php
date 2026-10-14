@props([
    'label' => 'Bar Graph',
    'items' => [],
])

@php
    $total = collect($items)->sum('count');
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col h-full']) }}>
    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ $label }}</h3>

    <div class="flex flex-col h-full justify-between gap-4">
        @forelse ($items as $item)
            @php
                $percentage = $total > 0 ? ($item['count'] / $total) * 100 : 0;
            @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-medium text-gray-600">{{ $item['label'] }}</span>
                    <span @if(!empty($item['id'])) id="{{ $item['id'] }}" @endif class="text-xs font-bold" style="color: {{ $item['color'] }};">{{ $item['count'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div
                        class="h-3 rounded-full transition-all duration-500"
                        style="width: {{ $percentage }}%; background-color: {{ $item['color'] }};"
                    ></div>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center">No data available</p>
        @endforelse
    </div>
</div>
