@extends('layouts.backend')

@section('title', __('Medya Galerisi'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Medya Galerisi') }}</h1>
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
                <div class="alert alert-dismissible bg-light-info border border-info border-dashed d-flex flex-column flex-sm-row p-5 mb-5">
            <i class="ki-duotone ki-information-5 fs-2hx text-info me-4 mb-2 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column text-info">
                <h4 class="fw-bold text-info">@ {{ __('Bilgilendirme') }}</h4>
                <span>- {{ __('Bu modül üzerinden işletmenize ait fotoğraf, video ve diğer görsel içerikleri yönetebilirsiniz. Medya içeriklerine ait başlıklar, açıklamalar, dosyalar, sıralamalar ve yayın durumları bu modül üzerinden düzenlenebilir ve güncel tutulabilir.') }}</span>
            </div>
        </div>
<div class="card card-flush mb-5 mb-xl-10">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Medya Galerisi Tablosu') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1 me-5">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <input type="text" data-kt-user-table-filter="search" class="form-control form-control-sm form-control-solid w-250px ps-13" placeholder="{{ __('Medya Galerisi Ara') }}" id="user-search-input" />
                    </div>
                    @if(auth()->user()->can('media.manage'))
                    @if(\App\Models\Media::count() > 1)
                    <a href="{{ route($routePrefix . 'media.reorder') }}" class="btn btn-icon btn-light-primary btn-sm me-3" title="{{ __('Medyaları Sırala') }}">
                        <i class="ki-duotone ki-dots-square fs-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                        </i>
                    </a>
                    @endif
                    <a href="{{ route($routePrefix . 'media.create') }}" class="btn btn-icon btn-light-success btn-sm me-3" title="{{ __('Medya Galerisi Ekle') }}">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </a>
                    @else
                    @if(\App\Models\Media::count() > 1)
                    <a href="javascript:void(0)" class="btn btn-icon btn-light-primary btn-sm me-3 unauthorized-action-btn" title="{{ __('Medyaları Sırala') }}">
                        <i class="ki-duotone ki-dots-square fs-2">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                        </i>
                    </a>
                    @endif
                    <a href="javascript:void(0)" class="btn btn-icon btn-light-success btn-sm me-3 unauthorized-action-btn" title="{{ __('Medya Galerisi Ekle') }}">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </a>
                    @endif
                    <button type="button" class="btn btn-icon btn-light-warning btn-sm" id="kt_media_table_reload" title="{{ __('Tabloyu Yenile') }}">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6 pb-4">
                <table class="table align-middle table-row-dashed fs-6 gy-2" id="kt_media_table">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">REF NO</th>
                            <th class="min-w-125px">{{ __('Medya Başlığı') }}</th>
                            <th class="min-w-100px">{{ __('Durum') }}</th>
                            <th class="min-w-125px">{{ __('Kayıt Tarihi') }}</th>
                            <th class="text-end min-w-125px">{{ __('İşlemler') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('themes/backend/metronic/assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('scripts')
<script src="{{ asset('themes/backend/metronic/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('themes/backend/metronic/assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#kt_media_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routePrefix . 'media.index') }}",
            lengthChange: true,
            order: [[0, 'desc']],
            columns: [
                {data: 'id', name: 'id', width: '5%', className: 'text-start align-middle'},
                {data: 'title', name: 'title', className: 'align-middle'},
                {data: 'status', name: 'status', className: 'align-middle'},
                {data: 'created_at', name: 'created_at', className: 'align-middle'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '12%', className: 'text-end align-middle'},
            ],
            language: {
                emptyTable: "{{ __('Tabloda herhangi bir veri mevcut değil') }}",
                info: "{{ __('_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor') }}",
                infoEmpty: "{{ __('Kayıt yok') }}",
                infoFiltered: "{{ __('(_MAX_ kayıt içerisinden bulunan)') }}",
                infoPostFix: "",
                thousands: ".",
                lengthMenu: "_MENU_",
                loadingRecords: "{{ __('Yükleniyor...') }}",
                processing: "{{ __('İşleniyor...') }}",
                search: "{{ __('Ara:') }}",
                zeroRecords: "{{ __('Eşleşen kayıt bulunamadı') }}",
                paginate: {
                    first: "{{ __('İlk') }}",
                    last: "{{ __('Son') }}",
                    next: "{{ __('Sonraki') }}",
                    previous: "{{ __('Önceki') }}"
                },
                aria: {
                    sortAscending: "{{ __(': artan sütun sıralamasını aktifleştir') }}",
                    sortDescending: "{{ __(': azalan sütun sıralamasını aktifleştir') }}"
                }
            },
            drawCallback: function(settings) {
                if (typeof refreshFsLightbox === 'function') {
                    refreshFsLightbox();
                }
            }
        });

        $('#user-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#kt_media_table_reload').on('click', function() {
            table.ajax.reload();
        });

        // Silme İşlemi
        $(document).on('click', '.delete-record', function() {
            var id = $(this).data('id');
            var url = "{{ route($routePrefix . 'media.destroy', ':id') }}";
            url = url.replace(':id', id);
            SwalHelper.deleteConfirmation(url, table);
        });
    });
</script>
@endpush
