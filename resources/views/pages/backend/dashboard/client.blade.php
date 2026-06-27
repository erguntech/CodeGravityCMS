@extends('layouts.backend')
<?php \Carbon\Carbon::setLocale('tr'); ?>



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
                <h4 class="fw-semibold text-primary">{{ __('Sn.') }} <span class="text-warning">{{ auth()->user()->client?->company_name ?? auth()->user()->name }}</span>, {{ __('Hoş Geldiniz!') }}</h4>
                <span>{{ __('Sisteminizi bu panelden kontrol edebilir ve yönetebilirsiniz.') }}</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="ki-duotone ki-cross fs-1 text-primary"><span class="path1"></span><span class="path2"></span></i>
            </button>
        </div>

        <div class="row g-5 g-xl-10 mb-5">

            {{-- Sol: Google Analytics Kartı (col-4) --}}
            <div class="col-md-4">
                <div class="card card-flush h-100">
                    <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px pt-0 pb-0">
                        <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                            <span class="d-flex text-white fw-bolder fs-6">{{ __('Ziyaretçi İstatistikleri') }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed border-gray-200"></div>
                    <div class="card-body pt-0 pb-0 px-3 d-flex flex-column justify-content-center">
                        <div id="kt_analytics_chart" style="height: 260px; width: 100%; margin-top: 20px; margin-bottom: 15px;"></div>
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
                            <div class="d-flex align-items-center justify-content-center"><span class="fs-4 fw-bold text-gray-900" id="kt_typedjs_profile_name"></span></div>
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 mt-1">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                    <div class="card-body pb-9 pt-4">
                        <div class="separator separator-dashed my-2"></div>
                          <div class="d-flex flex-column gap-2" style="margin-top: 2rem;">
                            @php
                                $client = auth()->user()->client;
                                $domainDays = $client?->domainRemainingDays();
                                $sslDays    = $client?->sslRemainingDays();
                            @endphp


                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('Domain:') }}</span>
                                @if($client?->domain)
                                    <span class="badge badge-light-info fw-bold fs-7">{{ $client->domain }}</span>
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-8">{{ __('Veri Girilmemiş') }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('Domain Kalan Süre:') }}</span>
                                @if($domainDays !== null)
                                    <span class="badge badge-light-{{ $domainDays <= 30 ? 'danger' : 'success' }} fw-bold fs-8">
                                        {{ $domainDays > 0 ? $domainDays . ' ' . __('gün') : __('Süresi Dolmuş') }}
                                    </span>
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-8">{{ __('Veri Girilmemiş') }}</span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-gray-500 fw-semibold fs-7">{{ __('SSL Kalan Süre:') }}</span>
                                @if($sslDays !== null)
                                    <span class="badge badge-light-{{ $sslDays <= 30 ? 'danger' : 'success' }} fw-bold fs-8">
                                        {{ $sslDays > 0 ? $sslDays . ' ' . __('gün') : __('Süresi Dolmuş') }}
                                    </span>
                                @else
                                    <span class="badge badge-light-danger fw-bold fs-8">{{ __('Veri Girilmemiş') }}</span>
                                @endif
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

        {{-- Alt Kayan Bilgi Bandı (Premium Corporate) --}}
        <div class="row mt-0">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-center p-4 mb-0 rounded" style="background-color: #1e1e2d; border: 1px solid var(--bs-primary); box-shadow: 0 0 12px rgba(13, 110, 253, 0.1);">
                    <marquee behavior="scroll" direction="left" scrollamount="5" class="fw-semibold text-white fs-6 mb-0 w-100" style="line-height: 1.5; display: flex; align-items: center;">
                        <span class="d-inline-flex align-items-center">
                            🔗 <span class="ms-2 me-1">{{ __('Domain') }}:</span> <span class="text-success fw-bold">{{ auth()->user()->client?->domain ?? __('Belirtilmemiş') }}</span>
                            <span class="text-muted mx-5">•</span>
                            
                            🌐 <span class="ms-2 me-1">{{ __('Domain Süresi') }}:</span> 
                            @if(isset($domainDays)) 
                                <span class="text-{{ $domainDays > 30 ? 'success' : 'danger' }} fw-bold">{{ $domainDays > 0 ? $domainDays . ' ' . __('Gün') : __('Süresi Dolmuş') }}</span> 
                            @else 
                                <span class="text-muted fw-bold">{{ __('Veri Yok') }}</span> 
                            @endif
                            <span class="text-muted mx-5">•</span>

                            🔒 <span class="ms-2 me-1">{{ __('SSL Durumu') }}:</span> 
                            @if(isset($sslDays)) 
                                <span class="text-{{ $sslDays > 30 ? 'success' : 'danger' }} fw-bold">{{ $sslDays > 0 ? __('Aktif') : __('Pasif') }}</span> 
                            @else 
                                <span class="text-muted fw-bold">{{ __('Veri Yok') }}</span> 
                            @endif
                            <span class="text-muted mx-5">•</span>

                            📈 <span class="ms-2 me-1">{{ __('Bu Ay') }}:</span> <span class="text-success fw-bold">{{ $totalMonthlyVisits ?? 0 }} {{ __('Ziyaretçi') }}</span>
                            
                            @if(isset($finans) && $finans)
                            <span class="text-muted mx-5">•</span>
                            💵 <span class="ms-2 me-1">USD:</span> <span class="text-success fw-bold">{{ $finans['USD'] }} ₺</span>
                            <span class="text-muted mx-5">•</span>
                            
                            💶 <span class="ms-2 me-1">EUR:</span> <span class="text-success fw-bold">{{ $finans['EUR'] }} ₺</span>
                            <span class="text-muted mx-5">•</span>
                            
                            🥇 <span class="ms-2 me-1">Altın:</span> <span class="text-success fw-bold">{{ $finans['Gold'] }} ₺</span>
                            @endif
                        </span>
                    </marquee>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('themes/backend/metronic/assets/plugins/custom/typedjs/typedjs.bundle.js') }}"></script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
        var typed = new Typed("#kt_typedjs_profile_name", {
            strings: [
                "{{ __('Hoş Geldiniz!') }}", 
                "{{ auth()->user()->client?->company_name ?? auth()->user()->name }}",
                "Sistem Saati: {{ \Carbon\Carbon::now()->timezone('Europe/Istanbul')->format('H:i') }}",
                "Bugün Günlerden {{ \Carbon\Carbon::now()->timezone('Europe/Istanbul')->translatedFormat('l') }}",
                "Kolay Gelsin. İyi Çalışmalar..."
            ],
            typeSpeed: 50,
            backSpeed: 30,
            backDelay: 2000,
            loop: true
        });
    });
</script>


@endpush



@push('scripts')
<script>
    var initAnalyticsChart = function() {
        var element = document.getElementById('kt_analytics_chart');
        if (!element) {
            return;
        }

        var visitsData = {!! $visits_data ?? '[]' !!};
        var visitsDates = {!! $visits_dates ?? '[]' !!};

        var options = {
            series: [{
                name: 'Ziyaretçiler',
                data: visitsData
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: 260,
                toolbar: { show: false }
            },
            plotOptions: { },
            legend: { show: false },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: ['#009EF7']
            },
            xaxis: {
                categories: visitsDates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#A1A5B7', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#A1A5B7', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: '#E4E6EF',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: false } },
                padding: { top: -20, bottom: -15, left: -10, right: 0 }
            },
            colors: ['#009EF7'],
            tooltip: {
                style: {
                    fontSize: '12px',
                    fontFamily: 'inherit'
                },
                y: {
                    formatter: function (val) {
                        return val + " kişi"
                    }
                }
            }
        };

        var chart = new ApexCharts(element, options);
        chart.render();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        initAnalyticsChart();
    });
</script>
@endpush
