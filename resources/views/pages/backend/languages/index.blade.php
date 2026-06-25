@extends('layouts.backend')

@section('title', __('Dil Ayarları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Dil Ayarları') }}</h1>
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
                    <span class="d-flex text-white fw-bolder fs-6">Sistem Dilleri</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar"></div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>

            <div class="card-body pt-6">
                @if($clientLanguages->isEmpty())
                    <div class="alert alert-warning">
                        {{ __('Hesabınıza tanımlanmış herhangi bir dil bulunmamaktadır. Lütfen yönetici ile iletişime geçin.') }}
                    </div>
                @else
                    <form action="{{ route('client.languages.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            @foreach($clientLanguages as $cl)
                                @php
                                    $langConfig = config("languages.{$cl->code}");
                                    $name = $langConfig['name'] ?? strtoupper($cl->code);
                                    $icon = isset($langConfig['icon']) ? asset($langConfig['icon']) : null;
                                @endphp
                                <div class="col-12 {{ $loop->last ? 'mb-0' : 'mb-6' }}">
                                    <div class="d-flex align-items-center bg-light rounded p-5 border border-dashed border-gray-300">
                                        <!-- Language Info -->
                                        <div class="d-flex align-items-center flex-grow-1">
                                            @if($icon)
                                                <img src="{{ $icon }}" class="w-30px h-30px rounded-1 me-4" alt="{{ $name }}"/>
                                            @endif
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-900 fw-bold fs-5">{{ $name }}</span>
                                                <span class="text-muted fs-7">{{ strtoupper($cl->code) }}</span>
                                            </div>
                                        </div>

                                        <!-- Default Radio -->
                                        <div class="d-flex align-items-center me-10">
                                            <div class="form-check form-check-custom form-check-solid form-check-success me-3">
                                                <input class="form-check-input default-language-radio" type="radio" name="default_language" value="{{ $cl->code }}" id="default_{{ $cl->code }}" data-code="{{ $cl->code }}" {{ $cl->is_default ? 'checked' : '' }} required />
                                            </div>
                                            <label class="form-check-label text-gray-700 fw-semibold cursor-pointer" for="default_{{ $cl->code }}">{{ __('Varsayılan (Favori)') }}</label>
                                        </div>

                                        <!-- Active Switch -->
                                        <div class="d-flex align-items-center w-100px justify-content-end">
                                            <div class="form-check form-switch form-check-custom form-check-solid me-3">
                                                <input class="form-check-input language-active-switch" type="checkbox" name="languages[{{ $cl->code }}][is_active]" value="1" id="active_{{ $cl->code }}" {{ $cl->is_active ? 'checked' : '' }} {{ $cl->is_default ? 'disabled' : '' }} />
                                            </div>
                                            <label class="form-check-label text-gray-700 fw-semibold cursor-pointer" for="active_{{ $cl->code }}">{{ __('Aktif') }}</label>
                                            
                                            <!-- Hidden input to guarantee submission if disabled -->
                                            <input type="hidden" name="languages[{{ $cl->code }}][is_active]" id="hidden_active_{{ $cl->code }}" value="1" {{ $cl->is_default ? '' : 'disabled' }} />
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Auto Translate Setting -->
                        <div class="row mt-6">
                            <div class="col-md-12">
                                <div class="alert alert-warning d-flex align-items-center p-5 mb-6">
                                    <i class="ki-duotone ki-information fs-2hx text-warning me-4">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-warning">{{ __('Bilgilendirme') }}</h4>
                                        <span>{{ __('Otomatik çeviri aktif olduğunda, ana dil haricindeki dillerin form alanlarına veri girmemeniz durumunda ilgili alanlar kaydedilirken sistem tarafından otomatik olarak çevrilecektir.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label required">{{ __('Otomatik Çeviri') }}</label>
                                <select name="auto_translate" class="form-select form-select-solid @error('auto_translate') border-danger @enderror" data-control="select2" data-hide-search="true">
                                    <option value="1" {{ old('auto_translate', auth()->user()->client?->auto_translate ?? 1) == 1 ? 'selected' : '' }}>{{ __('Evet') }}</option>
                                    <option value="0" {{ old('auto_translate', auth()->user()->client?->auto_translate ?? 1) == 0 ? 'selected' : '' }}>{{ __('Hayır') }}</option>
                                </select>
                                @error('auto_translate')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="separator separator-dashed my-10"></div>

                        <div class="d-flex justify-content-start">
                            <button type="submit" id="kt_languages_submit" class="btn btn-sm btn-warning">
                                <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const defaultRadios = document.querySelectorAll('.default-language-radio');
        const activeSwitches = document.querySelectorAll('.language-active-switch');
        const hiddenInputs = document.querySelectorAll('input[type="hidden"][id^="hidden_active_"]');

        defaultRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedCode = this.getAttribute('data-code');

                // Enable all switches and disable their hidden inputs
                activeSwitches.forEach(sw => {
                    sw.disabled = false;
                });
                hiddenInputs.forEach(hi => {
                    hi.disabled = true;
                });

                // Check and disable the switch for the default language, enable its hidden input
                const selectedSwitch = document.getElementById('active_' + selectedCode);
                const selectedHidden = document.getElementById('hidden_active_' + selectedCode);
                
                if (selectedSwitch) {
                    selectedSwitch.checked = true;
                    selectedSwitch.disabled = true;
                }
                if (selectedHidden) {
                    selectedHidden.disabled = false;
                }
            });
        });
    });
</script>
@endpush
