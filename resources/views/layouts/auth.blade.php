<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
	<!--begin::Head-->
	<head>
		<title>{{ $appName ?? 'CodeGravity' }} - @yield('title', __('Giriş Yap'))</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('themes/backend/metronic/favicon/apple-touch-icon.png') }}">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('themes/backend/metronic/favicon/favicon-32x32.png') }}">
		<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('themes/backend/metronic/favicon/favicon-16x16.png') }}">
		<link rel="shortcut icon" href="{{ asset('themes/backend/metronic/favicon/favicon.ico') }}">
		<!--begin::Fonts(mandatory for all pages)-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<!--end::Fonts-->
		<!--begin::Global Stylesheets Bundle(mandatory for all pages)-->
		<link href="{{ asset('themes/backend/metronic/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('themes/backend/metronic/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<!--end::Global Stylesheets Bundle-->
        @stack('styles')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
		<!--begin::Theme mode setup on page load-->
		<script>document.documentElement.setAttribute("data-bs-theme", "dark");</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<!--begin::Page bg image-->
			<style>body { background-image: url('{{ asset('themes/backend/metronic/assets/media/auth/bg4.jpg') }}'); } [data-bs-theme="dark"] body { background-image: url('{{ asset('themes/backend/metronic/assets/media/auth/bg4-dark.jpg') }}'); }</style>
			<!--end::Page bg image-->
			<!--begin::Authentication-->
			<div class="d-flex flex-column flex-column-fluid flex-center">
				<!--begin::Card-->
				<div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px px-20 pt-10 pb-12">
					<!--begin::Wrapper-->
					<div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-0">
                        @yield('content')
                        
                        <!--begin::Footer-->
                        <div class="text-gray-500 text-center fw-semibold fs-7 mt-5">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.
                        </div>
                        <!--end::Footer-->
					</div>
					<!--end::Wrapper-->
				</div>
				<!--end::Card-->
			</div>
			<!--end::Authentication-->
		</div>
		<!--end::Root-->
		<!--begin::Javascript-->
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('themes/backend/metronic/assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('themes/backend/metronic/assets/js/scripts.bundle.js') }}"></script>
		<!--end::Global Javascript Bundle-->
        @stack('scripts')
	</body>
</html>
