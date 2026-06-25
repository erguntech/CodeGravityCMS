@if (session('success'))
<div class="alert alert-dismissible bg-light-success border border-success border-dashed d-flex flex-column flex-sm-row align-items-sm-center w-100 p-5 mb-5">
    <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
    <div class="d-flex flex-column pe-0 pe-sm-10" style="margin-top: 2px;">
        <h5 class="mb-1 text-success">{{ __('İşlem Başarılı!') }}</h5>
        <span class="text-gray-800">{{ session('success') }}</span>
    </div>
    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
        <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
    </button>
</div>
@endif

@if (session('warning'))
<div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row align-items-sm-center w-100 p-5 mb-5">
    <i class="ki-duotone ki-pencil fs-2hx text-warning me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
    <div class="d-flex flex-column pe-0 pe-sm-10" style="margin-top: 2px;">
        <h5 class="mb-1 text-warning">{{ __('İşlem Başarılı!') }}</h5>
        <span class="text-gray-800">{{ session('warning') }}</span>
    </div>
    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
        <i class="ki-duotone ki-cross fs-1 text-warning"><span class="path1"></span><span class="path2"></span></i>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row align-items-sm-center w-100 p-5 mb-5">
    <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
    <div class="d-flex flex-column pe-0 pe-sm-10" style="margin-top: 2px;">
        <h5 class="mb-1 text-danger">{{ (str_contains(session('error'), 'Medya Sınır Aşımı!') || str_contains(session('error'), 'Medya Limit Aşımı!') || str_contains(session('error'), 'limitine ulaştınız')) ? __('Medya Limit Aşımı!') : __('Hata') }}</h5>
        <span class="text-gray-800">{!! session('error') !!}</span>
    </div>
    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
        <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
    </button>
</div>
@endif

@if (session('info'))
<div class="alert alert-dismissible bg-light-info border border-info border-dashed d-flex flex-column flex-sm-row w-100 p-5 mb-5">
    <i class="ki-duotone ki-information fs-2hx text-info me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
    <div class="d-flex flex-column pe-0 pe-sm-10">
        <h5 class="mb-1 text-info">{{ __('Bilgi') }}</h5>
        <span class="text-gray-800">{{ session('info') }}</span>
    </div>
    <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
        <i class="ki-duotone ki-cross fs-1 text-info"><span class="path1"></span><span class="path2"></span></i>
    </button>
</div>
@endif
