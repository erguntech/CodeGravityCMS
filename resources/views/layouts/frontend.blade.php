<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CodeGravity - Modern SaaS CMS Platform">
    
    <title>{{ $appName ?? 'CodeGravity' }} - @yield('title', __('Ana Sayfa'))</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('themes/backend/metronic/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('themes/backend/metronic/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('themes/backend/metronic/favicon/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('themes/backend/metronic/favicon/favicon.ico') }}">

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome (for icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link href="{{ asset('themes/frontend/css/style.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top cg-navbar" id="navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <span class="cg-logo-text">Code<span class="text-primary">Gravity</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cgNav" aria-controls="cgNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="cgNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#hero">{{ __('Ana Sayfa') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">{{ __('Özellikler') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">{{ __('Sistem') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">{{ __('İletişim') }}</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-4 cg-btn-glow">{{ __('Panele Git') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4 me-2">{{ __('Giriş Yap') }}</a>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="cg-footer pt-5 pb-4">
        <div class="container text-center">
            <p class="mb-0 text-muted">&copy; {{ date('Y') }} CodeGravity. {{ __('Tüm hakları saklıdır.') }}</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Navbar Scrolled Effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth Scroll
        document.querySelectorAll('a.nav-link[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
