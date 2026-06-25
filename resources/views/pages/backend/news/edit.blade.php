@extends('layouts.backend')

@section('title', __('Haber Düzenleme'))

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
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Haber Düzenleme') }}</h1>
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
        @php
            $clientLanguages = auth()->user()->client?->languages()->where('is_active', true)->get();
            if (!$clientLanguages || $clientLanguages->isEmpty()) {
                $clientLanguages = collect([(object)['code' => 'tr', 'is_default' => true]]);
            }
            $clientLanguages = $clientLanguages->sortByDesc('is_default')->values();

            $activeTabCode = null;
            if ($errors->any()) {
                foreach($clientLanguages as $lang) {
                    if ($errors->has('title.'.$lang->code) || $errors->has('description.'.$lang->code)) {
                        $activeTabCode = $lang->code;
                        break;
                    }
                }
            }
            if (!$activeTabCode) {
                $activeTabCode = $clientLanguages->where('is_default', true)->first()?->code ?? $clientLanguages->first()?->code ?? 'tr';
            }
        @endphp

        @php
            $newsImageSize = auth()->user()->client?->getImageSize('news');
        @endphp
        @if($newsImageSize)
            <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-5 mb-4">
                <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4 mb-2 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column text-warning">
                    <h4 class="fw-bold text-warning">@ Bilgilendirme</h4>
                    <span>- Yükleyeceğiniz manşet resmi, belirlenen en boy oranına (<strong>{{ $newsImageSize['width'] }}x{{ $newsImageSize['height'] }} px</strong>) göre sistem tarafından otomatik olarak kesilecektir (crop).</span>
                </div>
            </div>
        @endif
        <div class="card card-flush mb-5 mb-xl-10">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Haber Bilgileri') }}</span>
                </div>
                <div class="card-title"></div>
                                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                        @foreach($clientLanguages as $lang)
                        <li class="nav-item">
                            <a class="nav-link {{ $activeTabCode === $lang->code ? 'active' : '' }} d-flex align-items-center me-2 p-2" data-bs-toggle="tab" href="#lang_tab_{{ $lang->code }}">
                                @php $langConfig = config("languages.{$lang->code}"); @endphp
                                @if(isset($langConfig['icon']))
                                    <img src="{{ asset($langConfig['icon']) }}" class="w-25px h-25px rounded-1" alt="{{ $lang->code }}">
                                @else
                                    <span class="text-uppercase fw-bold">{{ $lang->code }}</span>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                <form action="{{ route($routePrefix . 'news.update', $news->id) }}" method="POST" id="kt_news_edit_form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12 mb-6">
                            <div class="tab-content">
                                @foreach($clientLanguages as $lang)
                                @php
                                    $titleVal = $news->getTranslation('title', $lang->code, false);
                                    $descVal = $news->getTranslation('description', $lang->code, false);
                                @endphp
                                <div class="tab-pane fade {{ $activeTabCode === $lang->code ? 'show active' : '' }}" id="lang_tab_{{ $lang->code }}" role="tabpanel">
                                    <div class="mb-6">
                                        <label class="form-label {{ $lang->is_default ? 'required' : '' }}">{{ __('Haber Başlığı') }} ({{ strtoupper($lang->code) }})</label>
                                        <input type="text" name="title[{{ $lang->code }}]" class="form-control form-control-solid @error('title.'.$lang->code) border-danger @enderror" placeholder="{{ __('Haber Başlığı Giriniz') }}" value="{{ old('title.'.$lang->code, $titleVal) }}" />
                                        @error('title.'.$lang->code)
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">{{ __('Haber Açıklaması') }} ({{ strtoupper($lang->code) }})</label>
                                        <textarea name="description[{{ $lang->code }}]" class="form-control form-control-solid @error('description.'.$lang->code) border-danger @enderror" placeholder="{{ __('Haber Açıklaması Giriniz') }}" rows="4">{{ old('description.'.$lang->code, $descVal) }}</textarea>
                                        @error('description.'.$lang->code)
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        
                        
                        <div class="col-md-6 mb-6">
                            <label class="form-label required">{{ __('Durumu') }}</label>
                            <select name="status" class="form-select form-select-solid @error('status') border-danger @enderror" data-control="select2" data-hide-search="true" data-placeholder="{{ __('Seçim Yapınız') }}">
                                <option value=""></option>
                                <option value="active" {{ old('status', $news->status) == 'active' ? 'selected' : '' }}>{{ __('Aktif') }}</option>
                                <option value="passive" {{ old('status', $news->status) == 'passive' ? 'selected' : '' }}>{{ __('Pasif') }}</option>
                            </select>
                            @error('status')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-6">
                            <label class="form-label">
                                {{ __('Haber Manşet Resmi') }}
                                @if($news->image)
                                    <a data-fslightbox="news-edit-gallery" data-type="image" href="{{ asset('storage/' . $news->image) }}" class="text-primary fw-bold ms-2">({{ __('Mevcut Resim') }})</a>
                                @endif
                            </label>
                            <input type="file" name="image" class="form-control form-control-solid @error('image') border-danger @enderror" accept="image/*" />
                            @error('image')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('SEO Meta Başlığı') }}</label>
                            <input type="text" name="seo_title" class="form-control form-control-solid @error('seo_title') border-danger @enderror" placeholder="{{ __('SEO Meta Başlığı Giriniz') }}" value="{{ old('seo_title', $news->seo_title) }}" />
                        </div>
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('SEO Meta Etiketleri') }}</label>
                            <input type="text" name="seo_keywords" class="form-control form-control-solid @error('seo_keywords') border-danger @enderror" placeholder="{{ __('SEO Meta Etiketleri Giriniz') }}" value="{{ old('seo_keywords', $news->seo_keywords) }}" />
                        </div>
                        <div class="col-md-4 mb-0">
                            <label class="form-label">{{ __('SEO Meta Açıklaması') }}</label>
                            <input type="text" name="seo_description" class="form-control form-control-solid @error('seo_description') border-danger @enderror" placeholder="{{ __('SEO Meta Açıklaması Giriniz') }}" value="{{ old('seo_description', $news->seo_description) }}" />
                        </div>
                    </div>

                    <div class="separator separator-dashed my-10"></div>
                    
                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_news_edit_submit" class="btn btn-sm btn-warning me-3">
                            <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route($routePrefix . 'news.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('themes/backend/metronic/assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>
<script>
    const form = document.getElementById('kt_news_edit_form');
    const submitButton = document.getElementById('kt_news_edit_submit');

    form.addEventListener('submit', function (e) {
        submitButton.setAttribute('data-kt-indicator', 'on');
        submitButton.disabled = true;
    });
</script>
@endpush
