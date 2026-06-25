@extends('layouts.backend')

@section('title', __('Moderatör Paneli'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Genel Bakış') }}</h1>
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
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
                <div class="card card-flush h-md-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $totalUsers ?? 0 }}</span>
                            </div>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('Toplam Kullanıcı') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-9 mb-md-5 mb-xl-10">
                <div class="card card-flush h-md-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ __('Merhaba, :name', ['name' => auth()->user()->name]) }}</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('Moderatör paneline hoş geldiniz.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
