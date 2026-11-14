<!-- {{--
    Shared Header — include on every module with: @include('partials.header')
    Works for both guards: admin (auth:admin) and candidate (auth:web)
--}}
@php
    $isAdmin = auth('admin')->check();
    $isCandidate = auth('web')->check();

    $currentUser = $isAdmin ? auth('admin')->user() : (auth('web')->user() ?? null);
    $firstName = $currentUser->fname ?? 'User';

    $dashboardLink = $isAdmin ? route('admin.dashboard') : route('dashboard');
@endphp

<header class="topbar">
    <div class="topbar-left">
        <a href="{{ $dashboardLink }}">
            <img src="{{ asset('logo.png') }}" alt="Logo">
        </a>
    </div>

    <div class="topbar-title">
    Barani Institute Admission Portal
    </div>

    <div class="user-menu" id="userMenu">
        <button class="user-menu-btn" id="userMenuBtn" type="button">
            {{ $firstName }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </button>

        <div class="user-menu-dropdown">
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:6px;">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Dashboard
                </a>

               

               

               

                <a href="{{ route('admin.applications.index') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:6px;">
                        <path d="M9 11H3v10h6V11Z"/><path d="M14 3h6v18h-6V3Z"/><path d="M9 11V3h5v8"/>
                    </svg>
                    Applications
                </a>

                

                <a href="#">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:6px;">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Profile Settings
                </a>
            @else
                <a href="{{ route('dashboard') }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:6px;">
                        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    Dashboard
                </a>
            @endif

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:#dc2626;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:6px;">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Logout
            </a>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</header>

<style>
.topbar {
    position: sticky;
    top: 0;
    height: 78px;
    background: #fff;
    border-bottom: 1px solid var(--border, #e1e3ee);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    cursor: default;
    user-select: none;
    width: 100%;
    z-index: 900;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.topbar-left img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.topbar-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: var(--primary, #0c7347);
    font-size: 26px;
    font-weight: 700;
    white-space: nowrap;
}

.user-menu {
    position: relative;
}

.user-menu-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 1px solid var(--border, #e1e3ee);
    border-radius: 8px;
    padding: 8px 14px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-dark, #232342);
    transition: border-color .2s;
}

.user-menu-btn:hover {
    border-color: var(--primary, #0c7347);
}

.user-menu-btn svg {
    width: 14px;
    height: 14px;
    color: #6c6f8a;
}

.user-menu-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: calc(100% + 8px);
    min-width: 190px;
    background: #fff;
    border: 1px solid var(--border, #e1e3ee);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(30,30,80,.12);
    z-index: 1000;
}

.user-menu.open .user-menu-dropdown {
    display: block;
}

.user-menu-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-dark, #232342);
    transition: background .15s;
    border-bottom: 1px solid #f3f4f8;
}

.user-menu-dropdown a:last-child {
    border-bottom: none;
}

.user-menu-dropdown a:hover {
    background: #f5f6fb;
    color: var(--primary, #0c7347);
}

.user-menu-dropdown a svg {
    flex-shrink: 0;
    color: #6c6f8a;
}

.user-menu-dropdown a:hover svg {
    color: var(--primary, #0c7347);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userMenu    = document.getElementById('userMenu');
    const userMenuBtn = document.getElementById('userMenuBtn');

    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('open');
        });
        document.addEventListener('click', function () {
            userMenu.classList.remove('open');
        });
    }
});
</script> -->