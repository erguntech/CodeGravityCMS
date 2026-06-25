@extends('layouts.backend')

@section('title', __('Güncelleme Ekleme'))

@push('styles')
<style>
    .border-danger + .select2-container--bootstrap5 .select2-selection,
    .border-danger + .select2-container [data-select2-id] {
        border-color: #f1416c !important;
    }
    select[data-control="select2"] {
        height: 44px !important;
        opacity: 0;
        overflow: hidden;
    }
</style>
@endpush

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Güncelleme Ekleme') }}</h1>
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
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Güncelleme Bilgileri') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar"></div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route('admin.updates.store') }}" method="POST" id="kt_updates_create_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-6">
                            <label class="form-label required">{{ __('Güncelleme Sürümü') }}</label>
                            <input type="text" name="version" class="form-control form-control-solid @error('version') border-danger @enderror" placeholder="{{ __('Örn: v1.0.0') }}" value="{{ old('version') }}" />
                            @error('version')
                                <div class="text-danger small mt-1">@ {{ __('Güncelleme Sürümü') }} {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-6">
                            <label class="form-label required">{{ __('Güncelleme Notları') }}</label>
                            <textarea name="notes" class="form-control form-control-solid @error('notes') border-danger @enderror" placeholder="{{ __('Güncelleme detaylarını buraya yazınız...') }}" rows="6">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger small mt-1">@ {{ __('Güncelleme Notları') }} {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-0">
                            <label class="form-label required">{{ __('Durumu') }}</label>
                            <select name="status" class="form-select form-select-solid @error('status') border-danger @enderror" data-control="select2" data-hide-search="true" data-placeholder="{{ __('Seçim Yapınız') }}">
                                <option value=""></option>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="passive" {{ old('status') == 'passive' ? 'selected' : '' }}>{{ __('Pasif') }}</option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">@ {{ __('Durumu') }} {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="separator separator-dashed my-10"></div>
                    
                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_updates_create_submit" class="btn btn-sm btn-success me-3">
                            <span class="indicator-label">{{ __('Güncelleme Ekle') }}</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route('admin.updates.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('kt_updates_create_form');
    const submitButton = document.getElementById('kt_updates_create_submit');

    form.addEventListener('submit', function (e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush
