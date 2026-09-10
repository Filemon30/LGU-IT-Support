@props([
    'title' => 'Help Desk',
    'subtitle' => null,
    'logo' => null,
    'background' => '#0f172a',
    'textColor' => '#ffffff',
    'showHamburger' => false,
])

<header
    {{ $attributes->merge([
        'class' => 'w-full shadow-sm',
        'style' => 'background-color: '.$background.'; color: '.$textColor.';',
    ]) }}
>
    <div class="grid w-full grid-cols-[1fr_auto] md:grid-cols-[1fr_auto_1fr] items-center gap-3 px-4 py-3 sm:px-6 sm:py-4 lg:px-8">
        <a
            href="{{ request()->routeIs('home') ? route('home') : url()->current() }}"
            class="flex items-center gap-2 sm:gap-3 justify-self-start self-center min-w-0 hover:opacity-85 transition-opacity"
        >
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $title }} logo" class="h-10 w-10 sm:h-12 sm:w-12 rounded-md object-cover shrink-0">
            @endif

            @if($title || $subtitle)
                <div class="flex flex-col leading-none min-w-0">
                    @if($title)
                        <span class="text-base sm:text-lg font-semibold leading-none truncate">{{ $title }}</span>
                    @endif

                    @if($subtitle)
                        <span class="hidden sm:inline mt-1 text-[10px] uppercase tracking-[0.14em] text-gray-400">{{ $subtitle }}</span>
                    @endif
                </div>
            @endif
        </a>

        @if(trim($slot) !== '')
            <div class="hidden md:flex items-center justify-center gap-8 self-center justify-self-center">
                {{ $slot }}
            </div>
        @endif

        <div class="flex items-center gap-2 justify-self-end self-center">
            <div class="header-buttons hidden md:flex items-center gap-2" style="display: none;">
                <a href="{{ route('track.request') }}" class="header-btn header-btn-secondary">
                    <span> Track Ticket </span>
                    <i class="ti ti-search"></i>
                </a>

                <a href="{{ route('submit.request') }}" class="header-btn header-btn-submit">
                    <span> Submit Ticket </span>
                    <i class="ti ti-send"></i>
                </a>

                <a href="{{ route('login') }}" class="header-btn header-btn-primary">
                    <span> Login </span>
                    <i class="ti ti-login"></i>
                </a>
            </div>

            @if($showHamburger)
                <button class="hamburger-btn" onclick="toggleHamburgerMenu()" aria-label="Menu">
                    <i class="ti ti-menu-2"></i>
                </button>
            @endif
        </div>
    </div>

    @if($showHamburger)
        <div class="hamburger-dropdown" id="hamburgerDropdown">
            <nav class="hamburger-nav">
                {{ $slot }}
            </nav>
            <div class="hamburger-buttons">
                <a href="{{ route('track.request') }}" class="header-btn header-btn-secondary">
                    <span> Track Request </span>
                    <i class="ti ti-search"></i>
                </a>

                <a href="{{ route('submit.request') }}" class="header-btn header-btn-submit">
                    <span> Submit Ticket </span>
                    <i class="ti ti-send"></i>
                </a>

                <a href="{{ route('login') }}" class="header-btn header-btn-primary">
                    <span> Login </span>
                    <i class="ti ti-login"></i>
                </a>
            </div>
        </div>
    @endif
</header>