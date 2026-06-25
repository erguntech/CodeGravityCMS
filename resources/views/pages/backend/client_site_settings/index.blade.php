@extends('layouts.backend')

@section('title', __('Site Ayarları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Site Ayarları') }}</h1>
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
        
        @php
            $client = $user->client;
        @endphp

        <div class="card card-flush mb-5">
            <div class="card-header align-items-center position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;" id="dynamic-ribbon">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('İletişim Bilgileri') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar m-0">
                    <ul class="nav nav-tabs border-0" role="tablist">
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px active" data-bs-toggle="tab" href="#tab_contact" data-ribbon="İletişim Bilgileri" data-ribbon-color="bg-warning" title="{{ __('İletişim') }}">
                                <i class="ki-duotone ki-phone fs-2 text-success"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link btn btn-icon btn-light btn-active-light-primary w-35px h-35px" data-bs-toggle="tab" href="#tab_social" data-ribbon="Sosyal Medya Hesapları" data-ribbon-color="bg-warning" title="{{ __('Sosyal Medya') }}">
                                <i class="ki-duotone ki-instagram fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            
            <div class="card-body pt-6">
                <div class="tab-content">
                    
                    {{-- 1. İletişim Bilgileri --}}
                    <div class="tab-pane fade show active" id="tab_contact" role="tabpanel">
                        <form action="{{ route('client.site-settings.update') }}" method="POST" class="kt_site_settings_form" novalidate>
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
                                <button type="submit" class="btn btn-sm btn-warning me-3 site_settings_submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Sosyal Medya --}}
                    <div class="tab-pane fade" id="tab_social" role="tabpanel">
                        <form action="{{ route('client.site-settings.update') }}" method="POST" class="kt_site_settings_form" novalidate>
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
                                    <input type="text" name="whatsapp" class="form-control form-control-solid" placeholder="05xx0000000" value="{{ old('whatsapp', $client?->whatsapp) }}" />
                                </div>
                            </div>
                            <div class="separator separator-dashed my-10"></div>
                            <div class="d-flex justify-content-start pb-4">
                                <button type="submit" class="btn btn-sm btn-warning me-3 site_settings_submit">
                                    <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                                    <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
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
    document.addEventListener('DOMContentLoaded', function () {
        const ribbon = document.getElementById('dynamic-ribbon');
        const ribbonText = ribbon.querySelector('span');

        document.querySelectorAll('.nav-link[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('show.bs.tab', function (e) {
                ribbon.className = 'ribbon-label fs-6'; 
                ribbon.classList.add(e.target.getAttribute('data-ribbon-color'));
                ribbonText.innerText = e.target.getAttribute('data-ribbon');
            });
        });

        document.querySelectorAll('.kt_site_settings_form').forEach(form => {
            form.addEventListener('submit', function (e) {
                const submitButton = form.querySelector('.site_settings_submit');
                if (submitButton) {
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                }
            });
        });
    });
</script>
@endpush
