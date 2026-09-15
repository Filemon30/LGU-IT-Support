@props([
    'id' => 'modal-form',
    'title' => 'Modal',
    'icon' => null,
    'width' => 'max-w-2xl',
])

<div
    id="{{ $id }}"
    data-modal
    {{ $attributes }}
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
>
    <div
        class="flex w-full {{ $width }} max-h-[calc(100vh-1.5rem)] sm:max-h-[calc(100vh-2rem)]
               flex-col overflow-visible rounded-2xl bg-white shadow-xl"
    >

        {{-- Header --}}
        <div
            class="relative flex shrink-0
                   border-b border-gray-200 px-5 py-4 sm:px-6 sm:py-5"
        >

            {{-- Title --}}
            <div class="flex items-center justify-center gap-2 pr-10">

                {{-- Optional Icon --}}
                @if ($icon)
                    <i class="{{ $icon }} text-lg text-gray-900"></i>
                @endif

                {{-- Centered Title --}}
                <h2 class="text-sm font-semibold text-gray-900 text-center">
                    {{ $title }}
                </h2>

            </div>

            {{-- Close Button --}}
            <button
                type="button"
                onclick="if(typeof closeModal === 'function') closeModal('{{ $id }}');"
                class="absolute right-3 top-1/2 -translate-y-1/2
                       flex h-9 w-9 items-center justify-center
                       rounded-lg text-gray-400
                       transition
                       hover:bg-gray-100 hover:text-gray-700
                       sm:right-5"
                aria-label="Close"
            >
                <i class="ti ti-x text-xl"></i>
            </button>

        </div>

        {{-- Scrollable Content --}}
        <div
            class="min-h-0 flex-1 overflow-y-auto overscroll-contain
                   px-4 py-5 sm:p-6"
        >
            {{ $slot }}
        </div>

    </div>
</div>