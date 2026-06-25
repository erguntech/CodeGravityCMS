@extends('layouts.backend')

@section('title', __('Kullanıcı Ekleme'))

@push('styles')
<style>
    .is-invalid + .select2-container--bootstrap5 .select2-selection,
    .is-invalid + .select2-container [data-select2-id] {
        border-color: #f1416c !important;
    }
    /* Select2 Sıçramasını Önleme */
    select[data-control="select2"] {
        height: 44px !important;
        opacity: 0;
        overflow: hidden;
    }
</style>
@endpush

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Kullanıcı Ekleme') }}</h1>
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
                <div class="ribbon-label bg-success fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">Kullanıcı Bilgileri</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar"></div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route($routePrefix . 'users.store') }}" method="POST" id="kt_user_create_form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('Ad Soyad') }}</label>
                            <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid border-danger @enderror" placeholder="{{ __('Ad Soyad Giriniz') }}" value="{{ old('name') }}" />
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('E-posta') }}</label>
                            <input type="email" name="email" class="form-control form-control-solid @error('email') is-invalid border-danger @enderror" placeholder="{{ __('E-posta Giriniz') }}" value="{{ old('email') }}" />
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('Roller') }}</label>
                            <select name="roles[]" class="form-select form-select-solid @error('roles') is-invalid border-danger @enderror" data-control="select2" data-placeholder="{{ __('Rol Seçiniz') }}" data-allow-clear="true" multiple="multiple">
                                <option value="">{{ __('Rol Seçiniz') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ (collect(old('roles'))->contains($role->name)) ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('roles')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('Durum') }}</label>
                            <select name="status" class="form-select form-select-solid @error('status') is-invalid border-danger @enderror" data-control="select2" data-hide-search="true">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="passive" {{ old('status') == 'passive' ? 'selected' : '' }}>{{ __('Pasif') }}</option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-6 mb-md-0">
                            <label class="form-label required">{{ __('Şifre') }}</label>
                            <input type="password" name="password" class="form-control form-control-solid @error('password') is-invalid border-danger @enderror" placeholder="{{ __('Şifre Giriniz') }}" autocomplete="new-password" />
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-0">
                            <label class="form-label required">{{ __('Şifre Tekrarı') }}</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-solid" placeholder="{{ __('Şifre Tekrarı Giriniz') }}" autocomplete="new-password" />
                        </div>
                    </div>
                    <div class="separator separator-dashed my-10"></div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_user_create_submit" class="btn btn-sm btn-success me-3">
                            <span class="indicator-label">Kullanıcı Ekle</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route($routePrefix . 'users.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('kt_user_create_form');
    const submitButton = document.getElementById('kt_user_create_submit');

    form.addEventListener('submit', function (e) {
        // Show indicator and disable button
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush

