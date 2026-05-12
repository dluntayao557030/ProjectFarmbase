<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmbase – @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-dark:    #2d5a1b;
            --green-mid:     #4D6619;
            --green-main:    #4e9a30;
            --green-light:   #6bbf42;
            --green-pale:    #e8f5e0;
            --green-border:  #b8dda0;
            --gold-dark:     #8a6200;
            --gold-mid:      #b08000;
            --gold-main:     #c8960a;
            --gold-light:    #e8b820;
            --gold-bg:       #7a5800;
            --gold-card:     #6b4f00;
            --cream:         #f7f9f3;

            --sidebar-w:     90px;
            --navbar-h:      64px;
            --text-dark:     #1a2e10;
            --text-mid:      #3a5228;
            --font:          'Inconsolata', monospace;
            --sidebar-speed: 0.28s;
        }

        html, body {
            height: 100%;
            font-family: var(--font);
            background: #eef4e8;
            overflow: hidden;
        }

        /* NAVBAR */
        .fb-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--navbar-h);
            background-image: url('/images/backgrounds/FarmbaseBackground.png');
            background-size: cover;
            display: flex;
            align-items: center;
            padding: 0 1.2rem;
            gap: 1rem;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(20,50,8,0.25);
            color: #fff;
        }

        .fb-navbar .left-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-shrink: 0;
        }

        .fb-navbar .barn-icon {
            height: 42px;
            object-fit: contain;
        }

        .fb-navbar .barn-name {
            font-size: 1.08rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 280px;
        }

        .fb-navbar .hamburger {
            background: none;
            border: none;
            color: rgba(255,255,255,0.9);
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0.35rem 0.55rem;
            border-radius: 6px;
            transition: all 0.2s;
            line-height: 1;
            flex-shrink: 0;
        }

        .fb-navbar .hamburger:hover { 
            background: rgba(255,255,255,0.18); 
        }

        .fb-navbar .right-section {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .fb-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            cursor: pointer;
        }

        .fb-navbar .user-avatar {
            width: 40px; 
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            border: 2px solid rgba(255,255,255,0.5);
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden;
        }

        .fb-navbar .user-name {
            font-size: 0.95rem;
            font-weight: 600;
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-logout {
            display: flex; 
            align-items: center; 
            gap: 0.4rem;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff; 
            border-radius: 6px;
            padding: 0.3rem 0.75rem;
            font-family: var(--font); 
            font-size: 0.82rem; 
            font-weight: 600;
            cursor: pointer; 
            transition: background 0.2s;
        }

        .btn-logout:hover { 
            background: rgba(255,255,255,0.25); 
        }

        .logout-icon { 
            width: 16px; 
            height: 16px; 
            mix-blend-mode: screen; 
            filter: brightness(2); 
        }

        /* SIDEBAR */
        .fb-sidebar {
            position: fixed;
            top: var(--navbar-h); left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--green-mid);
            display: flex; flex-direction: column;
            align-items: center;
            padding: 0.8rem 0;
            gap: 0.15rem;
            z-index: 90;
            box-shadow: 2px 0 10px rgba(20,50,8,0.22);
            overflow: hidden;
            transform: translateX(0);
            transition: transform var(--sidebar-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fb-sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-item {
            width: 100%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 0.9rem 0.5rem;
            cursor: pointer; text-decoration: none;
            color: rgba(255,255,255,0.65);
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
            gap: 5px;
        }

        .sidebar-item:hover  { background: rgba(255,255,255,0.09); color: #fff; }

        .sidebar-item.active {
            background: rgba(255,255,255,0.13);
            color: #fff;
            border-left-color: var(--green-light);
        }

        .sidebar-item .s-icon { 
            width: 30px; 
            height: 30px; 
            object-fit: contain; 
            flex-shrink: 0; 
        }

        .sidebar-item .s-icon-default  { display: block; }
        .sidebar-item .s-icon-active   { display: none; }
        .sidebar-item.active .s-icon-default,
        .sidebar-item:hover  .s-icon-default  { display: none; }
        .sidebar-item.active .s-icon-active,
        .sidebar-item:hover  .s-icon-active   { display: block; }

        .sidebar-item .s-label {
            font-family: var(--font);
            font-size: 0.63rem; 
            font-weight: 700;
            letter-spacing: 0.03em; 
            text-align: center; 
            line-height: 1.2;
        }

        /* MAIN CONTENT */
        .fb-main {
            position: fixed;
            top: var(--navbar-h);
            left: 0; right: 0; bottom: 0;
            overflow-y: auto;
            background: #eef4e8;
            margin-left: var(--sidebar-w);
            transition: margin-left var(--sidebar-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .fb-main {
            margin-left: 0;
        }

        .fb-hero {
            width: 100%; 
            height: 120px;
            position: relative; 
            overflow: hidden;
        }

        .fb-hero .hero-bg {
            position: absolute; 
            inset: 0;
            background: url('/images/backgrounds/BarnImage2.png') center 50%/cover no-repeat;
            filter: brightness(0.72);
        }

        .fb-hero .hero-overlay {
            position: absolute; 
            inset: 0;
            background: linear-gradient(90deg, rgba(20,50,8,0.55) 0%, rgba(20,50,8,0.2) 100%);
        }

        .fb-hero .hero-text {
            position: relative; 
            z-index: 2;
            height: 100%; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.8rem 1.8rem;
            font-family: var(--font);
            font-size: 1.5rem;
            font-weight: 700; 
            color: #ffffff;
            text-shadow: 0 1px 6px rgba(0,0,0,0.4);
            letter-spacing: 0.02em;
        }

        /* Responsive Hero Text */
        .hero-text-mobile { display: none; }
        @media (max-width: 768px) {
            .hero-text-desktop { display: none; }
            .hero-text-mobile { display: inline; }
        }

        .content-area {
            padding: 1.2rem 1.5rem 2rem;
            min-height: calc(100vh - var(--navbar-h) - 120px);
        }

        .flash-success {
            background: var(--green-pale);
            border: 1px solid var(--green-border);
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-size: 0.85rem; 
            color: var(--green-dark);
            margin-bottom: 1rem; 
            font-weight: 600;
        }

        /* ==================== MOBILE RESPONSIVENESS ==================== */
        @media (max-width: 768px) {
            :root {
                --navbar-h: 58px;
                --sidebar-w: 78px;
            }
            .fb-navbar {
                height: 58px;
                padding: 0 0.9rem;
                gap: 0.6rem;
            }
            .fb-navbar .barn-icon { height: 36px; }
            .fb-navbar .barn-name { font-size: 0.98rem; max-width: 180px; }
            .fb-navbar .user-avatar { width: 34px; height: 34px; }
            .fb-navbar .user-name { display: none; }
            .btn-logout {
                padding: 0.25rem 0.65rem;
                font-size: 0.78rem;
            }
            .fb-hero { height: 110px; }
            .fb-hero .hero-text {
                font-size: 1.25rem;
                padding: 1rem;
            }
            .content-area {
                padding: 1rem 1.1rem 1.8rem;
            }
            .sidebar-item .s-label {
                font-size: 0.58rem;
            }
        }

        @media (max-width: 576px) {
            .fb-navbar .hamburger { font-size: 1.4rem; padding: 0.4rem; }
            .fb-hero .hero-text { font-size: 1.15rem; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="fb-navbar">
    <div class="left-section">
        <img src="/images/logos/FarmbaseLogoOnly.png" alt="Barn" class="barn-icon">
        <div class="barn-name">
            {{ $currentBarn->barn_name ?? 'No Barn Selected' }}
        </div>

        <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" title="Toggle sidebar">
            ☰
        </button>
    </div>

    <div class="right-section">
        <div class="user-info">
            <img src="/images/icons/Owner.png" alt="Profile" class="user-avatar">
            <div class="user-name">
                {{ $currentUser->first_name ?? '' }} {{ $currentUser->last_name ?? 'Guest' }}
            </div>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn-logout">
                <img src="/images/icons/Logout.png" class="logout-icon" alt="">
                Logout
            </button>
        </form>
    </div>
</nav>

<!-- SIDEBAR -->
<aside class="fb-sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <img src="/images/icons/Dashboard.png" class="s-icon s-icon-default" alt="">
        <img src="/images/icons/DashboardClicked.png" class="s-icon s-icon-active" alt="">
        <span class="s-label">Dashboard</span>
    </a>

    <a href="{{ route('inventory.index') }}" class="sidebar-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
        <img src="/images/icons/Inventory.png" class="s-icon s-icon-default" alt="">
        <img src="/images/icons/InventoryClicked.png" class="s-icon s-icon-active" alt="">
        <span class="s-label">Supplies</span>
    </a>

    <a href="{{ route('suppliers.index') }}" class="sidebar-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
        <img src="/images/icons/Suppliers.png" class="s-icon s-icon-default" alt="">
        <img src="/images/icons/SuppliersClicked.png" class="s-icon s-icon-active" alt="">
        <span class="s-label">Suppliers</span>
    </a>

    <a href="{{ route('staffs.index') }}" class="sidebar-item {{ request()->routeIs('staffs.*') ? 'active' : '' }}">
        <img src="/images/icons/Staffs.png" class="s-icon s-icon-default" alt="">
        <img src="/images/icons/StaffsClicked.png" class="s-icon s-icon-active" alt="">
        <span class="s-label">Staffs</span>
    </a>

    <a href="{{ route('reports.index') }}" class="sidebar-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <img src="/images/icons/Reports.png" class="s-icon s-icon-default" alt="">
        <img src="/images/icons/ReportsClicked.png" class="s-icon s-icon-active" alt="">
        <span class="s-label">Reports</span>
    </a>
</aside>

<!-- MAIN CONTENT -->
<main class="fb-main" id="fbMain">
    <div class="fb-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <span class="hero-text-desktop">
                @yield('hero-text', 'Welcome to Farmbase.')
            </span>
            <span class="hero-text-mobile">
                @yield('hero-text-mobile', '📊 Farmbase – ' . ($currentUser->first_name ?? 'Admin'))
            </span>
        </div>
    </div>

    <div class="content-area">
        @if(session('success'))
            <div class="flash-success">✅ {{ session('success') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>

<script>
    const sidebar = document.getElementById('sidebar');
    const hamburger = document.getElementById('hamburgerBtn');
    const STORE_KEY = 'fb_sidebar_open';

    function toggleSidebar() {
        const willCollapse = !sidebar.classList.contains('collapsed');
        
        sidebar.classList.toggle('collapsed', willCollapse);
        document.body.classList.toggle('sidebar-collapsed', willCollapse);
        
        hamburger.textContent = willCollapse ? '☰' : '✕';

        localStorage.setItem(STORE_KEY, willCollapse ? '0' : '1');
    }

    (function init() {
        const saved = localStorage.getItem(STORE_KEY);
        if (saved === '0') {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            hamburger.textContent = '☰';
        }
    })();
</script>

<!-- PERMANENT MODAL FIX: root container and relocation script -->
<div id="modal-root"></div>
<script>
    (function relocateModals() {
        const modalRoot = document.getElementById('modal-root');
        if (!modalRoot) return;
        const modals = document.querySelectorAll('.fb-modal-backdrop, .kpi-modal-backdrop, .modal-backdrop');
        modals.forEach(modal => {
            if (modal.parentNode !== modalRoot) {
                modalRoot.appendChild(modal);
            }
        });
    })();
</script>

@stack('scripts')
</body>
</html>