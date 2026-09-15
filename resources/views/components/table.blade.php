@props([
    'columns' => [],
    'actions' => false,
    'maxHeight' => '400px',
    'minWidth' => '700px',
])

<div
    {{ $attributes->merge([
        'class' => '
            w-full
            max-w-full
            overflow-x-auto
            rounded-lg
            bg-transparent
        ',
    ]) }}
>
    <div class="overflow-y-auto" style="max-height: {{ $maxHeight }};">
        <table class="w-full text-left text-sm" @if($minWidth !== '0') style="min-width: {{ $minWidth }}" @else style="table-layout: fixed" @endif>

            <thead class="sticky top-0 bg-white z-10">
                @isset($header)
                    {{ $header }}
                @else
                    <tr class="border-b border-gray-200 bg-white">
                        @foreach ($columns as $column)
                            <th class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-500">
                                {{ $column }}
                            </th>
                        @endforeach

                        @if ($actions)
                            <th style="width: 100px" class="whitespace-nowrap px-4 py-3 text-xs font-bold uppercase tracking-wide text-gray-500">
                                Actions
                            </th>
                        @endif
                    </tr>
                @endisset
            </thead>

            <tbody class="font-semibold text-xs">
                {{ $body }}
            </tbody>

        </table>
    </div>
</div>
