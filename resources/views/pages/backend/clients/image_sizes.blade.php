@extends('layouts.backend')

@section('title', __('Müşteri Resim Boyutu Düzenleme'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Müşteri Resim Boyutu Düzenleme') }}</h1>
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
    $modules = [
        'slider' => ['title' => 'Slider Resmi', 'color' => 'primary', 'icon' => 'ki-picture'],
        'news' => ['title' => 'Haber Manşet Resmi', 'color' => 'success', 'icon' => 'ki-notepad'],
        'news_gallery' => ['title' => 'Haber Galerisi Resmi', 'color' => 'success', 'icon' => 'ki-gallery'],
        'media_gallery' => ['title' => 'Medya Galerisi Resmi', 'color' => 'info', 'icon' => 'ki-gallery-tick'],
        'reference' => ['title' => 'Referans Manşet Resmi', 'color' => 'warning', 'icon' => 'ki-briefcase'],
        'welcome_message' => ['title' => 'Açılış Mesajı Resmi', 'color' => 'primary', 'icon' => 'ki-message-text-2'],
        'product' => ['title' => 'Ürün Manşet Resmi', 'color' => 'danger', 'icon' => 'ki-handcart'],
        'product_gallery' => ['title' => 'Ürün Galerisi Resmi', 'color' => 'danger', 'icon' => 'ki-gallery'],
        'product_category' => ['title' => 'Ürün Kategorisi Manşet Resmi', 'color' => 'danger', 'icon' => 'ki-category'],
        'project' => ['title' => 'Proje Manşet Resmi', 'color' => 'primary', 'icon' => 'ki-teacher'],
        'project_gallery' => ['title' => 'Proje Galerisi Resmi', 'color' => 'primary', 'icon' => 'ki-gallery'],
        'project_category' => ['title' => 'Proje Kategorisi Manşet Resmi', 'color' => 'primary', 'icon' => 'ki-category'],
    ];
@endphp
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card card-flush mb-5 mb-xl-10">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">Müşteri Resim Boyutu Bilgileri</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar"></div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route($routePrefix . 'clients.image-sizes.update', $user->id) }}" method="POST" id="kt_image_sizes_form" novalidate>
                    @csrf
                    
                    <div class="row mb-0">
                        @foreach ($modules as $key => $mod)
                            <div class="col-md-3 {{ $loop->remaining < 4 ? 'mb-0' : 'mb-6' }}">
                                <label class="form-label required">{{ $mod['title'] }} Boyutları</label>
                                <input type="text" name="image_sizes[{{ $key }}]" class="form-control form-control-solid @error('image_sizes.'.$key) border border-danger @enderror" style="@error('image_sizes.'.$key) border-color: #f1416c !important; @enderror" placeholder="450x450 px" value="{{ old('image_sizes.'.$key, $user->client->image_sizes[$key] ?? '') }}" required />
                                @error('image_sizes.'.$key)
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="separator separator-dashed my-10"></div>
                    
                    <div class="d-flex justify-content-start pb-4">
                        <button type="submit" id="kt_image_sizes_submit" class="btn btn-sm btn-warning me-3">
                            <span class="indicator-label">Değişiklikleri Kaydet</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route($routePrefix . 'clients.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('kt_image_sizes_form');
    const submitButton = document.getElementById('kt_image_sizes_submit');

    form.addEventListener('submit', function (e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush
