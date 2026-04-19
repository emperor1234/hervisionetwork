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
            background: #0f0f0f;
            color: #e0e0e0;
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
        }

        /* NAV */
        .hvn-nav {
            background: #161616;
            border-bottom: 1px solid #2a2a2a;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .hvn-logo {
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 3px;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .hvn-logo span { display: block; font-size: 9px; letter-spacing: 6px; font-weight: 300; }
        .hvn-nav-links { display: flex; gap: 8px; align-items: center; }
        .hvn-nav-links a {
            color: #aaa;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 14px;
            transition: color .2s, background .2s;
        }
        .hvn-nav-links a:hover, .hvn-nav-links a.active { color: #fff; background: #2a2a2a; }
        .hvn-nav-links a.btn-accent {
            background: #6c63ff;
            color: #fff;
            font-weight: 500;
        }
        .hvn-nav-links a.btn-accent:hover { background: #574fd6; }

        /* MAIN */
        .hvn-main { max-width: 900px; margin: 0 auto; padding: 32px 16px 64px; }

        /* CARD */
        .hvn-card {
            background: #1a1a1a;
            border: 1px solid #2a2a2a;
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
        .form-group input:focus, .form-group select:focus { border-color: #6c63ff; }
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
        .role-option.selected, .role-option:has(input:checked) { border-color: #6c63ff; background: #1e1b3a; }
        .role-option strong { display: block; font-size: 15px; color: #fff; margin-bottom: 4px; }
        .role-option span { font-size: 12px; color: #888; }
        .btn-primary {
            width: 100%;
            background: #6c63ff;
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
        .btn-primary:hover { background: #574fd6; }
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
        .creator-card:hover { border-color: #6c63ff; }
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
        .write-post textarea:focus { border-color: #6c63ff; }
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
        .write-post input[type=text]:focus { border-color: #6c63ff; }
        .btn-sm {
            background: #6c63ff;
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
        .btn-sm:hover { background: #574fd6; }

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
        .pagination a:hover, .pagination a.current { background: #6c63ff; border-color: #6c63ff; color: #fff; }

        @media (max-width: 600px) {
            .hvn-nav-links a { padding: 6px 8px; font-size: 13px; }
            .creator-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); }
        }
    </style>
    @yield('head')
</head>
<body>

<nav class="hvn-nav">
    <a href="/" class="hvn-logo">Her Vision<span>— Network —</span></a>
    <div class="hvn-nav-links">
        <a href="/">Home</a>
        <a href="/community" class="{{ request()->is('community*') ? 'active' : '' }}">Community</a>
        <a href="/creators" class="{{ request()->is('creators*') ? 'active' : '' }}">Creators</a>
        @auth
            @if(auth()->user()->role === 'creator')
                <a href="/creator/dashboard">Dashboard</a>
            @endif
            <a href="/" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
            <form id="logout-form" action="/logout" method="POST" style="display:none">
                @csrf
            </form>
        @else
            <a href="/login">Sign In</a>
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
