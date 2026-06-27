@extends('layouts.backend')

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
        <!-- Welcome Alert -->
        <div class="alert alert-dismissible bg-light-primary border border-primary d-flex align-items-center p-5 mb-4">
            <i class="ki-duotone ki-notification-on fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h4 class="fw-semibold text-primary">{{ __('Sn.') }} <span class="text-warning">{{ auth()->user()->name }}</span>, {{ __('Hoş Geldiniz!') }}</h4>
                <span>{{ __('Tüm müşteri ve sistem yönetimini bu alandan sağlayabilirsiniz.') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>

        <div class="row g-5 g-xl-10 mb-4">

                        {{-- Sol: Ödemesi Yaklaşan Ürünler (col-4) --}}
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px pt-0 pb-0">
                        <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                            <span class="d-flex text-white fw-bolder fs-6">{{ __('Ödemesi Yaklaşan Ürünler') }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed border-gray-200"></div>
                    <div class="card-body pt-6 pb-6">
                        <div class="pe-3" style="max-height: 350px; overflow-y: auto;">
                        @if(!empty($expiringProducts) && $expiringProducts->count() > 0)
                            @foreach($expiringProducts as $item)
                                @php
                                    $client = $item['client'];
                                    $type = $item['type'];
                                    $daysLeft = $item['days_left'];
                                    $badgeColor = $daysLeft <= 7 ? 'danger' : 'warning';
                                    $typeBadgeColor = $type === 'SSL' ? 'info' : 'primary';
                                @endphp
                                <div class="d-flex align-items-center mb-4">
                                    <span class="bullet bullet-vertical h-40px bg-{{ $badgeColor }} me-3"></span>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <a href="{{ route('admin.clients.edit', $client->id) }}" class="text-gray-800 text-hover-{{ $badgeColor }} fw-bold fs-7 mb-1" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $client->company_name }}
                                        </a>
                                        <span class="text-muted fw-semibold fs-8">{{ $client->domain }}</span>
                                    </div>
                                    <div class="ms-2 d-flex flex-column align-items-end">
                                        <span class="badge badge-light-{{ $typeBadgeColor }} fw-bold px-2 py-1 mb-1">{{ $type }}</span>
                                        <span class="badge badge-light-{{ $badgeColor }} fw-bold px-2 py-1">
                                            {{ $daysLeft > 0 ? $daysLeft . ' ' . __('gün kaldı') : __('Süresi Doldu') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex flex-column justify-content-center align-items-center text-center h-100">
                                <span class="text-muted fw-semibold fs-7">{{ __('Yakın zamanda yenilenecek ürün bulunmuyor.') }}</span>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orta: Profil Kartı (col-4) --}}
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-header pt-9 justify-content-center">
                        <div class="card-title d-flex flex-column align-items-center">
                            <div class="mb-2" style="width:150px;height:150px;flex-shrink:0;">
                                <img src="{{ auth()->user()->avatar_url }}" alt="avatar" style="width:150px;height:150px;border-radius:12px;object-fit:cover;border:3px solid #E4E6EF;" />
                            </div>
                            <span class="fs-4 fw-bold text-gray-900">{{ __('CodeGravity Yönetimi') }}</span>
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 mt-1">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    <div class="card-body pb-9 pt-4">
                        <div class="separator separator-dashed my-2"></div>
                        <div class="d-flex flex-column gap-2 mt-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('Toplam Kullanıcı Sayısı:') }}</span>
                                <span class="badge badge-light-primary fw-bold fs-8">{{ $totalUsers ?? 0 }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('Sistem Versiyonu:') }}</span>
                                <span class="text-gray-800 fw-bold fs-7">v1.0.0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('Son Giriş:') }}</span>
                                <span class="text-gray-800 fw-bold fs-7">{{ now()->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sağ: TRT Haber RSS Kartı (col-4) --}}
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px pt-0 pb-0">
                        <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                            <span class="d-flex text-white fw-bolder fs-6">{{ __('Son Dakika Haberler') }} - TRT</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed border-gray-200"></div>
                    <div class="card-body pt-6 pb-6">
                        <div class="pe-3" style="max-height: 350px; overflow-y: auto;">
                        @if(!empty($trt_news))
                            @foreach($trt_news as $news)
                                <div class="d-flex align-items-center mb-4">
                                    <span class="bullet bullet-vertical h-40px bg-danger me-3"></span>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <a href="{{ $news['link'] }}" target="_blank" class="text-gray-800 text-hover-danger fw-bold fs-7 mb-1" title="{{ $news['title'] }}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $news['title'] }}
                                        </a>
                                        <span class="text-muted fw-semibold fs-8">{{ $news['date'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex flex-column justify-content-center align-items-center text-center h-100">
                                <span class="text-muted fw-semibold fs-7">{{ __('Haberler yüklenemedi.') }}</span>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
