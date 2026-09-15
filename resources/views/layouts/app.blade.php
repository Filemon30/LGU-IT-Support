<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="reverb-key" content="{{ config('broadcasting.connections.reverb.key') }}">
    <meta name="reverb-host" content="{{ config('broadcasting.connections.reverb.options.host') }}">
    <meta name="reverb-port" content="{{ config('broadcasting.connections.reverb.options.port') }}">

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('assets/images/biringan.png') }}"
    >

    <title>
        @yield('title', 'Dashboard')
        - City of Biringan EnchantaTech
    </title>

    @yield('head')

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <script src="{{ asset('assets/js/app.js') }}"></script>

</head>


<body class="min-h-screen bg-[#fafafa]">

<div class="flex min-h-screen">


    {{-- =========================================================
    SIDEBAR
    ========================================================== --}}

    <aside
        id="app-sidebar"
        class="
            fixed
            inset-y-0
            left-0
            z-40
            flex
            h-screen
            w-64
            shrink-0
            flex-col
            border-r
            border-gray-200
            bg-white
            -translate-x-full
            transition-transform
            duration-300
            ease-in-out
            lg:sticky
            lg:top-0
            lg:translate-x-0
        "
    >

        {{-- Mobile Close --}}

        <button
            type="button"
            id="app-sidebar-close"
            title="Close menu"
            class="
                absolute
                right-3
                top-4
                flex
                h-8
                w-8
                items-center
                justify-center
                rounded-full
                text-gray-500
                transition-colors
                hover:bg-gray-100
                hover:text-gray-900
                lg:hidden
                z-10
            "
        >
            <i class="ti ti-x text-lg"></i>
        </button>


        {{-- =====================================================
        LOGO (Sticky Header)
        ====================================================== --}}

        <div class="sticky top-0 z-10 bg-white">
            <a
                class="
                    flex
                    items-center
                    gap-3
                    px-4
                    py-5
                    transition-opacity
                "
            >

                <img
                    src="{{ asset('assets/images/biringan-sm.png') }}"
                    alt="EnchantaTech logo"
                    class="
                        h-11
                        w-11
                        shrink-0
                        rounded-md
                        object-cover
                    "
                >

                <div
                    class="
                        flex
                        min-w-0
                        flex-col
                        leading-none
                    "
                >

                    <span
                        class="
                            truncate
                            text-lg
                            font-bold
                            text-gray-900
                        "
                    >
                        CITY OF BIRINGAN
                    </span>

                    <span
                        class="
                            text-[8px]
                            font-bold
                            uppercase
                            tracking-[0.14em]
                            text-gray-400
                        "
                    >
                        ENCHANTATECH IT SUPPORT
                    </span>

                </div>

            </a>

            <div class="border-t border-gray-200"></div>
        </div>


        {{-- =====================================================
        ROLE
        ====================================================== --}}

        @php

            $role = session(
                'role',
                'admin'
            );

            $isAdmin =
                $role === 'admin';

        @endphp


        {{-- =====================================================
        NAVIGATION (Scrollable)
        ====================================================== --}}

        <nav
            id="app-sidebar-nav"
            class="
                flex-1
                overflow-y-auto
                mt-4
                flex
                flex-col
                gap-1
                px-2
                pb-4
                [&::-webkit-scrollbar]:w-0
                [&::-webkit-scrollbar-track]:bg-transparent
                [&::-webkit-scrollbar-thumb]:bg-transparent
            "
        >

            {{-- Dashboard --}}

            <x-side_nav_btn
                icon="ti ti-layout-dashboard"
                label="Dashboard"
                href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.dashboard') : request()->routeIs('dashboard')"
            />


            {{-- =================================================
            ADMIN USERS
            ================================================== --}}

            @if ($isAdmin)

                <x-side_nav_btn
                        icon="ti ti-users-group"
                        label="Staff"
                        href="{{ route('admin.staff') }}"
                        data-sidebar-btn
                        data-ajax-nav
                        data-sidebar-link
                        :active="request()->routeIs('admin.staff*')"
                    />

                    <x-side_nav_btn
                        icon="ti ti-building-community"
                        label="Barangays"
                        href="{{ route('admin.barangays') }}"
                        data-sidebar-btn
                        data-ajax-nav
                        data-sidebar-link
                        :active="request()->routeIs('admin.barangays')"
                    />

                <x-side_nav_btn
                        icon="ti ti-building-bank"
                        label="Offices"
                        href="{{ route('admin.offices') }}"
                        data-sidebar-btn
                        data-ajax-nav
                        data-sidebar-link
                        :active="request()->routeIs('admin.offices') || request()->routeIs('admin.offices.office_divisions')"
                />

                
                {{-- Services --}}

                <x-side_nav_btn
                    icon="ti ti-tool"
                    label="Services"
                    href="{{ route('admin.services') }}"
                    data-sidebar-btn
                    data-ajax-nav
                    data-sidebar-link
                    :active="request()->routeIs('admin.services*')"
                />

            @endif


            {{-- Tickets --}}

            <x-side_nav_btn
                icon="ti ti-ticket"
                label="{{ $isAdmin ? 'Tickets' : 'My Tickets' }}"
                href="{{ $isAdmin ? route('admin.tickets.index') : route('tickets.index') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.tickets.index') : request()->routeIs('tickets.index')"
            />



            {{-- Notifications --}}

            <x-side_nav_btn
                icon="ti ti-bell"
                label="Notifications"
                href="{{ $isAdmin ? route('admin.notifications') : route('notifications') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.notifications') : request()->routeIs('notifications')"
            />


            {{-- Reports --}}

            @if ($isAdmin)

                <x-side_nav_btn
                    icon="ti ti-report-analytics"
                    label="Reports"
                    href="{{ route('admin.reports') }}"
                    data-sidebar-btn
                    data-ajax-nav
                    data-sidebar-link
                    :active="request()->routeIs('admin.reports')"
                />

            @endif


            {{-- History --}}

            <x-side_nav_btn
                icon="ti ti-history"
                label="History"
                href="{{ $isAdmin ? route('admin.history') : route('history') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.history') : request()->routeIs('history')"
            />


            <div class="my-2 border-t border-gray-200"></div>


            {{-- Profile --}}

            <x-side_nav_btn
                icon="ti ti-user"
                label="Profile"
                href="{{ $isAdmin ? route('admin.profile') : route('profile') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.profile') : request()->routeIs('profile')"
            />


            {{-- Settings --}}

            <x-side_nav_btn
                icon="ti ti-settings"
                label="Settings"
                href="{{ $isAdmin ? route('admin.settings') : route('settings') }}"
                data-sidebar-btn
                data-ajax-nav
                data-sidebar-link
                :active="$isAdmin ? request()->routeIs('admin.settings') : request()->routeIs('settings')"
            />

        </nav>

    </aside>


    {{-- =========================================================
    MOBILE BACKDROP
    ========================================================== --}}

    <div
        id="app-sidebar-backdrop"
        class="
            fixed
            inset-0
            z-30
            hidden
            bg-black/50
            lg:hidden
        "
    ></div>


    {{-- =========================================================
    MAIN APPLICATION
    ========================================================== --}}

    <div class="flex min-w-0 flex-1 flex-col">


        {{-- =====================================================
        HEADER
        ====================================================== --}}

        <header
            class="
                sticky
                top-0
                z-10
                flex
                h-16
                shrink-0
                items-center
                justify-between
                border-b
                border-gray-200
                bg-white
                px-4
                sm:px-6
            "
        >

            {{-- LEFT --}}

            <div class="flex items-center gap-2 sm:gap-3">

                <button
                    type="button"
                    id="app-sidebar-open"
                    title="Open menu"
                    class="
                        flex
                        h-9
                        w-9
                        items-center
                        justify-center
                        rounded-full
                        text-gray-600
                        transition-colors
                        hover:bg-gray-100
                        hover:text-gray-900
                        lg:hidden
                    "
                >
                    <i class="ti ti-menu-2 text-xl"></i>
                </button>


                {{-- =================================================
                HEADER TITLE

                IMPORTANT:
                JavaScript WILL NOT modify this.
                Laravel controls the title.
                ================================================== --}}

                <h1
                    id="app-page-title"
                    class="
                        text-base
                        font-bold
                        text-gray-900
                        sm:text-lg
                    "
                >
                    @yield('title', 'Dashboard')
                </h1>

            </div>


            {{-- =====================================================
            HEADER RIGHT
            ====================================================== --}}

            <div class="flex items-center gap-3">


                {{-- Notifications --}}

                @php

                    $notifications = [

                        [
                            'unread' => true,
                            'icon' => 'ti ti-ticket',
                            'color' => 'text-blue-600 bg-blue-50',
                            'title' => 'New ticket submitted',
                            'body' => 'Printer not working at Barangay Hall.',
                            'time' => '5m ago',
                        ],

                        [
                            'unread' => true,
                            'icon' => 'ti ti-circle-check',
                            'color' => 'text-green-600 bg-green-50',
                            'title' => 'Ticket #1042 resolved',
                            'body' => 'Wi-Fi access point replaced.',
                            'time' => '1h ago',
                        ],

                        [
                            'unread' => true,
                            'icon' => 'ti ti-message',
                            'color' => 'text-amber-600 bg-amber-50',
                            'title' => 'New comment',
                            'body' => 'Admin commented on ticket #1039.',
                            'time' => '3h ago',
                        ],

                        [
                            'unread' => false,
                            'icon' => 'ti ti-user-plus',
                            'color' => 'text-purple-600 bg-purple-50',
                            'title' => 'New staff account',
                            'body' => 'IT Staff account was created.',
                            'time' => '1d ago',
                        ],

                        [
                            'unread' => false,
                            'icon' => 'ti ti-refresh',
                            'color' => 'text-gray-600 bg-gray-100',
                            'title' => 'Ticket #1035 updated',
                            'body' => 'Status changed to In Progress.',
                            'time' => '2d ago',
                        ],

                    ];

                    $unreadCount = count(
                        array_filter(
                            $notifications,
                            fn ($notification) =>
                                $notification['unread']
                        )
                    );

                @endphp


                <details class="group relative">

                    <summary
                        title="Notifications"
                        class="
                            relative
                            flex
                            h-9
                            w-9
                            cursor-pointer
                            select-none
                            list-none
                            items-center
                            justify-center
                            rounded-full
                            text-gray-600
                            transition-colors
                            hover:bg-gray-100
                            hover:text-gray-900
                            [&::-webkit-details-marker]:hidden
                        "
                    >

                        <i class="ti ti-bell text-xl"></i>

                        @if ($unreadCount > 0)

                            <span
                                class="
                                    absolute
                                    -right-0.5
                                    -top-0.5
                                    flex
                                    h-4
                                    min-w-4
                                    items-center
                                    justify-center
                                    rounded-full
                                    bg-red-500
                                    px-1
                                    text-[10px]
                                    font-bold
                                    leading-none
                                    text-white
                                "
                            >
                                {{ $unreadCount }}
                            </span>

                        @endif

                    </summary>


                    <div
                        class="
                            fixed
                            left-1/2
                            top-16
                            z-20
                            w-[calc(100vw-2rem)]
                            max-w-sm
                            -translate-x-1/2
                            rounded-lg
                            border
                            border-gray-200
                            bg-white
                            shadow-lg
                            sm:absolute
                            sm:left-auto
                            sm:right-0
                            sm:top-full
                            sm:w-80
                            sm:max-w-none
                            sm:translate-x-0
                        "
                    >

                        <div
                            class="
                                flex
                                h-11
                                items-center
                                justify-between
                                rounded-t-lg
                                border-b
                                border-gray-200
                                px-4
                            "
                        >

                            <div class="flex items-center gap-2">

                                <span
                                    class="
                                        text-sm
                                        font-bold
                                        text-gray-900
                                    "
                                >
                                    Notifications
                                </span>

                                <span
                                    class="
                                        rounded-full
                                        bg-red-50
                                        px-1.5
                                        py-0.5
                                        text-[10px]
                                        font-bold
                                        text-red-600
                                    "
                                >
                                    {{ $unreadCount }} new
                                </span>

                            </div>

                            <button
                                type="button"
                                class="
                                    text-xs
                                    font-medium
                                    text-[#071f45]
                                    hover:underline
                                "
                            >
                                Mark all as read
                            </button>

                        </div>


                        <div
                            class="
                                max-h-[55vh]
                                overflow-y-auto
                                sm:max-h-72
                            "
                        >

                            @foreach ($notifications as $notification)

                                <a
                                    href="#"
                                    class="
                                        flex
                                        items-start
                                        gap-3
                                        px-4
                                        py-3
                                        transition-colors
                                        {{ $notification['unread']
                                            ? 'bg-[#eef4ff] hover:bg-[#e3edff]'
                                            : 'hover:bg-gray-50' }}
                                    "
                                >

                                    <span
                                        class="
                                            flex
                                            h-8
                                            w-8
                                            shrink-0
                                            items-center
                                            justify-center
                                            rounded-full
                                            {{ $notification['color'] }}
                                        "
                                    >
                                        <i
                                            class="{{ $notification['icon'] }} text-base"
                                        ></i>
                                    </span>


                                    <span
                                        class="
                                            flex
                                            min-w-0
                                            flex-col
                                        "
                                    >

                                        <span
                                            class="
                                                flex
                                                items-center
                                                gap-1.5
                                            "
                                        >

                                            <span
                                                class="
                                                    truncate
                                                    text-sm
                                                    text-gray-900
                                                    {{ $notification['unread']
                                                        ? 'font-bold'
                                                        : 'font-semibold' }}
                                                "
                                            >
                                                {{ $notification['title'] }}
                                            </span>

                                            @if ($notification['unread'])

                                                <span
                                                    class="
                                                        h-1.5
                                                        w-1.5
                                                        shrink-0
                                                        rounded-full
                                                        bg-blue-500
                                                    "
                                                ></span>

                                            @endif

                                        </span>


                                        <span
                                            class="
                                                mt-0.5
                                                line-clamp-2
                                                text-xs
                                                text-gray-500
                                            "
                                        >
                                            {{ $notification['body'] }}
                                        </span>


                                        <span
                                            class="
                                                mt-1
                                                text-[10px]
                                                uppercase
                                                tracking-wide
                                                text-gray-400
                                            "
                                        >
                                            {{ $notification['time'] }}
                                        </span>

                                    </span>

                                </a>

                            @endforeach

                        </div>


                        <a
                            href="{{ $isAdmin ? route('admin.notifications') : route('notifications') }}"
                            data-ajax-nav
                            class="
                                flex
                                h-10
                                items-center
                                justify-center
                                gap-2
                                rounded-b-lg
                                border-t
                                border-gray-200
                                text-sm
                                font-medium
                                text-[#071f45]
                                transition-colors
                                hover:bg-gray-50
                            "
                        >
                            View all notifications
                        </a>

                    </div>

                </details>


                <div class="h-6 w-px bg-gray-200"></div>


                {{-- User --}}

                @php

                    $userName = session(
                        'user_name',
                        'Administrator'
                    );

                @endphp


                <details class="group relative">

                    <summary
                        class="
                            flex
                            cursor-pointer
                            select-none
                            items-center
                            gap-2.5
                            rounded-full
                            p-1
                            pl-2
                            pr-2
                            list-none
                            transition-colors
                            hover:bg-gray-100
                            [&::-webkit-details-marker]:hidden
                        "
                    >

                        <span
                            class="
                                hidden
                                text-sm
                                font-medium
                                text-gray-700
                                sm:inline
                            "
                        >
                            {{ $userName }}
                        </span>


                        <span
                            class="
                                flex
                                h-8
                                w-8
                                items-center
                                justify-center
                                rounded-full
                                bg-[#071f45]
                                text-sm
                                font-semibold
                                text-white
                            "
                        >
                            {{ mb_strtoupper(
                                mb_substr(
                                    $userName,
                                    0,
                                    1
                                )
                            ) }}
                        </span>


                        <i
                            class="
                                ti
                                ti-chevron-down
                                text-base
                                text-gray-500
                                transition-transform
                                duration-200
                                group-open:rotate-180
                            "
                        ></i>

                    </summary>


                    <div
                        class="
                            absolute
                            right-0
                            top-full
                            z-20
                            w-48
                            rounded-lg
                            border
                            border-gray-200
                            bg-white
                            py-1
                            shadow-lg
                        "
                    >

                        <a
                            href="{{ route('logout') }}"
                            class="
                                flex
                                h-10
                                items-center
                                gap-3
                                px-4
                                text-sm
                                text-red-600
                                transition-colors
                                hover:bg-red-50
                            "
                        >

                            <i class="ti ti-logout text-lg"></i>

                            <span>
                                Logout
                            </span>

                        </a>

                    </div>

                </details>

            </div>

        </header>


        {{-- =========================================================
        PAGE CONTENT
        ========================================================== --}}

        <main
            id="app-main"
            class="
                min-w-0
                flex-1
                overflow-x-auto
                p-4
                sm:p-6
                lg:p-6
            "
        >
            <div id="page-content">

                @yield('content')

            </div>

        </main>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('app-sidebar');
        const openBtn = document.getElementById('app-sidebar-open');
        const closeBtn = document.getElementById('app-sidebar-close');
        const backdrop = document.getElementById('app-sidebar-backdrop');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            backdrop.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        if (openBtn) {
            openBtn.addEventListener('click', openSidebar);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
    });

    // Toast Container
    var toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none';
    document.body.appendChild(toastContainer);

    // Render hidden alert templates using the Blade component
    var alertTemplates = {};
    document.querySelectorAll('[data-alert-template]').forEach(function(el) {
        alertTemplates[el.dataset.alertTemplate] = el.outerHTML;
        el.remove();
    });

    function showToast(title, message, type) {
        type = type || 'info';
        var html = alertTemplates[type] || alertTemplates.info;
        if (!html) return;

        var wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        var toast = wrapper.firstElementChild;

        toast.classList.add('pointer-events-auto', 'shadow-lg', 'max-w-sm', 'w-full');
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.3s ease-out';

        var titleEl = toast.querySelector('h5');
        if (titleEl) titleEl.textContent = title;

        var descEl = toast.querySelector('p');
        if (descEl) {
            descEl.textContent = message;
            descEl.classList.add('line-clamp-2');
        }

        var closeBtn = document.createElement('button');
        closeBtn.className = 'shrink-0 text-gray-400 hover:text-gray-600 transition-colors';
        closeBtn.innerHTML = '<i class="ti ti-x text-sm"></i>';
        closeBtn.onclick = function() { toast.remove(); };
        toast.appendChild(closeBtn);

        toastContainer.appendChild(toast);

        requestAnimationFrame(function() {
            toast.style.transform = 'translateX(0)';
            toast.style.opacity = '1';
        });

        setTimeout(function() {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(function() { toast.remove(); }, 300);
        }, 5000);
    }

    // Echo New Ticket Listener
    (function() {
        function bindEcho() {
            if (typeof window.Echo === 'undefined') return false;

            window.Echo.channel('tickets')
                .listen('.new-ticket', function(e) {
                    var requester = e.requester_type === 'Barangay'
                        ? 'Barangay - ' + (e.requester_name || '-')
                        : 'Office - ' + (e.requester_name || '-');
                    var msg = 'New ticket ' + (e.ticket_ref_num || '') + ' from ' + requester;
                    showToast('New Ticket', msg, 'info');
                });
            return true;
        }

        if (!bindEcho()) {
            var tries = 0;
            var timer = setInterval(function() {
                tries++;
                if (bindEcho() || tries > 50) clearInterval(timer);
            }, 200);
        }
    })();
</script>

{{-- Hidden Alert Notification Templates --}}
<div style="display:none">
    <x-alert-notification data-alert-template="info" type="info" title=" " description=" " />
    <x-alert-notification data-alert-template="success" type="success" title=" " description=" " />
    <x-alert-notification data-alert-template="warning" type="warning" title=" " description=" " />
    <x-alert-notification data-alert-template="error" type="error" title=" " description=" " />
</div>

</body>
</html>