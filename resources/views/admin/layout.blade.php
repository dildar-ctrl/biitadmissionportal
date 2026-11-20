<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Barani Institute</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('css/biit-responsive.css') }}">

    {{-- ============================================= --}}
    {{-- SHARED ADMIN THEME CSS — edit here, applies everywhere --}}
    {{-- ============================================= --}}
    <style>
        :root {
            --primary: #0c7347;
            --primary-dark: #0a5f3b;
            --bg-soft: #f4f5fb;
            --border: #e1e3ee;
            --text-dark: #232342;
            --text-muted: #6c6f8a;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            background-color: var(--primary);
        }

        /* decorative purple strips on both sides + content shell, matching reference layout */
        .page-shell {
            margin-left: 130px;
            margin-right: 130px;
            min-height: 100vh;
            background-color: var(--bg-soft);
        }

        .page-body {
            padding: 28px;
        }

        @media (max-width: 1200px) {
            .page-shell {
                margin-right: 0;
            }
        }

        @media (max-width: 900px) {
            .page-shell {
                margin-left: 0;
            }
        }

        /* ---------- Section heading + pill dropdown ---------- */
        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-heading h4 {
            font-weight: 700;
            color: var(--text-dark);
            border-left: 4px solid var(--primary);
            padding-left: 12px;
            margin: 0;
        }

        .pill-select {
            appearance: none;
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 12px 40px 12px 20px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath fill='white' d='M0 0l6 8 6-8z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 18px center;
        }

        /* ---------- Stat cards ---------- */
        .stat-card {
            background-color: #fff;
            border-radius: 14px;
            padding: 22px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            height: 100%;
        }

        .stat-card .icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-card .value {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-card .label {
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        /* ---------- Panels (status / distribution / recent) ---------- */
        .card-panel {
            background-color: #fff;
            border-radius: 14px;
            border: 1px solid var(--border);
            padding: 22px;
            height: 100%;
        }

        .card-panel h6 {
            font-weight: 700;
            color: var(--text-dark);
            font-size: 0.95rem;
            padding-bottom: 14px;
            margin-bottom: 14px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-panel h6::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--primary);
            display: inline-block;
        }

        .status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f8;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .status-row:last-child {
            border-bottom: none;
        }

        .status-row .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .status-row .right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pct-pill {
            background-color: var(--bg-soft);
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 999px;
        }

        .recent-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #f3f4f8;
            text-decoration: none;
            color: var(--text-dark);
        }

        .recent-row:last-child {
            border-bottom: none;
        }

        .recent-row .icon-box {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background-color: var(--bg-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .recent-row .title {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .recent-row .subtitle {
            font-size: 0.78rem;
            color: var(--text-muted);
        }
    </style>

    @stack('styles')

    {{-- shared responsive fixes — must stay LAST so it can override page styles --}}
    <link rel="stylesheet" href="{{ asset('css/portal-responsive.css') }}">
</head>

<body>

    <div class="page-shell">
        @include('partials.header')

        <div class="page-body">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        @if(session('success'))
        Swal.fire({ title: 'Success!', text: '{{ session('success') }}', icon: 'success', timer: 3000, confirmButtonColor: '#3e4095' });
        @endif
        @if(session('error'))
        Swal.fire({ title: 'Error!', text: '{{ session('error') }}', icon: 'error', confirmButtonColor: '#d33' });
        @endif
    </script>

    @stack('scripts')
</body>

</html>