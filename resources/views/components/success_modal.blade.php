@props([
    'id' => 'success-modal',
    'text' => 'Operation completed successfully!',
    'icon' => 'ti ti-circle-check',
])

<style>
    @keyframes success-scale {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes success-checkmark {
        0% { stroke-dashoffset: 100; }
        100% { stroke-dashoffset: 0; }
    }

    @keyframes success-bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    .success-icon-wrapper {
        animation: success-scale 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    .success-icon {
        animation: success-bounce 0.6s ease-in-out 0.4s;
    }

    .success-text {
        opacity: 0;
        animation: success-scale 0.4s ease forwards 0.3s;
    }

    .success-checkmark {
        stroke-dasharray: 100;
        stroke-dashoffset: 100;
        animation: success-checkmark 0.6s ease 0.5s forwards;
    }
</style>

<div
    id="{{ $id }}"
    data-modal
    data-modal-static
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-3 sm:p-4"
>
    <div
        class="flex w-full max-w-xs flex-col items-center justify-center
               rounded-2xl bg-white px-8 py-10 shadow-xl"
    >

        {{-- Success Icon --}}
        <div class="success-icon-wrapper mb-5">
            <div class="success-icon relative flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <i class="{{ $icon }} text-4xl text-green-500"></i>

                {{-- Decorative Ring --}}
                <svg
                    class="absolute inset-0 h-full w-full -rotate-90"
                    viewBox="0 0 64 64"
                    fill="none"
                >
                    <circle
                        cx="32"
                        cy="32"
                        r="30"
                        stroke="#22c55e"
                        stroke-width="3"
                        stroke-linecap="round"
                        class="success-checkmark"
                    />
                </svg>
            </div>
        </div>

        {{-- Changeable Text --}}
        <p class="success-text text-sm font-medium text-gray-700 text-center">
            {{ $text }}
        </p>

    </div>
</div>
