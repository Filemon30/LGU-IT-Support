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
                z-30
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

                    $notifications = \App\Models\Notification::where('user_id', session('user_id'))
                        ->orderBy('created_at', 'desc')
                        ->get();

                    $unreadCount = \App\Models\Notification::where('user_id', session('user_id'))
                        ->where('notification_status', 'Unread')
                        ->count();

                    $newCount = \App\Models\Notification::where('user_id', session('user_id'))
                        ->where('notification_status', 'Unread')
                        ->where('created_at', '>=', now()->subHours(24))
                        ->count();

                @endphp


                <button
                    type="button"
                    id="notif-bell-btn"
                    title="Notifications"
                    class="
                        relative
                        flex
                        h-9
                        w-9
                        cursor-pointer
                        select-none
                        items-center
                        justify-center
                        rounded-full
                        text-gray-600
                        transition-colors
                        hover:bg-gray-100
                        hover:text-gray-900
                    "
                >

                    <i class="ti ti-bell text-xl"></i>

                    @if ($unreadCount > 0)

                        <span
                            id="notif-unread-badge"
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

                    @else

                        <span
                            id="notif-unread-badge"
                            class="
                                absolute
                                -right-0.5
                                -top-0.5
                                hidden
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
                        ></span>

                    @endif

                </button>


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
                            z-40
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

    // Toast Container — positioned top-right below header (h-16) so it never overlaps the user dropdown
    var toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.className = 'fixed right-4 z-[9999] flex flex-col gap-3 pointer-events-none';
    toastContainer.style.top = '72px';
    document.body.appendChild(toastContainer);

    function showToast(title, message, type) {
        type = type || 'info';

        var iconBgColors = {
            info: 'bg-blue-100',
            success: 'bg-green-100',
            warning: 'bg-amber-100',
            error: 'bg-red-100',
        };
        var iconTextColors = {
            info: 'text-blue-600',
            success: 'text-green-600',
            warning: 'text-amber-600',
            error: 'text-red-600',
        };
        var iconBg = iconBgColors[type] || iconBgColors.info;
        var iconColor = iconTextColors[type] || iconTextColors.info;

        var toast = document.createElement('div');
        toast.className = 'pointer-events-auto shadow-lg w-80 rounded-lg border border-gray-200 bg-white px-5 py-5 relative';
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.3s ease-out';

        toast.innerHTML =
            '<button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 transition-colors toast-close">' +
                '<i class="ti ti-x text-sm"></i>' +
            '</button>' +
            '<div class="flex items-center gap-3">' +
                '<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full ' + iconBg + '">' +
                    '<i class="ti ti-bell text-xl ' + iconColor + '"></i>' +
                '</span>' +
                '<div class="flex-1 min-w-0 pr-4">' +
                    '<h5 class="text-sm font-semibold text-gray-900">' + title + '</h5>' +
                    '<p class="text-xs text-gray-600 mt-0.5 line-clamp-2">' + message + '</p>' +
                '</div>' +
            '</div>';

        toast.querySelector('.toast-close').onclick = function() { toast.remove(); };

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

    // Click notification → mark as read, then navigate
    document.addEventListener('click', function(e) {
        var link = e.target.closest('.notif-link');
        if (!link) return;

        e.preventDefault();

        var notifId = link.dataset.notifId;
        var notifUrl = link.getAttribute('href');
        var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        var markReadUrl = '{{ $isAdmin ? route("admin.notifications.markAsRead", ":id") : route("notifications.markAsRead", ":id") }}'.replace(':id', notifId);

        fetch(markReadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then(function() {
            window.location.href = notifUrl;
        }).catch(function() {
            window.location.href = notifUrl;
        });
    });

    // Real-time notification listener via Reverb
    (function() {
        function bindEcho() {
            if (typeof window.Echo === 'undefined') return false;

            window.Echo.channel('notifications')
                .listen('.notification-created', function(e) {
                    var badge = document.getElementById('notif-unread-badge');
                    if (badge) {
                        badge.style.display = 'flex';
                        var current = parseInt(badge.textContent) || 0;
                        badge.textContent = current + 1;
                    }

                    var newCountEl = document.getElementById('notif-new-count');
                    if (newCountEl) {
                        var parts = newCountEl.textContent.match(/(\d+)/);
                        var currentCount = parts ? parseInt(parts[1]) : 0;
                        newCountEl.textContent = (currentCount + 1) + ' new';
                    }

                    var unreadCountEl = document.getElementById('notif-unread-count');
                    if (unreadCountEl) {
                        var parts2 = unreadCountEl.textContent.match(/(\d+)/);
                        var currentUnread = parts2 ? parseInt(parts2[1]) : 0;
                        unreadCountEl.textContent = (currentUnread + 1) + ' unread';
                    }

                    var list = document.querySelector('#notif-sidebar .flex-1.overflow-y-auto');
                    if (list) {
                        var emptyState = list.querySelector('.px-5.py-12');
                        if (emptyState) emptyState.remove();

                        var title = e.notification_title || e.notification_message || 'New Notification';
                        var message = e.notification_message || '';
                        var targetUrl = e.target_url || '#';
                        var timeAgo = 'just now';

                        var item = document.createElement('a');
                        item.href = targetUrl;
                        item.className = 'notif-link block border-b border-gray-100 px-5 py-3.5 transition-colors bg-[#eef4ff] hover:bg-[#e3edff]';
                        item.innerHTML =
                            '<div class="flex items-center gap-2">' +
                                '<span class="text-sm font-bold text-gray-900">' + escapeHtml(title) + '</span>' +
                                '<span class="h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500 notif-dot-unread"></span>' +
                            '</div>' +
                            (message ? '<div class="mt-1 text-xs text-gray-500 line-clamp-2">' + escapeHtml(message) + '</div>' : '') +
                            '<div class="mt-1 text-[11px] text-gray-400">' + timeAgo + '</div>';

                        list.insertBefore(item, list.firstChild);
                    }
                });
            return true;
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        if (!bindEcho()) {
            var tries = 0;
            var timer = setInterval(function() {
                tries++;
                if (bindEcho() || tries > 50) clearInterval(timer);
            }, 200);
        }
    })();

    // Echo New Ticket Listener
    (function() {
        var currentUserId = {{ session('user_id', 0) }};

        function bindEcho() {
            if (typeof window.Echo === 'undefined') return false;

            window.Echo.channel('tickets')
                .listen('.new-ticket', function(e) {
                    if (e.submitted_by && e.submitted_by == currentUserId) return;

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

{{-- Notification Sidebar --}}
<div id="notif-sidebar-overlay" class="fixed inset-0 z-50 hidden bg-black/40 transition-opacity duration-300" style="opacity:0;"></div>

<div
    id="notif-sidebar"
    class="fixed top-0 right-0 z-50 flex h-full w-full max-w-md flex-col bg-white shadow-2xl transition-transform duration-300 ease-in-out"
    style="transform: translateX(100%);"
>
    <div class="flex h-14 items-center justify-between border-b border-gray-200 px-5">
        <div class="flex items-center gap-2">
            <span class="text-base font-bold text-gray-900">Notifications</span>
            <span
                id="notif-new-count"
                class="rounded-full bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-600"
            >
                {{ $newCount }} new
            </span>
            <span
                id="notif-unread-count"
                class="rounded-full bg-red-50 px-1.5 py-0.5 text-[10px] font-bold text-red-600"
            >
                {{ $unreadCount }} unread
            </span>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                id="mark-all-read-btn"
                class="text-xs font-medium text-[#071f45] hover:underline"
            >
                Mark all as read
            </button>
            <button
                type="button"
                id="notif-sidebar-close"
                class="flex h-8 w-8 items-center justify-center rounded-full text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900"
            >
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto">
        @foreach ($notifications as $notification)
            <div
                data-notif-id="{{ $notification->notification_id }}"
                class="
                    notif-item
                    block
                    border-b
                    border-gray-100
                    px-5
                    py-3.5
                    transition-colors
                    {{ $notification->notification_status === 'Unread'
                        ? 'bg-[#eef4ff] hover:bg-[#e3edff]'
                        : 'hover:bg-gray-50' }}
                "
            >
                <a
                    href="{{ $notification->target_url ?: '#' }}"
                    class="notif-link block"
                    data-notif-id="{{ $notification->notification_id }}"
                >
                    <div class="flex items-center gap-2">
                        <span
                            class="
                                text-sm
                                text-gray-900
                                {{ $notification->notification_status === 'Unread' ? 'font-bold' : 'font-semibold' }}
                            "
                        >
                            {{ $notification->notification_title ?: Str::limit($notification->notification_message, 60) }}
                        </span>
                        @if ($notification->notification_status === 'Unread')
                            <span class="notif-dot-unread h-1.5 w-1.5 shrink-0 rounded-full bg-blue-500"></span>
                        @endif
                    </div>
                    @if ($notification->notification_message)
                        <div class="mt-1 text-xs text-gray-500 line-clamp-2">
                            {{ Str::limit($notification->notification_message, 80) }}
                        </div>
                    @endif
                </a>
                <div class="mt-1.5 flex items-center justify-between">
                    <span class="text-[11px] text-gray-400">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                    @if ($notification->notification_status === 'Unread')
                        <button
                            type="button"
                            class="mark-read-btn text-[11px] font-medium text-[#071f45] hover:underline"
                            data-notif-id="{{ $notification->notification_id }}"
                        >
                            Mark as read
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($notifications->isEmpty())
            <div class="px-5 py-12 text-center">
                <i class="ti ti-bell-off text-4xl text-gray-300"></i>
                <p class="mt-3 text-sm text-gray-500">No notifications yet.</p>
            </div>
        @endif
    </div>
</div>

<script>
(function() {
    var sidebar = document.getElementById('notif-sidebar');
    var overlay = document.getElementById('notif-sidebar-overlay');
    var bellBtn = document.getElementById('notif-bell-btn');
    var closeBtn = document.getElementById('notif-sidebar-close');

    function openSidebar() {
        overlay.classList.remove('hidden');
        requestAnimationFrame(function() {
            overlay.style.opacity = '1';
            sidebar.style.transform = 'translateX(0)';
        });
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        overlay.style.opacity = '0';
        sidebar.style.transform = 'translateX(100%)';
        document.body.style.overflow = '';
        setTimeout(function() { overlay.classList.add('hidden'); }, 300);
    }

    bellBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    var markAllBtn = document.getElementById('mark-all-read-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            fetch('{{ $isAdmin ? route("admin.notifications.markAllAsRead") : route("notifications.markAllAsRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    document.querySelectorAll('.notif-dot-unread').forEach(function(dot) {
                        dot.classList.remove('bg-blue-500');
                        dot.classList.add('bg-gray-300');
                        dot.classList.remove('notif-dot-unread');
                    });
                    document.querySelectorAll('.notif-link').forEach(function(link) {
                        link.classList.remove('bg-[#eef4ff]');
                        link.classList.add('hover:bg-gray-50');
                    });
                    var badge = document.getElementById('notif-unread-badge');
                    if (badge) badge.style.display = 'none';
                    var countSpan = document.getElementById('notif-new-count');
                    if (countSpan) countSpan.textContent = '0 new';
                    var unreadSpan = document.getElementById('notif-unread-count');
                    if (unreadSpan) unreadSpan.textContent = '0 unread';
                    markAllBtn.disabled = true;
                    markAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        });
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.mark-read-btn');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        var notifId = btn.dataset.notifId;
        var notifItem = btn.closest('.notif-item');

        fetch('{{ $isAdmin ? route("admin.notifications.markAsRead", ":id") : route("notifications.markAsRead", ":id") }}'.replace(':id', notifId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                if (notifItem) {
                    notifItem.classList.remove('bg-[#eef4ff]', 'hover:bg-[#e3edff]');
                    notifItem.classList.add('hover:bg-gray-50');
                    var dot = notifItem.querySelector('.notif-dot-unread');
                    if (dot) {
                        dot.classList.remove('bg-blue-500');
                        dot.classList.add('bg-gray-300');
                        dot.classList.remove('notif-dot-unread');
                    }
                    var title = notifItem.querySelector('.text-sm');
                    if (title) {
                        title.classList.remove('font-bold');
                        title.classList.add('font-semibold');
                    }
                    btn.remove();
                }
                var badge = document.getElementById('notif-unread-badge');
                if (badge) {
                    var current = parseInt(badge.textContent) || 0;
                    if (current > 1) {
                        badge.textContent = current - 1;
                    } else {
                        badge.style.display = 'none';
                    }
                }
                var unreadSpan = document.getElementById('notif-unread-count');
                if (unreadSpan) {
                    var parts = unreadSpan.textContent.match(/(\d+)/);
                    var currentUnread = parts ? parseInt(parts[1]) : 0;
                    if (currentUnread > 1) {
                        unreadSpan.textContent = (currentUnread - 1) + ' unread';
                    } else {
                        unreadSpan.textContent = '0 unread';
                    }
                }
            }
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) {
            closeSidebar();
        }
    });
})();
</script>

</body>

</html>