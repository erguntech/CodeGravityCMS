@extends('layouts.backend')
<?php \Carbon\Carbon::setLocale('tr'); ?>
@section('title', __('Güncelleme Notları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Güncelleme Notları') }}</h1>
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
        <!-- Alert Box -->
        <div class="alert alert-dismissible bg-light-primary border border-primary d-flex align-items-center p-5 mb-4">
            <i class="ki-duotone ki-notification-on fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h4 class="fw-semibold text-primary">{{ __('Sistem Güncellemeleri') }}</h4>
                <span>{{ __('Sistemimizde yapılan son güncellemeleri, yenilikleri ve hata düzeltmelerini bu sayfadan takip edebilirsiniz.') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>


                @if($notes->count() > 0)
                    <div class="accordion" id="kt_accordion_updates">
                        @foreach($notes as $index => $note)
                            <!--begin::Item-->
                            <div class="accordion-item">
                                <!--begin::Header-->
                                <h2 class="accordion-header" id="kt_accordion_updates_header_{{ $note->id }}">
                                    <button class="accordion-button fs-4 fw-semibold {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#kt_accordion_updates_body_{{ $note->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="kt_accordion_updates_body_{{ $note->id }}">
                                        {{ $note->version }} <span class="fs-6 fw-normal text-muted ms-3">({{ $note->created_at->format('d.m.Y') }})</span>
                                    </button>
                                </h2>
                                <!--end::Header-->

                                <!--begin::Body-->
                                <div id="kt_accordion_updates_body_{{ $note->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="kt_accordion_updates_header_{{ $note->id }}" data-bs-parent="#kt_accordion_updates">
                                    <div class="accordion-body text-gray-600">
                                        {!! nl2br(e($note->notes)) !!}
                                    </div>
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Item-->
                        @endforeach
                    </div>
                @else
                    <div class="d-flex flex-column justify-content-center align-items-center text-center h-200px">
                        <i class="ki-duotone ki-information-5 fs-3x text-muted mb-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <span class="text-muted fw-semibold fs-5">{{ __('Henüz yayınlanmış bir güncelleme notu bulunmuyor.') }}</span>
                    </div>
                @endif

    </div>
</div>
@endsection
