<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Her Vision Network')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #1D1D1D;
            color: #e0e0e0;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }

        /* NAV — pixel-matches Angular header: logo | search | links | user */
        .hvn-nav {
            background: #121212;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            padding: 0 24px;
            display: flex;
            align-items: center;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .hvn-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
            margin-right: 24px;
        }
        .hvn-logo img { height: 36px; width: auto; display: block; }

        /* Search bar — grows to fill available space */
        .hvn-search {
            flex: 1;
            position: relative;
            margin-right: 24px;
        }
        .hvn-search input {
            width: 100%;
            background: #2a2a2a;
            border: 1px solid transparent;
            border-radius: 4px;
            color: #e0e0e0;
            padding: 9px 40px 9px 14px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }
        .hvn-search input::placeholder { color: #6b6b6b; }
        .hvn-search input:focus { border-color: rgba(255,255,255,0.15); }
        .hvn-search svg {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b6b6b;
            pointer-events: none;
        }

        /* Nav links */
        .hvn-nav-links {
            display: flex;
            align-items: center;
            gap: 0;
            flex-shrink: 0;
        }
        .hvn-nav-links a {
            color: #ccc;
            text-decoration: none;
            padding: 6px 16px;
            font-size: 14px;
            white-space: nowrap;
            border-radius: 4px;
            transition: color .15s, background .15s;
        }
        .hvn-nav-links a:hover, .hvn-nav-links a.active { color: #fff; background: rgba(255,255,255,0.07); }
        .hvn-nav-links a.btn-accent {
            background: #F65F54;
            color: #fff;
            font-weight: 500;
            margin-left: 8px;
        }
        .hvn-nav-links a.btn-accent:hover { background: #d94f45; }

        /* User chip */
        .hvn-user {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            margin-left: 16px;
            cursor: pointer;
            position: relative;
        }
        .hvn-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background: #3a3a3a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: #ccc;
            overflow: hidden;
            flex-shrink: 0;
        }
        .hvn-user-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
        .hvn-user-email { font-size: 13px; color: #ccc; max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .hvn-user-caret { color: #888; font-size: 10px; }
        .hvn-user-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: #2a2a2a;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 6px;
            min-width: 160px;
            padding: 6px 0;
            z-index: 200;
        }
        .hvn-user-menu.open { display: block; }
        .hvn-user-menu a, .hvn-user-menu button {
            display: block;
            width: 100%;
            text-align: left;
            padding: 9px 16px;
            font-size: 14px;
            color: #ccc;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .hvn-user-menu a:hover, .hvn-user-menu button:hover { background: rgba(255,255,255,0.06); color: #fff; }

        /* MAIN */
        .hvn-main { max-width: 900px; margin: 0 auto; padding: 32px 16px 64px; }

        /* CARD */
        .hvn-card {
            background: #2a2a2a;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        /* FORM */
        .hvn-form { max-width: 440px; margin: 60px auto; }
        .hvn-form h1 { font-size: 24px; font-weight: 500; margin-bottom: 6px; color: #fff; }
        .hvn-form p.sub { color: #888; font-size: 14px; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; color: #aaa; margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%;
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 6px;
            color: #e0e0e0;
            padding: 11px 14px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color .2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: #F65F54; }
        .role-options { display: flex; gap: 12px; }
        .role-option {
            flex: 1;
            border: 2px solid #2a2a2a;
            border-radius: 8px;
            padding: 14px 12px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            text-align: center;
        }
        .role-option input[type=radio] { display: none; }
        .role-option.selected, .role-option:has(input:checked) { border-color: #F65F54; background: #2d1a19; }
        .role-option strong { display: block; font-size: 15px; color: #fff; margin-bottom: 4px; }
        .role-option span { font-size: 12px; color: #888; }
        .btn-primary {
            width: 100%;
            background: #F65F54;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 13px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: background .2s;
            margin-top: 8px;
        }
        .btn-primary:hover { background: #d94f45; }
        .btn-primary:disabled { background: #333; cursor: not-allowed; }
        .alert { padding: 12px 16px; border-radius: 6px; font-size: 14px; margin-bottom: 16px; }
        .alert-error { background: #2d1515; border: 1px solid #6b2020; color: #f87171; }
        .alert-success { background: #152d1a; border: 1px solid #1e5c2d; color: #4ade80; }

        /* PAGE HEADING */
        .page-heading { margin-bottom: 28px; }
        .page-heading h1 { font-size: 28px; font-weight: 500; color: #fff; }
        .page-heading p { color: #888; font-size: 14px; margin-top: 4px; }

        /* POST CARD */
        .post-card { display: block; color: inherit; text-decoration: none; }
        .post-card h3 { font-size: 17px; font-weight: 500; color: #fff; margin-bottom: 6px; }
        .post-card .meta { font-size: 13px; color: #666; display: flex; gap: 16px; }
        .post-card .body-preview { font-size: 14px; color: #999; margin: 8px 0 12px; line-height: 1.5; }
        .post-card .stats { display: flex; gap: 16px; font-size: 13px; color: #666; }
        .post-card .stats span { display: flex; align-items: center; gap: 4px; }

        /* CREATOR GRID */
        .creator-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 16px; }
        .creator-card { text-align: center; padding: 28px 20px; text-decoration: none; color: inherit; display: block; }
        .creator-card:hover { border-color: #F65F54; }
        .creator-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: #2a2a2a;
            margin: 0 auto 14px;
            object-fit: cover;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: #555;
            overflow: hidden;
        }
        .creator-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .creator-name { font-size: 16px; font-weight: 500; color: #fff; margin-bottom: 6px; }
        .creator-bio { font-size: 13px; color: #888; line-height: 1.5; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 64px 20px; color: #555; }
        .empty-state h3 { font-size: 18px; margin-bottom: 8px; color: #777; }

        /* WRITE POST */
        .write-post textarea {
            width: 100%;
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 6px;
            color: #e0e0e0;
            padding: 12px 14px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            min-height: 80px;
            outline: none;
        }
        .write-post textarea:focus { border-color: #F65F54; }
        .write-post input[type=text] {
            width: 100%;
            background: #1e1e1e;
            border: 1px solid #333;
            border-radius: 6px;
            color: #e0e0e0;
            padding: 11px 14px;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            margin-bottom: 10px;
        }
        .write-post input[type=text]:focus { border-color: #F65F54; }
        .btn-sm {
            background: #F65F54;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
            transition: background .2s;
        }
        .btn-sm:hover { background: #d94f45; }

        /* PAGINATION */
        .pagination { display: flex; gap: 8px; justify-content: center; margin-top: 28px; }
        .pagination a {
            padding: 8px 14px;
            border-radius: 5px;
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
        }
        .pagination a:hover, .pagination a.current { background: #F65F54; border-color: #F65F54; color: #fff; }

        @media (max-width: 600px) {
            .hvn-nav-links a { padding: 6px 8px; font-size: 13px; }
            .creator-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        }
    </style>
    @yield('head')
</head>
<body>

<nav class="hvn-nav">
    {{-- Logo --}}
    <a href="/" class="hvn-logo">
        <img src="/storage/branding_media/BYnrmXBiztBYfakdtYol94onVywTZ2TfQDGCUYId.png" alt="Her Vision Network">
    </a>

    {{-- Search --}}
    <div class="hvn-search">
        <input type="text" placeholder="Search for movies, tv shows and people…"
               value="{{ request('query') }}"
               onkeydown="if(event.key==='Enter'&&this.value.trim()){window.location.href='/?query='+encodeURIComponent(this.value.trim());event.preventDefault();}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
    </div>

    {{-- Nav links --}}
    <div class="hvn-nav-links">
        <a href="/movies">Movies</a>
        <a href="/series">Series</a>
        <a href="/creators" class="{{ request()->is('creators*') ? 'active' : '' }}">Creators</a>
        <a href="/community" class="{{ request()->is('community*') ? 'active' : '' }}">Community</a>
        @auth
            @php $u = auth()->user(); @endphp
        @else
            <a href="/login">Sign In</a>
            <a href="/creator-signup" class="btn-accent">Join as Creator</a>
        @endauth
    </div>

    {{-- User chip (authenticated only) --}}
    @auth
        <div class="hvn-user">
            <div class="hvn-user-avatar">
                @if($u->avatar)
                    <img src="{{ $u->avatar }}" alt="{{ $u->username }}">
                @else
                    {{ strtoupper(substr($u->username ?? $u->email, 0, 1)) }}
                @endif
            </div>
            <span class="hvn-user-email">{{ $u->email }}</span>
            <span class="hvn-user-caret">▼</span>
            <div class="hvn-user-menu">
                @if($u->role === 'creator')
                    <a href="/creator/dashboard">Dashboard</a>
                @endif
                <a href="/account-settings">Settings</a>
                <form action="/logout" method="POST" style="margin:0">
                    @csrf
                    <button type="submit">Sign Out</button>
                </form>
            </div>
        </div>
    @endauth
</nav>

<main class="hvn-main">
    @yield('content')
</main>

@yield('scripts')
<script>
async function getXsrfToken() {
    await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
    return document.cookie.split(';').reduce(function(v, c) {
        var p = c.trim().split('=');
        return p[0] === 'XSRF-TOKEN' ? decodeURIComponent(p[1]) : v;
    }, '');
}
(function () {
    var toggle = document.querySelector('.hvn-user');
    var menu   = document.querySelector('.hvn-user-menu');
    if (!toggle || !menu) return;
    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });
    document.addEventListener('click', function () {
        menu.classList.remove('open');
    });
}());
</script>
</body>
</html>
