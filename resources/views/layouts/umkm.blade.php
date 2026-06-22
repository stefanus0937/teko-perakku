<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TekoPerakku</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    {{-- Shared layout + sidebar + DataTables + manage block → admin.css (centralized).
         Dark mode → theme-dark.css. Header → partials/header.blade.php → header.css. --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    @yield('css')
</head>
<body class="tp-app-shell">
    {{-- Unified shared header --}}
    @include('partials.header')

    <main class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('umkm.profile') }}" class="sidebar-link {{ request()->routeIs('umkm.profile') ? 'active' : '' }}">
                        <i class="far fa-user"></i> {{ __('navigation.profile') }}
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('chats.index') }}" class="sidebar-link {{ request()->routeIs('chats*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> {{ __('navigation.chat') }}
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('admin.produk-index') }}" class="sidebar-link {{ request()->routeIs('admin.produk*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i> {{ __('navigation.products') }}
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('pengaturan') }}" class="sidebar-link {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> {{ __('navigation.settings') }}
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('admin.pelaporan-index') }}" class="sidebar-link {{ request()->routeIs('admin.pelaporan*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i> {{ __('navigation.reports') }}
                    </a>
                </li>
            </ul>
        </aside>

        <section class="content-area">
            @yield('content')
        </section>
    </main>

    {{-- Global settings (dark mode + font + language) — single source of truth --}}
    @include('partials._settings')

    <script>
        // Profile Dropdown Toggle (logic header — bukan setting)
        document.addEventListener('DOMContentLoaded', () => {
            const trigger = document.getElementById('profileTrigger');
            const dropdown = document.getElementById('profileDropdown');
            if (trigger && dropdown) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('show');
                });
                document.addEventListener('click', () => {
                    dropdown.classList.remove('show');
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        @if(Auth::check())
        window.addEventListener('load', () => {
            if (window.Echo) {
                // Helper to clear UI badges
                const clearUnreadBadge = (userId, usahaId) => {
                    const selector = `.contact-item[data-user-id="${userId}"][data-usaha-id="${usahaId || 'null'}"]`;
                    const contactItem = document.querySelector(selector);
                    if (contactItem) {
                        const badge = contactItem.querySelector('.unread-badge');
                        if (badge) badge.style.display = 'none';
                        const lastMsgTextStrong = contactItem.querySelector('.last-msg-text strong');
                        if (lastMsgTextStrong) {
                            const span = lastMsgTextStrong.parentElement;
                            span.innerText = lastMsgTextStrong.innerText;
                        }
                    }
                };

                window.Echo.private('chat.{{ Auth::id() }}')
                    .listen('.message.sent', (e) => {
                        const pathRegex = new RegExp('/chats/' + e.message.sender_id + '$');
                        const isStrictlyInRoom = window.activeChatUserId && 
                                               window.activeChatUserId == e.message.sender_id && 
                                               (window.activeUsahaId == e.message.usaha_id) &&
                                               document.visibilityState === 'visible' &&
                                               document.hasFocus();

                        if (isStrictlyInRoom) {
                            clearUnreadBadge(e.message.sender_id, e.message.usaha_id);
                            axios.post(`{{ url('chats') }}/${e.message.sender_id}/read?usaha_id=${e.message.usaha_id || ''}`);
                        } else {
                            axios.post(`{{ url('chats') }}/${e.message.sender_id}/delivered?usaha_id=${e.message.usaha_id || ''}`);
                        }
                    });

                // Auto-mark as read when switching back to the tab
                const markCurrentAsRead = () => {
                    const pathRegex = new RegExp('/chats/' + window.activeChatUserId + '$');
                    if (window.activeChatUserId && document.visibilityState === 'visible' && document.hasFocus() && pathRegex.test(window.location.pathname)) {
                        clearUnreadBadge(window.activeChatUserId);
                        axios.post(`{{ url('chats') }}/${window.activeChatUserId}/read`);
                    }
                };

                // Clear badge on initial page load if we are already in a chat room
                if (window.activeChatUserId) {
                    clearUnreadBadge(window.activeChatUserId);
                }
                window.addEventListener('visibilitychange', markCurrentAsRead);
                window.addEventListener('focus', markCurrentAsRead);

                // Global online presence
                window.Echo.join('online')
                    .here((users) => { /* Online globally */ })
                    .joining((user) => { /* User joined globally */ })
                    .leaving((user) => { /* User left globally */ });
            }
        });
        @endif
    </script>

    {{-- Shared UI behaviors: clickable rows + click-toggle action dropdowns --}}
    @include('partials._admin-shell-js')

    @yield('js')
</body>
</html>
