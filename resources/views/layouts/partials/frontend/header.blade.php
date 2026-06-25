    <header class="header sticky-bar">
        <div class="container">
            <div class="main-header">
                <div class="header-left">
                    <div class="header-logo">
                        <a href="{{ url('/') }}" class="d-flex"><img alt="jobhub" src="{{ asset('themes/frontend/assets/imgs/theme/jobhub-logo.svg') }}" /></a>
                    </div>
                    <div class="header-nav">
                        <nav class="nav-main-menu d-none d-xl-block">
                            <ul class="main-menu">
                                <li class="has-children">
                                    <a class="active" href="{{ url('/') }}">{{ __('Ana Sayfa') }}</a>
                                </li>
                                <li class="has-children">
                                    <a href="{{ url('/?type=model') }}">{{ __('Modeller') }}</a>
                                </li>
                                <li class="has-children">
                                    <a href="{{ url('/?type=agency') }}">{{ __('Ajanslar') }}</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="burger-icon burger-icon-white">
                            <span class="burger-icon-top"></span>
                            <span class="burger-icon-mid"></span>
                            <span class="burger-icon-bottom"></span>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="block-signin">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-default btn-shadow ml-40 hover-up">{{ __('Yönetim Paneli') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-default btn-shadow ml-40 hover-up">{{ __('Giriş Yap') }}</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-link-bd-btom hover-up ml-20">{{ __('Kayıt Ol') }}</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="mobile-header-active mobile-header-wrapper-style perfect-scrollbar">
        <div class="mobile-header-wrapper-inner">
            <div class="mobile-header-top">
                <div class="user-account">
                    <img src="{{ asset('themes/frontend/assets/imgs/avatar/ava_1.png') }}" alt="jobhub" />
                    <div class="content">
                        <h6 class="user-name">{{ __('Hoş Geldiniz') }}</h6>
                        <p class="font-xs text-muted">{{ __('Seçkin Rehber') }}</p>
                    </div>
                </div>
                <div class="burger-icon burger-icon-white">
                    <span class="burger-icon-top"></span>
                    <span class="burger-icon-mid"></span>
                    <span class="burger-icon-bottom"></span>
                </div>
            </div>
            <div class="mobile-header-content-area">
                <div class="perfect-scroll">
                    <div class="mobile-search mobile-header-border mb-30">
                        <form action="{{ url('/') }}" method="GET">
                            <input type="text" name="search" placeholder="{{ __('Ara...') }}" />
                            <i class="fi-rr-search"></i>
                        </form>
                    </div>
                    <div class="mobile-menu-wrap mobile-header-border">
                        <!-- mobile menu start -->
                        <nav>
                            <ul class="mobile-menu font-heading">
                                <li><a class="active" href="{{ url('/') }}">{{ __('Ana Sayfa') }}</a></li>
                                <li><a href="{{ url('/?type=model') }}">{{ __('Modeller') }}</a></li>
                                <li><a href="{{ url('/?type=agency') }}">{{ __('Ajanslar') }}</a></li>
                            </ul>
                        </nav>
                        <!-- mobile menu end -->
                    </div>
                    <div class="mobile-account">
                        <h6 class="mb-10">{{ __('Hesap') }}</h6>
                        <ul class="mobile-menu font-heading">
                            @auth
                                <li><a href="{{ route('dashboard') }}">{{ __('Yönetim Paneli') }}</a></li>
                            @else
                                <li><a href="{{ route('login') }}">{{ __('Giriş Yap') }}</a></li>
                                @if (Route::has('register'))
                                    <li><a href="{{ route('register') }}">{{ __('Kayıt Ol') }}</a></li>
                                @endif
                            @endauth
                        </ul>
                    </div>
                    <div class="site-copyright">{{ __('Copyright 2026 © PRIVE. Tüm Hakları Saklıdır.') }}</div>
                </div>
            </div>
        </div>
    </div>
