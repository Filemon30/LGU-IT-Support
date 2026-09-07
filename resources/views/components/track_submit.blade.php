@props([
    'href' => null,
])

<a
    href="{{ $href ?? route('submit.request') }}"
    {{ $attributes->merge([
        'class' => '
            submit-button
            w-full
            mt-3
            flex
            items-center
            justify-center
            gap-2
            rounded-xl
            text-sm
            font-semibold
            text-white
        ',
    ]) }}
>
    {{ $slot }}
</a>
