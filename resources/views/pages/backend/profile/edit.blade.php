@extends('layouts.backend')

@section('title', __('Şifre Değiştirme'))

@section('page_title')
    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
        <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
            {{ __('Şifre Değiştirme') }}</h1>
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
                        <span class="d-flex text-white fw-bolder fs-6">{{ __('Şifre Bilgileri') }}</span>
                    </div>
                    <div class="card-title"></div>
                </div>
                <div class="separator separator-dashed border-gray-200"></div>
                <div class="card-body pt-6">
                    <form action="{{ route('profile.update') }}" method="POST" id="kt_profile_settings_form"
                        autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div class="row mb-6">
                            <div class="col-md-12">
                                <label class="form-label required">{{ __('Mevcut Şifre') }}</label>
                                <input type="password" name="current_password"
                                    class="form-control form-control-solid @error('current_password') is-invalid border-danger @enderror"
                                    placeholder="{{ __('Mevcut Şifre Giriniz') }}" />
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 mb-0">
                                <label class="form-label required">{{ __('Yeni Şifre') }}</label>
                                <input type="password" name="password"
                                    class="form-control form-control-solid @error('password') is-invalid border-danger @enderror"
                                    placeholder="{{ __('Yeni Şifre Giriniz') }}" />
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-0">
                                <label class="form-label required">{{ __('Şifre Tekrarı') }}</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-solid @error('password') is-invalid border-danger @enderror"
                                    placeholder="{{ __('Şifre Tekrarı Giriniz') }}" />
                            </div>
                        </div>
                    <div class="separator separator-dashed my-10"></div>

                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_profile_settings_submit" class="btn btn-sm btn-warning me-3">
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
        const form = document.getElementById('kt_profile_settings_form');
        const submitButton = document.getElementById('kt_profile_settings_submit');

        form.addEventListener('submit', function (e) {
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;
        });
    </script>
@endpush

