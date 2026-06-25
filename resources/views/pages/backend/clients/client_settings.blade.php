@extends('layouts.backend')

@section('title', __('Müşteri Ayarları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Müşteri Ayarları') }}</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">{{ $appName }}</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
@php
    $routePrefix = auth()->user()->hasRole('Admin') ? 'admin.' : (auth()->user()->hasRole('Moderator') ? 'moderator.' : '');
    $client = $user->client;

    $imageSizeModules = [
        'slider'             => ['title' => 'Slider Resmi',                    'color' => 'primary', 'icon' => 'ki-picture'],
        'welcome_message'    => ['title' => 'Açılış Mesajı Resmi',             'color' => 'primary', 'icon' => 'ki-message-text-2'],
        'product'            => ['title' => 'Ürün Manşet Resmi',               'color' => 'danger',  'icon' => 'ki-handcart'],
        'product_category'   => ['title' => 'Ürün Kategorisi Manşet Resmi',    'color' => 'danger',  'icon' => 'ki-category'],
        'project'            => ['title' => 'Proje Manşet Resmi',              'color' => 'primary', 'icon' => 'ki-teacher'],
        'project_category'   => ['title' => 'Proje Kategorisi Manşet Resmi',   'color' => 'primary', 'icon' => 'ki-category'],
        'blog_post'          => ['title' => 'Blog Yazısı Manşet Resmi',        'color' => 'info',    'icon' => 'ki-book-open'],
        'blog_post_category' => ['title' => 'Blog Kategorisi Manşet Resmi',    'color' => 'info',    'icon' => 'ki-category'],
        'service'            => ['title' => 'Hizmet Manşet Resmi',             'color' => 'dark',    'icon' => 'ki-briefcase'],
        'service_category'   => ['title' => 'Hizmet Kategorisi Manşet Resmi',  'color' => 'dark',    'icon' => 'ki-category'],
        'news'               => ['title' => 'Haber Manşet Resmi',              'color' => 'success', 'icon' => 'ki-notepad'],
        'reference'          => ['title' => 'Referans Manşet Resmi',           'color' => 'warning', 'icon' => 'ki-briefcase'],
        'brand'              => ['title' => 'Marka Manşet Resmi',              'color' => 'success', 'icon' => 'ki-tag'],
    ];
@endphp

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="card card-flush mb-5">
            <div class="card-header align-items-center position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;" id="dynamic-ribbon">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Domain & SSL Bilgileri') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar m-0">
                    <ul class="nav nav-tabs border-0" role="tablist">
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px active" data-bs-toggle="tab" href="#tab_domain" data-ribbon="Domain & SSL Bilgileri" data-ribbon-color="bg-warning" title="{{ __('Domain & SSL') }}">
                                <i class="ki-duotone ki-shield-tick fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px" data-bs-toggle="tab" href="#tab_contact" data-ribbon="İletişim Bilgileri" data-ribbon-color="bg-warning" title="{{ __('İletişim') }}">
                                <i class="ki-duotone ki-phone fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px" data-bs-toggle="tab" href="#tab_social" data-ribbon="Sosyal Medya Hesapları" data-ribbon-color="bg-warning" title="{{ __('Sosyal Medya') }}">
                                <i class="ki-duotone ki-instagram fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px" data-bs-toggle="tab" href="#tab_images" data-ribbon="Resim Boyutu Ayarları" data-ribbon-color="bg-warning" title="{{ __('Resim Boyutları') }}">
                                <i class="ki-duotone ki-picture fs-2 text-dark"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px" data-bs-toggle="tab" href="#tab_modules" data-ribbon="Admin Panel Modülleri" data-ribbon-color="bg-warning" title="{{ __('Modül Yönetimi') }}">
                                <i class="ki-duotone ki-abstract-26 fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            
            <div class="card-body pt-6">
                <div class="tab-content">
                    
                    {{-- 1. Domain & SSL Bilgileri --}}
                    <div class="tab-pane fade show active" id="tab_domain" role="tabpanel">
                        <form action="{{ route($routePrefix . 'clients.client-settings.update', $user->id) }}" method="POST" class="kt_client_settings_form" novalidate>
                            @csrf
                            <div class="row g-5">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Domain Adı') }}</label>
                                    <input type="text" name="domain" class="form-control form-control-solid" placeholder="ornek.com" value="{{ old('domain', $client?->domain) }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Domain Başlangıç Tarihi') }}</label>
                                    <input type="date" name="domain_started_at" class="form-control form-control-solid" value="{{ old('domain_started_at', $client?->domain_started_at?->format('Y-m-d')) }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Domain Bitiş Tarihi') }}</label>
                                    <input type="date" name="domain_expires_at" class="form-control form-control-solid" value="{{ old('domain_expires_at', $client?->domain_expires_at?->format('Y-m-d')) }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('SSL Başlangıç Tarihi') }}</label>
                                    <input type="date" name="ssl_started_at" class="form-control form-control-solid" value="{{ old('ssl_started_at', $client?->ssl_started_at?->format('Y-m-d')) }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('SSL Bitiş Tarihi') }}</label>
                                    <input type="date" name="ssl_expires_at" class="form-control form-control-solid" value="{{ old('ssl_expires_at', $client?->ssl_expires_at?->format('Y-m-d')) }}" />
                                </div>
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 btn-submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

                    {{-- 2. İletişim Bilgileri --}}
                    <div class="tab-pane fade" id="tab_contact" role="tabpanel">
                        <form action="{{ route($routePrefix . 'clients.client-settings.update', $user->id) }}" method="POST" class="kt_client_settings_form" novalidate>
                            @csrf
                            <div class="row g-5">
                                <div class="col-12">
                                    <label class="form-label">{{ __('Adres Bilgileri') }}</label>
                                    <input type="text" name="address" class="form-control form-control-solid" placeholder="{{ __('Adres Bilgileri...') }}" value="{{ old('address', $client?->address) }}" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Ek Adres Bilgileri') }}</label>
                                    <textarea name="additional_address" class="form-control form-control-solid" rows="3" placeholder="{{ __('Ek Adres Bilgileri...') }}">{{ old('additional_address', $client?->additional_address) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Telefon Numarası') }}</label>
                                    <input type="text" name="phone" class="form-control form-control-solid" placeholder="{{ __('Telefon Numarası...') }}" value="{{ old('phone', $client?->phone) }}" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Fax Numarası') }}</label>
                                    <input type="text" name="fax" class="form-control form-control-solid" placeholder="{{ __('Fax Numarası...') }}" value="{{ old('fax', $client?->fax) }}" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Ek İletişim Bilgileri') }}</label>
                                    <textarea name="additional_contact" class="form-control form-control-solid" rows="3" placeholder="{{ __('Ek İletişim Bilgileri...') }}">{{ old('additional_contact', $client?->additional_contact) }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('Adres Koordinatları') }}</label>
                                    <input type="text" name="coordinates" class="form-control form-control-solid" placeholder="37.97947439519515, 32.4620700817852" value="{{ old('coordinates', $client?->coordinates) }}" />
                                </div>
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 btn-submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

                    {{-- 3. Sosyal Medya --}}
                    <div class="tab-pane fade" id="tab_social" role="tabpanel">
                        <form action="{{ route($routePrefix . 'clients.client-settings.update', $user->id) }}" method="POST" class="kt_client_settings_form" novalidate>
                            @csrf
                            <div class="row g-5">
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="ki-duotone ki-instagram fs-4 text-danger me-1"><span class="path1"></span><span class="path2"></span></i>
                                        {{ __('Instagram') }}
                                    </label>
                                    <input type="url" name="instagram" class="form-control form-control-solid" placeholder="https://instagram.com/..." value="{{ old('instagram', $client?->instagram) }}" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="ki-duotone ki-facebook fs-4 text-primary me-1"><span class="path1"></span></i>
                                        {{ __('Facebook') }}
                                    </label>
                                    <input type="url" name="facebook" class="form-control form-control-solid" placeholder="https://facebook.com/..." value="{{ old('facebook', $client?->facebook) }}" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="ki-duotone ki-whatsapp fs-4 text-success me-1"><span class="path1"></span></i>
                                        {{ __('WhatsApp') }}
                                    </label>
                                    <input type="text" name="whatsapp" class="form-control form-control-solid" placeholder="+90 500 000 00 00" value="{{ old('whatsapp', $client?->whatsapp) }}" />
                                </div>
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 btn-submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

                    {{-- 4. Resim Boyutları --}}
                    <div class="tab-pane fade" id="tab_images" role="tabpanel">
                        <form action="{{ route($routePrefix . 'clients.client-settings.update', $user->id) }}" method="POST" class="kt_client_settings_form" novalidate>
                            @csrf
                            <div class="row mb-0">
                                @foreach ($imageSizeModules as $key => $mod)
                                    <div class="{{ $loop->last ? 'col-md-12' : 'col-md-6' }} {{ $loop->remaining < 2 ? 'mb-0' : 'mb-6' }}">
                                        <label class="form-label">{{ $mod['title'] }} Boyutları</label>
                                        <input type="text"
                                            name="image_sizes[{{ $key }}]"
                                            class="form-control form-control-solid @error('image_sizes.'.$key) border border-danger @enderror"
                                            style="@error('image_sizes.'.$key) border-color: #f1416c !important; @enderror"
                                            placeholder="450x450 px"
                                            value="{{ old('image_sizes.'.$key, $client?->image_sizes[$key] ?? '') }}" />
                                        @error('image_sizes.'.$key)
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 btn-submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

                    {{-- 5. Admin Panel Modülleri --}}
                    <div class="tab-pane fade" id="tab_modules" role="tabpanel">
                        <form action="{{ route($routePrefix . 'clients.client-settings.update', $user->id) }}" method="POST" class="kt_client_settings_form" novalidate>
                            @csrf
                            <input type="hidden" name="is_modules_form" value="1">
                            <div class="d-flex flex-column gap-5">
                                @php
                                    $availableModules = [
                                        'product_management'   => ['title' => 'Ürün Yönetimi', 'icon' => 'ki-parcel', 'color' => 'primary'],
                                        'project_management'   => ['title' => 'Proje Yönetimi', 'icon' => 'ki-briefcase', 'color' => 'success'],
                                        'blog_management'      => ['title' => 'Blog Yönetimi', 'icon' => 'ki-abstract-27', 'color' => 'info'],
                                        'service_management'   => ['title' => 'Hizmet Yönetimi', 'icon' => 'ki-abstract-26', 'color' => 'warning'],
                                        'slider_management'    => ['title' => 'Slider Yönetimi', 'icon' => 'ki-slider-horizontal', 'color' => 'danger'],
                                        'media_gallery'        => ['title' => 'Medya Galerisi', 'icon' => 'ki-picture', 'color' => 'dark'],
                                        'news_management'      => ['title' => 'Haberler', 'icon' => 'ki-book-open', 'color' => 'primary'],
                                        'reference_management' => ['title' => 'Referans Yönetimi', 'icon' => 'ki-award', 'color' => 'success'],
                                        'brand_management'     => ['title' => 'Marka Yönetimi', 'icon' => 'ki-tag', 'color' => 'info'],
                                        'welcome_message'      => ['title' => 'Açılış Mesajı', 'icon' => 'ki-sms', 'color' => 'warning']
                                    ];
                                    $clientModules = old('modules', $client?->modules ?? []);
                                @endphp

                                @foreach($availableModules as $key => $module)
                                <div class="d-flex align-items-center border border-dashed border-gray-300 rounded p-5 mb-0">
                                    <div class="symbol symbol-50px me-5">
                                        <span class="symbol-label bg-light-{{ $module['color'] }}">
                                            <i class="ki-duotone {{ $module['icon'] }} fs-2x text-{{ $module['color'] }}">
                                                <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <label class="form-check-label text-gray-900 fw-bold fs-5" for="module_{{ $key }}" style="cursor: pointer;">
                                            {{ $module['title'] }}
                                        </label>
                                        <span class="text-muted fw-semibold">Bu müşterinin {{ mb_strtolower($module['title']) }} modülüne erişimini yönetin.</span>
                                    </div>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input h-30px w-50px cursor-pointer" type="checkbox" value="{{ $key }}" id="module_{{ $key }}" name="modules[]" {{ in_array($key, $clientModules) ? 'checked' : '' }} />
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 btn-submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }}
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                                <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-header .nav-tabs .nav-link {
        border: 1px solid transparent !important;
    }
    .card-header .nav-tabs .nav-link.active {
        background-color: var(--bs-primary-light) !important;
        border: 1px solid var(--bs-primary) !important;
        border-radius: 0.475rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab geçişlerinde ribbon güncellemesi
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const ribbon = document.getElementById('dynamic-ribbon');
            const ribbonText = ribbon.querySelector('span');
            
            // Eski renkleri kaldır
            ribbon.classList.remove('bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger');
            
            // Yeni rengi ekle
            ribbon.classList.add(e.target.getAttribute('data-ribbon-color'));
            
            // Metni güncelle
            ribbonText.innerText = e.target.getAttribute('data-ribbon');
        });
    });

    const forms = document.querySelectorAll('.kt_client_settings_form');

    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const submitButton = form.querySelector('.btn-submit');
            if (submitButton) {
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            }
        });

        // Module toggle logic removed
    });
</script>
@endpush
