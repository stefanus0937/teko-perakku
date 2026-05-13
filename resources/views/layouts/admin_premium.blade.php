<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TekoPerakku</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    {{-- Layout (sidebar + main-content + DataTables + manage block) → admin.css.
         Body harus pakai class `tp-admin-premium` agar varian Inter/fixed-sidebar aktif. --}}
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <style>
        .profile-dropdown {
    position: relative;
}

.user-nav {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    background: transparent;
    border: none;
}

.user-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #111827;
}

.user-name i {
    font-size: 11px;
    color: #9ca3af;
}

.profile-dropdown-menu {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 180px;
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    padding: 8px;
    display: none;
    z-index: 999;
}

.profile-dropdown-menu.show {
    display: block;
}

.profile-dropdown-item {
    width: 100%;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all .2s ease;
}

.profile-dropdown-item:hover {
    background: #f3f4f6;
}

.profile-dropdown-item.logout {
    color: #dc2626;
}

.profile-dropdown-item.logout:hover {
    background: #fef2f2;
}
    </style>
    @yield('css')
</head>
<body class="tp-app-shell tp-admin-premium">
    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    ADMIN
                    <span>TekoPerakku</span>
                </div>
            </div>
            <nav class="sidebar-menu">

                <a href="{{ route('profile') }}" class="menu-item {{ request()->routeIs('profile*') ? 'active' : '' }}">
                    <i class="far fa-user"></i> Profil
                </a>
@if(auth()->user()->role == 'admin_utama')
                <a href="{{ route('admin.manage.index') }}" class="menu-item {{ request()->routeIs('admin.manage.*') ? 'active' : '' }}">
                    <i class="fas fa-user-shield"></i> Kelola Admin
                </a>
@endif
                <a href="{{ route('admin.pengerajin-index') }}" class="menu-item {{ request()->routeIs('admin.pengerajin*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Pengrajin
                </a>
                <a href="{{ route('admin.usaha-index') }}" class="menu-item {{ request()->routeIs('admin.usaha*') ? 'active' : '' }}">
                    <i class="fas fa-briefcase"></i> Usaha
                </a>
                <a href="{{ route('admin.produk-index') }}" class="menu-item {{ request()->routeIs('admin.produk*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Produk
                </a>
                <a href="{{ route('admin.pelaporan-index') }}" class="menu-item {{ request()->routeIs('admin.pelaporan*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Pelaporan
                </a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <div class="profile-dropdown">
                    <button class="user-nav" id="profileTrigger" type="button">

                        <div class="user-avatar">
                            <img src="{{ Auth::user()->foto 
                                ? asset('storage/'.Auth::user()->foto) 
                                : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->nama ?? Auth::user()->username).'&background=e4e4e7&color=71717a' }}" 
                                alt="User">
                        </div>

                        <span class="user-name">
                            {{ Auth::user()->nama ?? Auth::user()->username }}
                            <i class="fas fa-chevron-down"></i>
                        </span>

                    </button>

                    <div class="profile-dropdown-menu" id="profileDropdown">

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="profile-dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>

                    </div>
                </div>
            </header>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        @if(Auth::check())
        window.addEventListener('load', () => {
            if (window.Echo) {
                // Helper to clear UI badges
                const clearUnreadBadge = (userId) => {
                    const contactItem = document.querySelector(`.contact-item[href$="/chats/${userId}"]`);
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
                                               pathRegex.test(window.location.pathname) &&
                                               document.visibilityState === 'visible' &&
                                               document.hasFocus();

                        if (isStrictlyInRoom) {
                            clearUnreadBadge(e.message.sender_id);
                            axios.post(`{{ url('chats') }}/${e.message.sender_id}/read`);
                        } else {
                            axios.post(`{{ url('chats') }}/${e.message.sender_id}/delivered`);
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

    {{-- Global settings (dark mode + font + language) — single source of truth --}}
    @include('partials._settings')

    {{-- Shared UI behaviors: clickable rows + click-toggle action dropdowns --}}
    @include('partials._admin-shell-js')

    @yield('js')
    
</body>
</html>