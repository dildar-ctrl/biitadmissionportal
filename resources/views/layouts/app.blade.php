<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - BIIT Admissions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet"<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - BIIT Admissions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

    <style>
        :root {
            --primary: #0c7347;
            --primary-light: #10a562;
            --primary-dark: #0a5f3b;
            --primary-soft: #e6f4ec;
            --border: #e8ebe9;
            --text-dark: #1f2937;
            --text-muted: #7c8a86;
            --bg-light: #f6f9f7;
            --bg-soft: #f0f4f2;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            color: var(--text-dark);
        }

        .stepper-title {
            text-align: center;
            color: var(--primary);
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 24px;
        }

        .dashboard-container { display: flex; min-height: 100vh; }

        /* -------- SIDEBAR -------- */
        .sidebar {
            width: 260px;
            background-color: #fff;
            position: fixed;
            height: 100vh;
            overflow: hidden;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
        }

        .top-section { position: relative; }

        .top-bg-fill {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 210px;
            background-color: #eef8f1;
            z-index: 0;
        }

        .top-wave-svg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 150px;
            z-index: 1;
        }

        .brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 34px 22px 0 22px;
        }

        .logo-badge {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
        }

        .logo-badge img { width: 100%; height: 100%; object-fit: contain; }

        .brand-text h1 {
            font-size: 14.5px;
            letter-spacing: 0.2px;
            color: #16743a;
            font-weight: 800;
            line-height: 1.25;
            white-space: nowrap;
            margin: 0;
        }

        .brand-text p {
            font-size: 10px;
            color: #8a988f;
            margin-top: 3px;
            white-space: nowrap;
        }

        .dashboard-slot { position: relative; z-index: 2; padding: 0 18px; margin-top: 98px; margin-bottom: 4px; }

        /* ============ NAV MENU ============ */
        .nav-menu {
            flex: 1;
            overflow-y: auto;
            padding: 0 18px 10px 18px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            border-radius: 14px;
            color: #3f4d45;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .nav-item i { width: 20px; text-align: center; margin-right: 14px; color: #279a4f; }
        .nav-item:hover:not(.active) { background: #f2f8f4; }

        .nav-item.active {
            background: linear-gradient(90deg, #279a4f 0%, #17753b 100%);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(23,117,59,0.28);
        }

        .nav-item.active i { color: #ffffff; }

        .nav-section-label {
            padding: 0 16px;
            margin-top: 14px;
            margin-bottom: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--text-muted);
        }

        .nav-divider {
            height: 1px;
            background-color: var(--border);
            margin: 10px 16px;
        }

        .nav-placeholder {
            padding: 12px 16px;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ============ BOTTOM SECTION ============ */
        .bottom-section {
            position: relative;
            margin-top: auto;
            padding: 20px 18px 20px 18px;
            min-height: 150px;
        }

        .bottom-bg-fill {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ffffff;
            z-index: 0;
        }

        .bottom-wave-svg {
            position: absolute;
            bottom: 0; left: 0;
            width: 100%; height: 120px;
            z-index: 1;
        }

        .user-info {
            position: relative;
            z-index: 2;
            font-size: 0.78rem;
            color: var(--text-muted);
            word-break: break-all;
            margin-bottom: 8px;
        }

        .sidebar-signout {
            position: relative;
            z-index: 2;
            width: 100%;
            margin-top: 60px;
            background-color: #fff;
            border: 1.5px solid var(--border);
            color: var(--text-muted);
            padding: 9px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .sidebar-signout:hover { background-color: #fef2f2; border-color: #fca5a5; color: #b91c1c; }

        /* -------- MAIN AREA -------- */
        .main-area { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }

        .topbar {
            background-color: #fff;
            border-bottom: 1px solid var(--border);
            padding: 14px 32px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .bell-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--bg-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            border: none;
            font-size: 1rem;
            cursor: pointer;
        }

        .main-content { flex: 1; padding: 28px 32px; }

        @media (max-width: 1024px) {
            .sidebar { width: 220px; }
            .main-area { margin-left: 220px; }
        }

        @media (max-width: 768px) {
            .dashboard-container { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-area { margin-left: 0; }
            .topbar { flex-wrap: wrap; }
        }

        @yield('styles')
    </style>
    @stack('styles')
</head>
<body class="admin-layout candidate-layout">

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="adminSidebar">

        <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Close menu"><i class="fas fa-times"></i></button>

        <div class="top-section">
            <div class="top-bg-fill"></div>
            <svg class="top-wave-svg" viewBox="0 0 260 260" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="swoosh" x1="260" y1="0" x2="15" y2="180" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#279653"/>
                        <stop offset="35%" stop-color="#6fc38c"/>
                        <stop offset="70%" stop-color="#bfe6cb"/>
                        <stop offset="100%" stop-color="#f6fbf7"/>
                    </linearGradient>
                </defs>
                <path d="
                    M0,168
                    C 55,152 85,150 122,143
                    C 160,136 187,130 214,105
                    C 237,80 250,40 260,0
                    L260,75
                    C 253,98 244,118 229,133
                    C 202,158 168,148 133,140
                    C 99,133 50,140 0,185
                    Z
                " fill="url(#swoosh)"/>
            </svg>

            <div class="brand">
                <div class="logo-badge"><img src="{{ asset('logo.png') }}" alt="BIIT Logo"></div>
                <div class="brand-text">
                    <h1>BIIT ADMISSIONS</h1>
                    <p>Candidate Portal</p>
                </div>
            </div>

            <div class="dashboard-slot">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="margin:0;">
                    <i class="fas fa-th"></i>
                    Dashboard
                </a>
            </div>
        </div>

        <nav class="nav-menu">
            <div class="nav-section-label">Application Flow</div>

            <a href="{{ route('apply.index') }}" class="nav-item {{ request()->routeIs('apply.index') || request()->routeIs('apply.store') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i>
                <span>1. Select Program</span>
            </a>

            <a href="{{ route('profile.edit') }}" class="nav-item {{ request()->routeIs('profile.*') && !request()->routeIs('profile.review') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>2. My Profile</span>
            </a>

            <a href="{{ route('document.edit') }}" class="nav-item {{ request()->routeIs('document.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i>
                <span>3. Documents</span>
            </a>

            @php
                $latestApp = auth()->user()->candidate?->applications()->latest('id')->first();
                $hasSessionSelection = session()->has('application.selected_programs');
            @endphp

            @if($latestApp)
                <div class="nav-divider"></div>

                <a href="{{ route('profile.review') }}" class="nav-item {{ request()->routeIs('profile.review') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i>
                    <span>Review Profile</span>
                </a>

                <a href="{{ route('apply.status') }}" class="nav-item {{ request()->routeIs('apply.status') || request()->routeIs('apply.download') ? 'active' : '' }}">
                    <i class="fas fa-download"></i>
                    <span>Application Status</span>
                </a>
            @elseif($hasSessionSelection)
                <div class="nav-divider"></div>

                <a href="{{ route('profile.review') }}" class="nav-item {{ request()->routeIs('profile.review') ? 'active' : '' }}">
                    <i class="fas fa-eye"></i>
                    <span>4. Review Profile</span>
                </a>

                <a href="{{ route('undertakings.show') }}" class="nav-item {{ request()->routeIs('undertakings.*') ? 'active' : '' }}">
                    <i class="fas fa-file-contract"></i>
                    <span>5. Undertakings</span>
                </a>
            @else
                <div class="nav-divider"></div>
                <div class="nav-placeholder">Steps 4 & 5 will appear after you select a program.</div>
            @endif
        </nav>

        <div class="bottom-section">
            <div class="bottom-bg-fill"></div>
            <svg class="bottom-wave-svg" viewBox="0 0 260 244" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="base" x1="0" y1="0" x2="0.85" y2="1">
                        <stop offset="0%" stop-color="#eaf8ee"/>
                        <stop offset="35%" stop-color="#9cddb0"/>
                        <stop offset="100%" stop-color="#3fae66"/>
                    </linearGradient>
                    <linearGradient id="hi" x1="0" y1="0" x2="1" y2="0.4">
                        <stop offset="0%" stop-color="#ffffff" stop-opacity="0.75"/>
                        <stop offset="100%" stop-color="#ffffff" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <path d="
                    M0,48
                    C 15,40 30,29 46,26
                    C 61,24 76,23 92,23
                    C 114,23 137,26 160,27
                    C 183,26 198,22 214,16
                    C 225,10 233,4 244,0
                    L260,0 L260,244 L0,244 Z
                " fill="url(#base)"/>
                <path d="
                    M0,48
                    C 15,40 30,29 46,26
                    C 61,24 76,23 92,23
                    C 114,23 137,26 160,27
                    C 183,26 198,22 214,16
                    C 225,10 233,4 244,0
                    L252,10
                    C 240,26 229,45 214,55
                    C 195,68 179,66 160,68
                    C 137,71 114,70 92,66
                    C 72,63 53,60 34,66
                    C 19,71 8,68 0,80
                    Z
                " fill="url(#hi)"/>
            </svg>

            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-signout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sign out
                </button>
            </form>
        </div>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- MAIN AREA -->
    <div class="main-area">
        <header class="topbar">
            <button type="button" class="menu-toggle" id="menuToggle" aria-label="Open menu" aria-controls="adminSidebar" aria-expanded="false"><i class="fas fa-bars"></i></button>
            <a class="topbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('logo.png') }}" alt="BIIT Logo">
                <span class="topbar-brand-text"><strong>BIIT ADMISSIONS</strong><small>Candidate Portal</small></span>
            </a>

            <div class="topbar-right">
                <button class="bell-btn"><i class="fas fa-bell"></i></button>
            </div>
        </header>

        <main class="main-content">
            @if(session('success'))
            <div style="background:#e7f5ee; border:1px solid #34d399; color:#0a5f3b; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:0.9rem; font-weight:600;">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="background:#fef2f2; border:1px solid #f87171; color:#b91c1c; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:0.9rem; font-weight:600;">
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mobile menu (drawer) - only visible below 992px, styled in responsive.css
    (function () {
        var body = document.body,
            toggle = document.getElementById('menuToggle'),
            sidebar = document.getElementById('adminSidebar'),
            backdrop = document.getElementById('sidebarBackdrop'),
            closeBtn = document.getElementById('sidebarClose');
        if (!toggle || !sidebar) return;
        function setOpen(open) {
            body.classList.toggle('sidebar-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        toggle.addEventListener('click', function () { setOpen(!body.classList.contains('sidebar-open')); });
        if (backdrop) backdrop.addEventListener('click', function () { setOpen(false); });
        if (closeBtn) closeBtn.addEventListener('click', function () { setOpen(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        sidebar.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { setOpen(false); }); });
        window.matchMedia('(min-width: 992px)').addEventListener('change', function (e) { if (e.matches) setOpen(false); });
    })();
</script>
@stack('scripts')
</body>
</html>