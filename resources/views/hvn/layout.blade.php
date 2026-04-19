<!DOCTYPE html>
<html lang="en" class="be-dark-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Her Vision Network')</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        /* ── Reset ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── Matches Angular dark theme exactly (config/common/themes.php) ── */
        :root {
            --accent:      #F65F54;
            --accent-dark: #d44840;
            --bg:          #1D1D1D;
            --bg-alt:      #121212;
            --surface:     #242424;
            --surface2:    #333333;
            --divider:     rgba(255,255,255,0.12);
            --divider-lt:  rgba(255,255,255,0.06);
            --hover:       rgba(255,255,255,0.04);
            --text:        #ffffff;
            --text-2:      rgba(255,255,255,0.70);
            --text-hint:   rgba(255,255,255,0.50);
        }

        body {
            background: var(--bg-alt);
            color: var(--text);
            font-family: Roboto, 'Helvetica Neue', sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar — mirrors material-navbar height/style from Angular ── */
        .hvn-nav {
            background: var(--bg);
            border-bottom: 1px solid var(--divider-lt);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 57px;
            position: sticky;
            top: 0;
            z-index: 200;
        }

        .hvn-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text);
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .hvn-logo-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent); flex-shrink: 0;
        }

        .hvn-nav-links {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .hvn-nav-links a {
            color: var(--text-2);
            text-decoration: none;
            padding: 0 14px;
            height: 57px;
            display: inline-flex;
            align-items: center;
            font-size: 14px;
            font-weight: 400;
            letter-spacing: .01em;
            transition: color .15s, background .15s;
            position: relative;
        }
        .hvn-nav-links a:hover { color: var(--text); background: var(--hover); }
        .hvn-nav-links a.active { color: var(--text); }
        .hvn-nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2px;
            background: var(--accent);
        }
        .hvn-nav-links a.btn-accent {
            background: var(--accent);
            color: #fff;
            font-weight: 500;
            height: auto;
            padding: 7px 16px;
            border-radius: 4px;
            margin-left: 6px;
        }
        .hvn-nav-links a.btn-accent:hover { background: var(--accent-dark); }
        .hvn-nav-links a.btn-accent::after { display: none; }

        /* ── Main ── */
        .hvn-main { max-width: 900px; margin: 0 auto; padding: 32px 16px 64px; }

        /* ── Card ── */
        .hvn-card {
            background: var(--bg);
            border: 1px solid var(--divider);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        /* ── Form ── */
        .hvn-form { max-width: 440px; margin: 60px auto; }
        .hvn-form h1 { font-size: 24px; font-weight: 500; margin-bottom: 6px; }
        .hvn-form p.sub { color: var(--text-2); font-size: 14px; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; color: var(--text-2); margin-bottom: 6px; }
        .form-group input, .form-group select {
            width: 100%; background: var(--surface);
            border: 1px solid var(--divider); border-radius: 4px;
            color: var(--text); padding: 11px 14px; font-size: 14px;
            font-family: inherit; outline: none; transition: border-color .2s;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--accent); }

        .role-options { display: flex; gap: 12px; }
        .role-option {
            flex: 1; border: 2px solid var(--divider);
            border-radius: 8px; padding: 14px 12px;
            cursor: pointer; transition: border-color .2s, background .2s; text-align: center;
        }
        .role-option input[type=radio] { display: none; }
        .role-option.selected, .role-option:has(input:checked) {
            border-color: var(--accent);
            background: rgba(246,95,84,.08);
        }
        .role-option strong { display: block; font-size: 15px; color: var(--text); margin-bottom: 4px; }
        .role-option span { font-size: 12px; color: var(--text-hint); }

        .btn-primary {
            width: 100%; background: var(--accent); color: #fff;
            border: none; border-radius: 4px; padding: 13px;
            font-size: 15px; font-weight: 500; cursor: pointer;
            font-family: inherit; transition: background .2s; margin-top: 8px;
        }
        .btn-primary:hover { background: var(--accent-dark); }
        .btn-primary:disabled { background: var(--surface2); color: var(--text-hint); cursor: not-allowed; }

        /* ── Alerts ── */
        .alert { padding: 12px 16px; border-radius: 4px; font-size: 14px; margin-bottom: 16px; }
        .alert-error   { background: rgba(244,67,54,.1);  border: 1px solid rgba(244,67,54,.4);  color: #ef9a9a; }
        .alert-success { background: rgba(76,175,80,.1);  border: 1px solid rgba(76,175,80,.4);  color: #a5d6a7; }

        /* ── Btn-sm ── */
        .btn-sm {
            background: var(--accent); color: #fff; border: none;
            border-radius: 4px; padding: 8px 18px; font-size: 13px;
            font-weight: 500; cursor: pointer; font-family: inherit; transition: background .2s;
        }
        .btn-sm:hover { background: var(--accent-dark); }
        .btn-sm:disabled { background: var(--surface2); cursor: not-allowed; }

        /* ── Creator grid ── */
        .creator-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px,1fr)); gap: 16px; }
        .creator-card { text-align: center; padding: 28px 20px; text-decoration: none; color: inherit; display: block; }
        .creator-card:hover { border-color: var(--accent); }
        .creator-avatar {
            width: 80px; height: 80px; border-radius: 50%;
            background: var(--surface); margin: 0 auto 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px; color: var(--text-hint); overflow: hidden;
        }
        .creator-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .creator-name { font-size: 16px; font-weight: 500; color: var(--text); margin-bottom: 6px; }
        .creator-bio  { font-size: 13px; color: var(--text-2); line-height: 1.5; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 64px 20px; color: var(--text-hint); }
        .empty-state h3 { font-size: 18px; margin-bottom: 8px; color: var(--text-2); }

        /* ── Pagination ── */
        .pagination { display: flex; gap: 8px; justify-content: center; margin-top: 28px; }
        .pagination a {
            padding: 8px 14px; border-radius: 4px;
            background: var(--bg); border: 1px solid var(--divider);
            color: var(--text-2); text-decoration: none; font-size: 14px;
        }
        .pagination a:hover, .pagination a.current {
            background: var(--accent); border-color: var(--accent); color: #fff;
        }

        /* ── Responsive ── */
        @media (max-width: 600px) {
            .hvn-nav-links a { padding: 0 8px; font-size: 13px; }
            .creator-grid { grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); }
        }
        @media (max-width: 420px) {
            .hvn-nav-links a span.nav-label { display: none; }
        }
    </style>
    @yield('head')
</head>
<body>

<nav class="hvn-nav">
    <a href="/" class="hvn-logo">
        <span class="hvn-logo-dot"></span>
        <span>Her Vision Network</span>
    </a>
    <div class="hvn-nav-links">
        <a href="/community" class="{{ request()->is('community*') ? 'active' : '' }}">
            <span class="nav-label">Community</span>
        </a>
        <a href="/creators" class="{{ request()->is('creators*') ? 'active' : '' }}">
            <span class="nav-label">Creators</span>
        </a>
        @auth
            @if(auth()->user()->role === 'creator')
                <a href="/creator/dashboard"><span class="nav-label">Dashboard</span></a>
            @endif
            <a href="/" onclick="event.preventDefault(); document.getElementById('hvn-logout').submit();">
                <span class="nav-label">Sign Out</span>
            </a>
            <form id="hvn-logout" action="/logout" method="POST" style="display:none">@csrf</form>
        @else
            <a href="/login"><span class="nav-label">Sign In</span></a>
            <a href="/creator-signup" class="btn-accent">Join as Creator</a>
        @endauth
    </div>
</nav>

<main class="hvn-main">
    @yield('content')
</main>

@yield('scripts')
</body>
</html>
