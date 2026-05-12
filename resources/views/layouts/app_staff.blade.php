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

            --navbar-h:      64px;
            --text-dark:     #1a2e10;
            --text-mid:      #3a5228;
            --font:          'Inconsolata', monospace;
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
            padding: 0 1rem;
            gap: 0.75rem;
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
            max-width: 220px;
        }

        .fb-navbar .right-section {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        .fb-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .fb-navbar .user-avatar {
            width: 38px; 
            height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            border: 2px solid rgba(255,255,255,0.5);
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .fb-navbar .user-name {
            font-size: 0.92rem;
            font-weight: 600;
            max-width: 130px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-logout {
            display: flex; 
            align-items: center; 
            gap: 0.35rem;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff; 
            border-radius: 6px;
            padding: 0.35rem 0.7rem;
            font-family: var(--font); 
            font-size: 0.8rem; 
            font-weight: 600;
            cursor: pointer; 
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-logout:hover { 
            background: rgba(255,255,255,0.25); 
        }

        .logout-icon { 
            width: 15px; 
            height: 15px; 
            mix-blend-mode: screen; 
            filter: brightness(2); 
        }

        /* HERO SECTION */
        .fb-hero {
            width: 100%; 
            height: 135px;
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
            background: linear-gradient(rgba(20,50,8,0.70), rgba(20,50,8,0.50));
        }

        .fb-hero .hero-text {
            position: relative; 
            z-index: 2;
            height: 100%; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 1.2rem;
            font-family: var(--font);
            font-size: 1.4rem;
            font-weight: 700; 
            color: #ffffff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.65);
            letter-spacing: 0.01em;
            text-align: center;
            line-height: 1.35;
            width: 100%;
        }

        /* Mobile Short Hero Text */
        .hero-text-desktop {
            display: block;
        }
        .hero-text-mobile {
            display: none;
        }

        /* MAIN CONTENT */
        .fb-main {
            position: fixed;
            top: var(--navbar-h);
            left: 0; right: 0; bottom: 0;
            overflow-y: auto;
            background: #eef4e8;
        }

        .content-area {
            padding: 1.2rem 1.5rem 2rem;
            min-height: calc(100vh - var(--navbar-h) - 135px);
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
        @media (max-width: 576px) {
            .fb-navbar {
                height: 58px;
                padding: 0 0.85rem;
                gap: 0.5rem;
            }

            .fb-navbar .barn-icon { height: 36px; }
            .fb-navbar .barn-name { font-size: 0.97rem; max-width: 165px; }
            .fb-navbar .user-avatar { width: 34px; height: 34px; }
            .fb-navbar .user-name { display: none; }

            .btn-logout {
                padding: 0.32rem 0.65rem;
                font-size: 0.79rem;
                gap: 0.3rem;
            }
            .logout-icon { width: 14px; height: 14px; }

            .fb-hero {
                height: 115px;
            }

            .fb-hero .hero-text {
                font-size: 1.22rem;
                padding: 0.8rem 1rem;
                line-height: 1.4;
            }

            /* Show short version on mobile */
            .hero-text-desktop {
                display: none;
            }
            .hero-text-mobile {
                display: block;
            }

            .content-area {
                padding: 0.9rem 1rem 1.8rem;
            }
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
    </div>

    <div class="right-section">
        <div class="user-info">
            <img src="/images/icons/Staff.png" alt="Profile" class="user-avatar">
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

<!-- MAIN CONTENT -->
<main class="fb-main" id="fbMain">
    <div class="fb-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="hero-text">
            <!-- Desktop / Tablet version (full message) -->
            <span class="hero-text-desktop">
                @yield('hero-text', 'Welcome to Farmbase.')
            </span>
            
            <!-- Mobile short version -->
            <span class="hero-text-mobile">
                Let's get to work, {{ $currentUser->first_name ?? 'Staff' }} 🌾
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