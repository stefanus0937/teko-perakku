<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TekoPerakku</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #333;
            --text-main: #1a1a1a;
            --text-muted: #71717a;
            --border-color: #e4e4e7;
            --bg-light: #fafafa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #fff;
            color: var(--text-main);
        }

        /* Header styles dipindah ke partials/header.blade.php (scoped ke .main-header) */

        /* Main Content Layout */
        .main-container {
            width: 100%;
            margin: 40px 0;
            padding: 0 80px;
            display: flex !important;
            align-items: flex-start;
            gap: 40px;
            min-height: 600px;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            flex-shrink: 0;
            background: #fff;
            border-right: 1px solid transparent;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-item {
            margin-bottom: 10px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 20px;
            text-decoration: none;
            color: #71717a;
            font-size: 14px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.2s;
        }

        .sidebar-link i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-link:hover {
            background: #f4f4f5;
            color: #18181b;
        }

        .sidebar-link.active {
            background: #f4f4f5;
            color: #18181b;
        }

        /* Content Area */
        .content-area {
            flex: 1;
        }

        @media (max-width: 1024px) {
            .main-container { padding: 0 40px; flex-direction: column; }
            .sidebar { width: 100%; }
        }

        /* Global Settings Classes */
        body.font-small { --base-font-size: 12px; }
        body.font-medium { --base-font-size: 15px; }
        body.font-large { --base-font-size: 19px; }

        body.font-small .nav-link, body.font-small .sidebar-link { font-size: 11px !important; }
        body.font-medium .nav-link, body.font-medium .sidebar-link { font-size: 14px !important; }
        body.font-large .nav-link, body.font-large .sidebar-link { font-size: 18px !important; }

        body.font-small .logo { font-size: 20px !important; }
        body.font-large .logo { font-size: 32px !important; }

        body.dark-mode {
            background-color: #121212 !important;
            color: #e4e4e7 !important;
        }
        body.dark-mode .main-header,
        body.dark-mode .sidebar {
            background: #1e1e1e !important;
            border-color: #333 !important;
        }
        body.dark-mode .logo,
        body.dark-mode .user-name {
            color: #fff !important;
        }
        body.dark-mode .nav-link { color: #e4e4e7 !important; }
        body.dark-mode .sidebar-link { color: #a1a1aa !important; }
        body.dark-mode .sidebar-link:hover, 
        body.dark-mode .sidebar-link.active {
            background: #2a2a2a !important;
            color: #fff !important;
        }
        body.dark-mode .search-input {
            background: #2a2a2a !important;
            border-color: #444 !important;
            color: #fff !important;
        }

        /* Profile dropdown styles dipindah ke partials/header.blade.php */
    </style>
    @yield('css')
</head>
<body>
    {{-- Unified shared header --}}
    @include('partials.header')

    <main class="main-container">
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('user.profile') }}" class="sidebar-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                        <i class="far fa-user"></i> Profil
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('chats.index') }}" class="sidebar-link {{ request()->routeIs('chats*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('favorit') }}" class="sidebar-link {{ request()->routeIs('favorit') ? 'active' : '' }}">
                        <i class="far fa-heart"></i> Favorit
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('pengaturan') }}" class="sidebar-link {{ request()->routeIs('pengaturan') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                </li>
            </ul>
        </aside>

        <section class="content-area">
            @yield('content')
        </section>
    </main>

    <script>
        // Apply settings immediately to prevent flicker
        (function() {
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            if (isDarkMode) document.body.classList.add('dark-mode');
            
            const savedFont = localStorage.getItem('fontSize') || 'medium';
            document.body.classList.add('font-' + savedFont);

            // Translation Dictionary
            const translations = {
                en: {
                    "Beranda": "Home",
                    "Katalog": "Catalog",
                    "Kategori": "Categories",
                    "Tentang Kami": "About Us",
                    "Kontak": "Contact",
                    "Profil": "Profile",
                    "Chat": "Chat",
                    "Favorit": "Favorites",
                    "Pengaturan": "Settings",
                    "Cari Produk": "Search Products",
                    "Ukuran Font": "Font Size",
                    "Mode Gelap": "Dark Mode",
                    "Notifikasi Email": "Email Notifications",
                    "Bahasa": "Language",
                    "Hapus Akun": "Delete Account",
                    "Kecil": "Small",
                    "Sedang": "Medium",
                    "Besar": "Large",
                    "Apakah anda yakin untuk menghapus akun?": "Are you sure you want to delete your account?",
                    "Tidak": "No",
                    "Iya": "Yes"
                }
            };

            const savedLang = localStorage.getItem('language') || 'id';
            if (savedLang === 'en') {
                document.addEventListener('DOMContentLoaded', () => {
                    const dict = translations.en;
                    const walk = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null, false);
                    let node;
                    while (node = walk.nextNode()) {
                        const trimmed = node.nodeValue.trim();
                        if (dict[trimmed]) {
                            node.nodeValue = node.nodeValue.replace(trimmed, dict[trimmed]);
                        }
                    }
                    // Special case for placeholders
                    document.querySelectorAll('[placeholder]').forEach(el => {
                        if (dict[el.placeholder]) el.placeholder = dict[el.placeholder];
                    });
                });
            }

            // Profile Dropdown Toggle
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
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @vite(['resources/js/app.js'])
    <script>
        @if(Auth::check())
        window.addEventListener('load', () => {
            if (window.Echo) {
                // Mark all received messages as delivered when connecting
                axios.post('{{ route("chats.delivered.all") }}');

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
    @yield('js')
</body>
</html>
