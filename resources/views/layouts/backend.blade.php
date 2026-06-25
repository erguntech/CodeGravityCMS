<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<!--begin::Head-->

<head>
	<title>{{ $appName ?? 'CodeGravity' }} - @yield('title', __('Genel Bakış'))</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('themes/backend/metronic/favicon/apple-touch-icon.png') }}">
	<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('themes/backend/metronic/favicon/favicon-32x32.png') }}">
	<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('themes/backend/metronic/favicon/favicon-16x16.png') }}">
	<link rel="shortcut icon" href="{{ asset('themes/backend/metronic/favicon/favicon.ico') }}">
	<!--begin::Fonts(mandatory for all pages)-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	<!--end::Fonts-->
	<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
	<link href="{{ asset('themes/backend/metronic/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet"
		type="text/css" />
	<link href="{{ asset('themes/backend/metronic/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
	<!--end::Global Stylesheets Bundle-->
	@stack('styles')
    <style>
        .glitch-wrapper {
            position: relative;
            display: inline-block;
        }
        .glitch-img {
            animation: horror-flicker 10s infinite;
        }
        .glitch-wrapper::before,
        .glitch-wrapper::after {
            content: "";
            background-image: url("{{ asset('themes/backend/metronic/assets/media/logos/default-dark.svg') }}");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: left center;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
        }
        .glitch-wrapper::before {
            left: 3px;
            animation: horror-glitch-1 6s infinite linear alternate-reverse;
            filter: drop-shadow(-3px 0 0 rgba(180,0,0,0.8));
        }
        .glitch-wrapper::after {
            left: -3px;
            animation: horror-glitch-2 8s infinite linear alternate-reverse;
            filter: drop-shadow(3px 0 0 rgba(150,150,150,0.8));
        }

        @keyframes horror-flicker {
            0%, 90%, 94%, 98%, 100% { opacity: 1; filter: grayscale(0%) contrast(100%); transform: skewX(0deg); }
            92% { opacity: 0.8; filter: grayscale(50%) contrast(150%); transform: skewX(3deg); }
            96% { opacity: 0.4; filter: grayscale(80%) contrast(200%); transform: skewX(-3deg); }
        }

        @keyframes horror-glitch-1 {
          0%, 80% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
          81% { opacity: 1; clip-path: inset(10% 0 80% 0); transform: translate(-5px, 2px); }
          82% { opacity: 1; clip-path: inset(80% 0 5% 0); transform: translate(5px, -2px); }
          83% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
          
          92% { opacity: 1; clip-path: inset(40% 0 40% 0); transform: translate(-3px, 1px); }
          93% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
        }
        @keyframes horror-glitch-2 {
          0%, 75% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
          76% { opacity: 0.8; clip-path: inset(20% 0 60% 0); transform: translate(4px, -1px); }
          77% { opacity: 1; clip-path: inset(60% 0 20% 0); transform: translate(-4px, 1px); }
          78% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
          
          88% { opacity: 1; clip-path: inset(10% 0 70% 0); transform: translate(3px, 3px); }
          89% { opacity: 0; clip-path: inset(50% 0 50% 0); transform: translate(0, 0); }
        }

        /* Sidebar Footer Minimize Toggle */
        [data-kt-app-sidebar-minimize="on"] #kt_app_sidebar:not(:hover) #kt_app_sidebar_footer .app-sidebar-logo-default {
            display: none !important;
        }
        #kt_app_sidebar_footer .app-sidebar-logo-minimize {
            display: none !important;
        }
        [data-kt-app-sidebar-minimize="on"] #kt_app_sidebar:not(:hover) #kt_app_sidebar_footer .app-sidebar-logo-minimize {
            display: block !important;
        }

        textarea {
            resize: none !important;
        }
        .form-control.is-invalid,
        .form-select.is-invalid {
            border: 1px solid #f1416c !important;
        }
        .error-message {
            margin-top: 8px !important;
            display: block;
        }
        /* Sidebar spacing reduction */
        #kt_app_sidebar_menu .menu-item .menu-link {
            padding-top: 0.35rem !important;
            padding-bottom: 0.35rem !important;
        }
        /* Content Background */
        #kt_app_main {
            background-image: url('{{ asset('themes/backend/metronic/assets/media/backgrounds/bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            
        }
        .is-invalid + .select2-container .select2-selection,
        .is-invalid + .select2-container--bootstrap5 .select2-selection,
        .is-invalid + .select2-container .select2-selection--single,
        .is-invalid + .select2-container .select2-selection--multiple {
            border: 1px solid #f1416c !important;
        }
        /* Hide Select2 before initialization to prevent FOUC */
        select[data-control="select2"] {
            opacity: 0;
        }
        .select2-hidden-accessible {
            display: none;
        }
        /* DataTable Pagination Separator */
        .dataTables_wrapper .row:last-child {
            border-top: 1px dashed #E4E6EF;
            margin-top: 20px !important;
            padding-top: 20px !important;
            margin-left: -2.25rem !important;
            margin-right: -2.25rem !important;
            padding-left: 2.25rem;
            padding-right: 2.25rem;
        }
        /* Select2 Multiple Choice Styling */
        .select2-container--bootstrap5 .select2-selection--multiple .select2-selection__choice {
            font-size: 1rem !important;
            font-weight: 500 !important;
            display: inline-flex !important;
            align-items: center !important;
            padding: 2px 8px !important;
            border-radius: 0.475rem !important;
        }
        .select2-selection__choice__display {
            font-size: 1rem !important;
            margin-left: 1.3rem !important;
        }
        .select2-container--bootstrap5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
            margin-right: 6px !important;
            display: flex !important;
            align-items: center !important;
            width: 12px !important;
            height: 12px !important;
        }
        /* Tagify vertical alignment fix */
        .tagify__input {
            line-height: 1.5 !important;
            padding: 0.4rem 0.5rem !important;
            margin-top: auto !important; margin-bottom: 0 !important; margin-right: 0 !important;
        }
        .tagify {
            display: flex !important;
            align-items: center !important;
        }
        /* Custom styling for disabled inputs */
        .form-control:disabled, 
        .form-control[disabled],
        .form-control-solid:disabled,
        .form-control-solid[disabled] {
            background-color: #151521 !important;
            opacity: 0.7;
            cursor: not-allowed;
        }
        /* Force footer alignment to the absolute left of the screen */
        #kt_app_footer, 
        .app-footer {
            display: flex !important;
            justify-content: flex-start !important;
            align-items: center !important;
            background: transparent !important;
            border-top: none !important;
            width: 100% !important;
            margin-top: auto !important; margin-bottom: 0 !important; margin-right: 0 !important;
            margin-left: 0 !important; /* Reset Metronic automatic sidebar offset */
            padding: 0 !important;
            padding-left: 0 !important; /* Reset any left padding */
            left: 0 !important; /* Force reset positioning left offset */
            right: auto !important;
            position: relative !important;
        }
        #kt_app_footer .container-fluid,
        .app-footer .container-fluid {
            display: flex !important;
            justify-content: flex-start !important;
            align-items: center !important;
            max-width: none !important;
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: auto !important;
            padding-left: 1.25rem !important; /* Mobile offset */
        }
        @media (min-width: 992px) {
            #kt_app_footer .container-fluid,
            .app-footer .container-fluid {
                padding-left: 2.25rem !important; /* Desktop offset matching card margin */
            }

        @media (max-width: 991.98px) {
            #kt_app_footer .container-fluid,
            .app-footer .container-fluid {
                justify-content: center !important;
                text-align: center !important;
                padding-left: 0 !important;
            }
            #kt_app_footer .text-gray-900,
            .app-footer .text-gray-900 {
                text-align: center !important;
                width: 100%;
            }
        }

        }
    
        /* Sidebar Icons Custom Color */
        .app-sidebar .menu-item .menu-link .menu-icon i,
        .app-sidebar .menu-item .menu-link .menu-icon i span {
            transition: color 1s ease !important;
        }

        .app-sidebar .menu-item .menu-link:hover .menu-icon i,
        .app-sidebar .menu-item .menu-link:hover .menu-icon i span {
            color: var(--bs-primary) !important;
            transition: color 0.2s ease !important;
        }

        .app-sidebar .menu-item.here > .menu-link .menu-icon i,
        .app-sidebar .menu-item.here > .menu-link .menu-icon i span,
        .app-sidebar .menu-item.show > .menu-link .menu-icon i,
        .app-sidebar .menu-item.show > .menu-link .menu-icon i span,
        .app-sidebar .menu-item .menu-link.active .menu-icon i,
        .app-sidebar .menu-item .menu-link.active .menu-icon i span {
            color: var(--bs-primary) !important;
            transition: none !important;
        }
</style>
	<script>document.documentElement.setAttribute("data-bs-theme", "dark");</script>
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true"
	data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
	data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
	data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="false" class="app-default">
	<!--begin::App-->
	<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
		<!--begin::Page-->
		<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
			<!--begin::Header-->
			<div id="kt_app_header" class="app-header" data-kt-sticky="true"
				data-kt-sticky-activate="{default: true, lg: true}" data-kt-sticky-name="app-header-minimize"
				data-kt-sticky-offset="{default: '200px', lg: '0'}" data-kt-sticky-animation="false">
				<!--begin::Header container-->
				<div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
					id="kt_app_header_container">
					<!--begin::Sidebar mobile toggle-->
					<div class="d-flex align-items-center d-lg-none ms-n3 me-1 me-md-2" title="Show sidebar menu">
						<div class="btn btn-icon btn-active-color-primary w-35px h-35px"
							id="kt_app_sidebar_mobile_toggle">
							<i class="ki-duotone ki-abstract-14 fs-2 fs-md-1">
								<span class="path1"></span><span class="path2"></span>
							</i>
						</div>
					</div>
					<!--end::Sidebar mobile toggle-->
					<!--begin::Mobile logo-->
					<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
						<a href="{{ route('dashboard') }}" class="d-lg-none">
							<img alt="Logo"
								src="{{ asset('themes/backend/metronic/assets/media/logos/default-small.svg') }}"
								style="height: 36px !important;" />
						</a>
					</div>
					<!--end::Mobile logo-->
					<!--begin::Header wrapper-->
					<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
						id="kt_app_header_wrapper">
						<!--begin::Page title-->
						<div class="app-header-menu align-items-stretch">
							<!--begin::Menu holder-->
							<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
								id="kt_app_header_menu" data-kt-menu="true">
								<div class="d-flex align-items-center h-lg-100 ms-5">
									@yield('page_title')
								</div>
							</div>
							<!--end::Menu holder-->
						</div>
						<!--end::Page title-->
						<div class="app-navbar flex-shrink-0">





                            <!--begin::User menu-->
                            <div class="app-navbar-item ms-1 ms-md-4" id="kt_header_user_menu_toggle">
                                <!--begin::Menu wrapper-->
                                <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="user" />
                                </div>
                                <!--begin::User account menu-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-2 fs-6 w-300px" data-kt-menu="true">
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content d-flex align-items-center px-3">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-50px me-5">
                                                <img alt="Logo" src="{{ auth()->user()->avatar_url }}" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Username-->
                                            <div class="d-flex flex-column">
                                                <div class="fw-bold d-flex align-items-center fs-5">{{ auth()->user()->name }}
                                                <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2">{{ __(auth()->user()->user_type) }}</span></div>
                                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                                            </div>
                                            <!--end::Username-->
                                        </div>
                                    </div>
                                    <!--end::Menu item-->
                                    <div class="separator my-2"></div>
                                    <!--begin::Menu items-->
                                    <div class="menu-item px-5 mb-0">
                                        <a href="{{ route('profile.edit') }}" class="menu-link px-5 py-1" style="min-height: auto;">{{ __('Şifre Değiştir') }}</a>
                                    </div>
                                    <div class="menu-item px-5 mt-0 mb-1">
                                        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();" class="menu-link px-5 py-1 text-danger" style="min-height: auto;">{{ __('Çıkış Yap') }}</a>
                                    </div>
                                    <!--end::Menu items-->
                                </div>
                                <!--end::User account menu-->
                                <!--end::Menu wrapper-->
                            </div>
                            <!--end::User menu-->
						</div>
					</div>
					<!--end::Header wrapper-->
				</div>
				<!--end::Header container-->
			</div>
			<!--end::Header-->
			<!--begin::Wrapper-->
			<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
				<!--begin::Sidebar-->
				<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true"
					data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}"
					data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start"
					data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
					<!--begin::Logo-->
					<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
						<!--begin::Logo image-->
						<a href="{{ route('dashboard') }}">
							<div class="app-sidebar-logo-default">
								<div class="glitch-wrapper" style="height:30px;">
									<img alt="Logo"
										src="{{ asset('themes/backend/metronic/assets/media/logos/default-dark.svg') }}"
										style="height: 30px !important;" class="glitch-img" />
								</div>
							</div>
							<img alt="Logo"
								src="{{ asset('themes/backend/metronic/assets/media/logos/default-small.svg') }}"
								style="height: 24px !important;" class="app-sidebar-logo-minimize" />
						</a>
						<!--end::Logo image-->
						<!--begin::Sidebar toggle-->
						<div id="kt_app_sidebar_toggle"
							class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate"
							data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
							data-kt-toggle-name="app-sidebar-minimize">
							<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
								<span class="path1"></span><span class="path2"></span>
							</i>
						</div>
						<!--end::Sidebar toggle-->
					</div>
					<!--end::Logo-->
					<!--begin::sidebar menu-->
					<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
						<!--begin::Menu wrapper-->
						<div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper mb-5" style="margin-top: 23px !important;">
							<!--begin::Menu-->
							<div class="menu menu-column menu-rounded menu-sub-indention px-3" id="kt_app_sidebar_menu"
								data-kt-menu="true" data-kt-menu-expand="false">
								<!--begin:Menu item-->
								<div class="menu-item">
									<!--begin:Menu link-->
									<a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
										<span class="menu-icon">
											<i class="ki-duotone ki-element-11 fs-2">
												<span class="path1"></span><span class="path2"></span><span
													class="path3"></span><span class="path4"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Genel Bakış') }}</span>
									</a>
									<!--end:Menu link-->
								</div>
								<!--end:Menu item-->
								@canany(['users.view', 'roles.view', 'permissions.view', 'logs.view'])
								<!--begin:Menu item-->
								<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs($routePrefix . 'users.*') || request()->routeIs($routePrefix . 'roles.*') || request()->routeIs($routePrefix . 'permissions.*') || request()->routeIs($routePrefix . 'logs.*') ? 'here show' : '' }}">
									<!--begin:Menu link-->
									<span class="menu-link">
										<span class="menu-icon">
											<i class="ki-duotone ki-user fs-2">
												<span class="path1"></span><span class="path2"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Kullanıcı Yönetimi') }}</span>
										<span class="menu-arrow"></span>
									</span>
									<!--end:Menu link-->
									<!--begin:Menu sub-->
									<div class="menu-sub menu-sub-accordion">
										@can('users.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'users.*') ? 'active' : '' }}" href="{{ route($routePrefix . 'users.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Kullanıcılar') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan
										@can('roles.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'roles.*') ? 'active' : '' }}" href="{{ route($routePrefix . 'roles.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Kullanıcı Rolleri') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan
										@can('permissions.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'permissions.*') ? 'active' : '' }}" href="{{ route($routePrefix . 'permissions.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Kullanıcı İzinleri') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan
										@can('logs.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'logs.*') ? 'active' : '' }}" href="{{ route($routePrefix . 'logs.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Log Kayıtları') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan
									</div>
									<!--end:Menu sub-->
								</div>
								<!--end:Menu item-->
								@endcanany
								@can('clients.view')
								<!--begin:Menu item-->
								<div class="menu-item">
									<!--begin:Menu link-->
									<a class="menu-link {{ request()->routeIs($routePrefix . 'clients.*') ? 'active' : '' }}" href="{{ route($routePrefix . 'clients.index') }}">
										<span class="menu-icon">
											<i class="ki-duotone ki-address-book fs-2">
												<span class="path1"></span><span class="path2"></span><span class="path3"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Müşteriler') }}</span>
									</a>
									<!--end:Menu link-->
								</div>
								<!--end:Menu item-->
								@endcan



								@if(auth()->user()->user_type === 'Client')
									<!-- Client Modules -->
									@if(auth()->user()->hasModule('product_management'))
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('client.product-categories.*') || request()->routeIs('client.products.*') ? 'here show' : '' }}">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-parcel fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Ürün Yönetimi') }}</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
											<div class="menu-item">
												<a class="menu-link py-1 {{ request()->routeIs('client.products.*') ? 'active' : '' }}" href="{{ route('client.products.index') }}">
													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
													<span class="menu-title">{{ __('Ürünler') }}</span>
												</a>
											</div>
											<div class="menu-item">
												<a class="menu-link py-1 {{ request()->routeIs('client.product-categories.*') ? 'active' : '' }}" href="{{ route('client.product-categories.index') }}">
													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
													<span class="menu-title">{{ __('Ürün Kategorileri') }}</span>
												</a>
											</div>
										</div>
									</div>
									@endif

									@if(auth()->user()->hasModule('project_management'))
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('client.project-categories.*') || request()->routeIs('client.projects.*') ? 'here show' : '' }}">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-briefcase fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Proje Yönetimi') }}</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.projects.*') ? 'active' : '' }}" href="{{ route('client.projects.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Projeler') }}</span>
  												</a>
  											</div>
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.project-categories.*') ? 'active' : '' }}" href="{{ route('client.project-categories.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Proje Kategorileri') }}</span>
  												</a>
  											</div>
  										</div>
									</div>
									@endif

									@if(auth()->user()->hasModule('blog_management'))
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('client.blog-post-categories.*') || request()->routeIs('client.blog-posts.*') ? 'here show' : '' }}">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-abstract-27 fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Blog Yönetimi') }}</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.blog-posts.*') ? 'active' : '' }}" href="{{ route('client.blog-posts.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Blog Yazıları') }}</span>
  												</a>
  											</div>
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.blog-post-categories.*') ? 'active' : '' }}" href="{{ route('client.blog-post-categories.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Blog Kategorileri') }}</span>
  												</a>
  											</div>
  										</div>
									</div>
									@endif

									@if(auth()->user()->hasModule('service_management'))
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('client.service-categories.*') || request()->routeIs('client.services.*') ? 'here show' : '' }}">
										<span class="menu-link">
											<span class="menu-icon">
												<i class="ki-duotone ki-abstract-26 fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Hizmet Yönetimi') }}</span>
											<span class="menu-arrow"></span>
										</span>
										<div class="menu-sub menu-sub-accordion">
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.services.*') ? 'active' : '' }}" href="{{ route('client.services.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Hizmetler') }}</span>
  												</a>
  											</div>
  											<div class="menu-item">
  												<a class="menu-link py-1 {{ request()->routeIs('client.service-categories.*') ? 'active' : '' }}" href="{{ route('client.service-categories.index') }}">
  													<span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
  													<span class="menu-title">{{ __('Hizmet Kategorileri') }}</span>
  												</a>
  											</div>
  										</div>
									</div>
									@endif

									@if(auth()->user()->hasModule('slider_management'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.sliders.*') ? 'active' : '' }}" href="{{ route('client.sliders.index') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-slider-horizontal fs-2">
													<span class="path1"></span><span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Slider Yönetimi') }}</span>
										</a>
									</div>
									@endif

									@if(auth()->user()->hasModule('media_gallery'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.media.*') ? 'active' : '' }}" href="{{ route('client.media.index') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-picture fs-2">
													<span class="path1"></span><span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Medya Galerisi') }}</span>
										</a>
									</div>
									@endif

									@if(auth()->user()->hasModule('news_management'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.news.*') ? 'active' : '' }}" href="{{ route('client.news.index') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-book-open fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Haberler') }}</span>
										</a>
									</div>
									@endif

									@if(auth()->user()->hasModule('reference_management'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.references.*') ? 'active' : '' }}" href="{{ route('client.references.index') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-award fs-2">
													<span class="path1"></span><span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Referans Yönetimi') }}</span>
										</a>
									</div>
									@endif
									@if(auth()->user()->hasModule('brand_management'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.brands.*') ? 'active' : '' }}" href="{{ route('client.brands.index') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-tag fs-2">
													<span class="path1"></span><span class="path2"></span><span class="path3"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Marka Yönetimi') }}</span>
										</a>
									</div>
									@endif


									@if(auth()->user()->hasModule('welcome_message'))
									<!--begin:Menu item-->
									<div class="menu-item">
										<a class="menu-link py-2 {{ request()->routeIs('client.welcome-message.*') ? 'active' : '' }}" href="{{ route('client.welcome-message.edit') }}">
											<span class="menu-icon">
												<i class="ki-duotone ki-sms fs-2">
													<span class="path1"></span><span class="path2"></span>
												</i>
											</span>
											<span class="menu-title">{{ __('Açılış Mesajı') }}</span>
										</a>
									</div>
									@endif
								@endif


								@can('updates.view')
								<!--begin:Menu item-->
								<div class="menu-item">
									<a class="menu-link py-2 {{ request()->routeIs('admin.updates.*') ? 'active' : '' }}" href="{{ route('admin.updates.index') }}">
										<span class="menu-icon">
											<i class="ki-duotone ki-abstract-26 fs-2">
												<span class="path1"></span><span class="path2"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Güncelleme Yönetimi') }}</span>
									</a>
								</div>
								<!--end:Menu item-->
								@endcan

								<!--begin:Menu item-->
								<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs($routePrefix . 'settings.*') || request()->routeIs('profile.*') || request()->routeIs('client.site-settings.*') || request()->routeIs('client.languages.*') ? 'here show' : '' }}">
									<!--begin:Menu link-->
									<span class="menu-link">
										<span class="menu-icon">
											<i class="ki-duotone ki-setting-2 fs-2">
												<span class="path1"></span><span class="path2"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Ayarlar') }}</span>
										<span class="menu-arrow"></span>
									</span>
									<!--end:Menu link-->
									<!--begin:Menu sub-->
									<div class="menu-sub menu-sub-accordion">
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Şifre Değiştirme') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@if(auth()->user()->user_type === 'Client')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs('client.site-settings.*') ? 'active' : '' }}" href="{{ route('client.site-settings.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Site Ayarları') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs('client.languages.*') ? 'active' : '' }}" href="{{ route('client.languages.index') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Dil Ayarları') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endif
										@can('systemsettings.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'settings.system') ? 'active' : '' }}" href="{{ route($routePrefix . 'settings.system') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Sistem Ayarları') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan
									</div>
									<!--end:Menu sub-->
								</div>

								<!--begin:Menu item (Destek)-->
								<div data-kt-menu-trigger="click" class="menu-item menu-accordion {{ request()->routeIs('support.*') || request()->routeIs($routePrefix . 'api-access') ? 'here show' : '' }}">
									<!--begin:Menu link-->
									<span class="menu-link">
										<span class="menu-icon">
											<i class="ki-duotone ki-questionnaire-tablet fs-2">
												<span class="path1"></span><span class="path2"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Destek') }}</span>
										<span class="menu-arrow"></span>
									</span>
									<!--end:Menu link-->
									<!--begin:Menu sub-->
									<div class="menu-sub menu-sub-accordion">
										@can('api_access.view')
										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs($routePrefix . 'api-access') ? 'active' : '' }}" href="{{ route($routePrefix . 'api-access') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('API Erişimi') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->
										@endcan

										<!--begin:Menu item-->
										<div class="menu-item">
											<!--begin:Menu link-->
											<a class="menu-link py-1 {{ request()->routeIs('support.updates') ? 'active' : '' }}" href="{{ route('support.updates') }}">
												<span class="menu-bullet">
													<span class="bullet bullet-dot"></span>
												</span>
												<span class="menu-title">{{ __('Güncelleme Notları') }}</span>
											</a>
											<!--end:Menu link-->
										</div>
										<!--end:Menu item-->

									</div>
									<!--end:Menu sub-->
								</div>								<!--begin:Menu item-->
								<div class="menu-item">
									<form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
										@csrf
									</form>
									<a class="menu-link py-2" href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
										<span class="menu-icon">
											<i class="ki-duotone ki-entrance-right fs-2">
												<span class="path1"></span><span class="path2"></span>
											</i>
										</span>
										<span class="menu-title">{{ __('Güvenli Çıkış') }}</span>
									</a>
								</div>
								<!--end:Menu item-->
							</div>
							<!--end::Menu-->
						</div>
						<!--end::Menu wrapper-->
					</div>
					<!--end::sidebar menu-->
  					<!--begin::Footer-->
  					<div class="app-sidebar-footer flex-column-auto pt-2 pb-6 px-6" id="kt_app_sidebar_footer">
                        @php
                            $clientDomain = auth()->user()->client?->domain;
                            $viewSiteUrl = $clientDomain ? (str_starts_with($clientDomain, 'http') ? $clientDomain : 'https://' . $clientDomain) : url('/');
                        @endphp
  						<a href="{{ $viewSiteUrl }}" target="_blank" class="btn btn-flex flex-center btn-danger overflow-hidden text-nowrap px-0 h-40px w-100">
  							<i class="fas fa-globe fs-2 m-0 p-0 app-sidebar-logo-minimize" style="line-height: 1;"></i>
							<span class="btn-label fw-bold app-sidebar-logo-default">{{ __('Siteyi Görüntüle') }}</span>
  						</a>
  					</div>
  					<!--end::Footer-->
				</div>
				<!--end::Sidebar-->
				<!--begin::Main-->
				<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
					<!--begin::Content wrapper-->
					<div class="d-flex flex-column flex-column-fluid" style="min-height: calc(100vh - 110px);">
						@if(session('success') || session('warning') || session('error') || session('info'))
							<div class="app-container container-fluid mt-5">
								@include('layouts.partials.alerts')
							</div>
							<style>#kt_app_content { padding-top: 0 !important; }
        /* Sidebar Icons Custom Color */
        .app-sidebar .menu-item .menu-link .menu-icon i,
        .app-sidebar .menu-item .menu-link .menu-icon i span {
            transition: color 1s ease !important;
        }

        .app-sidebar .menu-item .menu-link:hover .menu-icon i,
        .app-sidebar .menu-item .menu-link:hover .menu-icon i span {
            color: var(--bs-primary) !important;
            transition: color 0.2s ease !important;
        }

        .app-sidebar .menu-item.here > .menu-link .menu-icon i,
        .app-sidebar .menu-item.here > .menu-link .menu-icon i span,
        .app-sidebar .menu-item.show > .menu-link .menu-icon i,
        .app-sidebar .menu-item.show > .menu-link .menu-icon i span,
        .app-sidebar .menu-item .menu-link.active .menu-icon i,
        .app-sidebar .menu-item .menu-link.active .menu-icon i span {
            color: var(--bs-primary) !important;
            transition: none !important;
        }
</style>
						@endif
						@yield('content')
					</div>
					<!--end::Content wrapper-->

				</div>
				<!--end::Main-->
			</div>
			<!--end::Wrapper-->
		</div>
		<!--end::Page-->
	</div>
	<!--end::App-->
	<!--begin::Javascript-->
	<!--begin::Global Javascript Bundle(mandatory for all pages)-->
	<script src="{{ asset('themes/backend/metronic/assets/plugins/global/plugins.bundle.js') }}"></script>
	<script src="{{ asset('themes/backend/metronic/assets/js/scripts.bundle.js') }}"></script>
	<!--end::Global Javascript Bundle-->
	@include('layouts.partials.swal-scripts')
	<script>
		$(document).ready(function() {
			@if(session('error'))
				SwalHelper.error("{!! session('error') !!}", "{{ (str_contains(session('error'), 'Medya Sınır Aşımı!') || str_contains(session('error'), 'Medya Limit Aşımı!') || str_contains(session('error'), 'limitine ulaştınız')) ? __('Medya Limit Aşımı!') : __('Hata!') }}");
			@endif

			@if(session('warning'))
				Swal.fire({
					icon: 'warning',
					title: "{{ __('Uyarı!') }}",
					html: "{!! session('warning') !!}",
					confirmButtonText: "{{ __('Tamam') }}",
					color: '#ffffff',
					background: '#1e1e2d'
				});
			@endif
		});
	</script>
	@stack('scripts')
</body>

</html>