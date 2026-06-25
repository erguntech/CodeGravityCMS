@extends('layouts.backend')

@section('title', __('Müşteri Detayları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Müşteri Profili') }}</h1>
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
        <div class="d-flex flex-column flex-xl-row">
            <!-- Sidebar -->
            <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body pt-15">
                        <div class="d-flex flex-center flex-column mb-5">
                            <div class="symbol symbol-100px symbol-circle mb-7">
                                <span class="symbol-label bg-light-primary text-primary fs-2x fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{ $user->name }}</a>
                            <div class="fs-5 fw-semibold text-muted mb-6">{{ $user->user_type }}</div>
                        </div>

                        <div class="d-flex flex-stack fs-4 py-3">
                            <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_user_view_details" role="button" aria-expanded="false" aria-controls="kt_user_view_details">{{ __('Detaylar') }}
                            <span class="ms-2 rotate-180">
                                <i class="ki-duotone ki-down fs-3"></i>
                            </span></div>
                            <span data-bs-toggle="tooltip" data-bs-trigger="hover" title="{{ __('Düzenle') }}">
                                <a href="{{ route($routePrefix . 'clients.edit', $user->id) }}" class="btn btn-sm btn-light-primary">{{ __('Düzenle') }}</a>
                            </span>
                        </div>

                        <div class="separator separator-dashed my-3"></div>

                        <div id="kt_user_view_details" class="collapse show">
                            <div class="pb-5 fs-6">
                                <div class="fw-bold mt-5">{{ __('Hesap ID') }}</div>
                                <div class="text-gray-600">ID-{{ $user->id }}</div>
                                <div class="fw-bold mt-5">{{ __('E-posta') }}</div>
                                <div class="text-gray-600">
                                    <a href="#" class="text-gray-600 text-hover-primary">{{ $user->email }}</a>
                                </div>

                                <div class="fw-bold mt-5">{{ __('Durum') }}</div>
                                <div class="text-gray-600">
                                    <span class="badge badge-light-{{ $user->status === 'active' ? 'success' : 'danger' }}">{{ __($user->status) }}</span>
                                </div>
                                <div class="fw-bold mt-5">{{ __('Kayıt Tarihi') }}</div>
                                <div class="text-gray-600">{{ $user->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-lg-row-fluid ms-lg-15">
                <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab" href="#kt_user_view_overview_tab">{{ __('Genel Bakış') }}</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="kt_user_view_overview_tab" role="tabpanel">
                        <div class="card card-flush mb-6 mb-xl-9">
                            <div class="card-header mt-6">
                                <div class="card-title flex-column">
                                    <h2 class="mb-1">{{ __('Müşteri Özeti') }}</h2>
                                    <div class="fs-6 fw-semibold text-muted">{{ __('Bu müşteriye ait özet bilgiler') }}</div>
                                </div>
                            </div>
                            <div class="card-body p-9 pt-4">
                                <p class="text-gray-600">Bu müşteriye ait özel veriler (randevular, siparişler, vs.) eklendiğinde burada görüntülenecektir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
