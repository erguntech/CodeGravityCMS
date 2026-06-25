@extends('layouts.backend')

@section('title', __('Sistem Ayarları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Sistem Ayarları') }}</h1>
        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">{{ $appName }}</a>
        </li>
    </ul>
</div>
@endsection

@section('content')
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        

        <div class="card card-flush mb-5 mb-xl-10">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Sistem Bilgileri') }}</span>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route($routePrefix . 'settings.system.update') }}" method="POST" id="kt_system_settings_form" autocomplete="off" novalidate>
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('Uygulama Adı') }}</label>
                            <input type="text" name="app_name" class="form-control form-control-solid @error('app_name') is-invalid border-danger @enderror" placeholder="{{ __('Uygulama Adı Giriniz') }}" value="{{ $settings['app_name'] ?? '' }}" />
                            @error('app_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-6">
                            <label class="form-label">{{ __('Uygulama Açıklaması') }}</label>
                            <input type="text" name="app_description" class="form-control form-control-solid @error('app_description') is-invalid border-danger @enderror" placeholder="{{ __('Uygulama Açıklaması Giriniz') }}" value="{{ $settings['app_description'] ?? '' }}" />
                            @error('app_description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-6">
                            <label class="form-label">{{ __('SEO Anahtar Kelimeler') }}</label>
                            <input type="text" name="seo_keywords" class="form-control form-control-solid @error('seo_keywords') is-invalid border-danger @enderror" placeholder="{{ __('SEO Anahtar Kelimeler Giriniz') }}" value="{{ $settings['seo_keywords'] ?? '' }}" />
                            @error('seo_keywords')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label class="form-label">{{ __('İletişim Numarası') }}</label>
                            <input type="text" name="contact_phone" class="form-control form-control-solid @error('contact_phone') is-invalid border-danger @enderror" placeholder="{{ __('İletişim Numarası Giriniz') }}" value="{{ $settings['contact_phone'] ?? '' }}" />
                            @error('contact_phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-6">
                            <label class="form-label">{{ __('E-posta Adresi') }}</label>
                            <input type="email" name="contact_email" class="form-control form-control-solid @error('contact_email') is-invalid border-danger @enderror" placeholder="{{ __('E-posta Adresi Giriniz') }}" value="{{ $settings['contact_email'] ?? '' }}" />
                            @error('contact_email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-6">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('Adres') }}</label>
                            <input type="text" name="contact_address" class="form-control form-control-solid @error('contact_address') is-invalid border-danger @enderror" placeholder="{{ __('Adres Giriniz') }}" value="{{ $settings['contact_address'] ?? '' }}" />
                            @error('contact_address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('WhatsApp Numarası') }}</label>
                            <input type="text" name="whatsapp_number" class="form-control form-control-solid @error('whatsapp_number') is-invalid border-danger @enderror" placeholder="{{ __('WhatsApp Numarası Giriniz') }}" value="{{ $settings['whatsapp_number'] ?? '' }}" />
                            @error('whatsapp_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('Telegram Kullanıcı Adı') }}</label>
                            <input type="text" name="telegram_username" class="form-control form-control-solid @error('telegram_username') is-invalid border-danger @enderror" placeholder="{{ __('Telegram Kullanıcı Adı Giriniz') }}" value="{{ $settings['telegram_username'] ?? '' }}" />
                            @error('telegram_username')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('Destek E-Posta Adresi') }}</label>
                            <input type="email" name="support_email" class="form-control form-control-solid @error('support_email') is-invalid border-danger @enderror" placeholder="{{ __('Destek E-Posta Adresi Giriniz') }}" value="{{ $settings['support_email'] ?? '' }}" />
                            @error('support_email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>



                    <div class="separator separator-dashed my-10"></div>
                    <div class="d-flex justify-content-start pb-4">
                        <button type="submit" id="kt_system_settings_submit" class="btn btn-sm btn-warning me-3">
                            <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('kt_system_settings_form');
    const submitButton = document.getElementById('kt_system_settings_submit');

    form.addEventListener('submit', function (e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush

