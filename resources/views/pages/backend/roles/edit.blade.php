@extends('layouts.backend')

@section('title', __('Kullanıcı Rolü Düzenleme'))

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
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Kullanıcı Rolü Düzenleme') }}</h1>
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
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Rol Bilgileri') }}</span>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route($routePrefix . 'roles.update', $role->id) }}" method="POST" id="kt_role_edit_form" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="row mb-6">
                        <div class="col-md-12 mb-0">
                            <label class="form-label required">{{ __('Rol Adı') }}</label>
                            <input type="text" name="name" class="form-control form-control-solid @error('name') is-invalid border-danger @enderror" placeholder="Rol Adı Giriniz" value="{{ old('name', $role->name) }}" />
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-0">
                            <label class="form-label">İzinler</label>
                            <select name="permissions[]" class="form-select form-select-solid @error('permissions') is-invalid border-danger @enderror" data-control="select2" data-placeholder="İzin Seçiniz" data-allow-clear="true" multiple="multiple">
                                <option value="">İzin Seçiniz</option>
                                @foreach($permissions as $permission)
                                    <option value="{{ $permission->name }}" {{ (collect(old('permissions', $role->permissions->pluck('name')))->contains($permission->name)) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                @endforeach
                            </select>
                            @error('permissions')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="separator separator-dashed my-10"></div>
                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_role_edit_submit" class="btn btn-sm btn-warning me-3">
                            <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route($routePrefix . 'roles.index') }}" class="btn btn-sm btn-light btn-active-light-primary">Geri Dön</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('kt_role_edit_form');
    const submitButton = document.getElementById('kt_role_edit_submit');

    form.addEventListener('submit', function (e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush

