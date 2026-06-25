@extends('layouts.backend')

@section('title', __('Satın Alma Kayıtları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Satın Alma Kayıtları') }}</h1>
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
            <!-- Header with Ribbon -->
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Satın Alma Kayıtları') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1 me-5">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <input type="text" id="purchases-search-input" class="form-control form-control-sm form-control-solid w-250px ps-13" placeholder="{{ __('Satın Alma Ara...') }}" />
                    </div>
                    <button type="button" class="btn btn-icon btn-light-warning btn-sm" id="kt_purchases_table_reload" title="{{ __('Tabloyu Yenile') }}">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </div>
            </div>

            <div class="separator separator-dashed border-gray-200"></div>

            <!-- Card Body -->
            <div class="card-body pt-6 pb-4">
                <table class="table align-middle table-row-dashed fs-6 gy-3" id="kt_purchases_table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th>REF ID</th>
                            <th>{{ __('Kullanıcı') }}</th>
                            <th>{{ __('Yükseltme Türü') }}</th>
                            <th>{{ __('Yükseltme Planı') }}</th>
                            <th>{{ __('Hedef Model / Ajans') }}</th>
                            <th>{{ __('Kredi Tutarı') }}</th>
                            <th>{{ __('Tarih') }}</th>
                            <th class="text-end">{{ __('İşlemler') }}</th>
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
<script>
$(document).ready(function() {
    var table = $('#kt_purchases_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route($routePrefix . 'upgrades.purchases') }}",
        lengthChange: true,
        order: [[0, 'desc']],
        columns: [
            {data: 'id', name: 'id', className: 'align-middle'},
            {data: 'user_info', name: 'user.name', className: 'align-middle'},
            {data: 'feature_name', name: 'feature.name', className: 'align-middle'},
            {data: 'plan_name', name: 'plan.name', className: 'align-middle'},
            {data: 'target', name: 'target', orderable: false, searchable: false, className: 'align-middle'},
            {data: 'price_credit', name: 'price_credit', className: 'align-middle'},
            {data: 'created_at', name: 'created_at', className: 'align-middle'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end align-middle'},
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
        }
    });

    $('#purchases-search-input').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#kt_purchases_table_reload').on('click', function() {
        table.ajax.reload();
    });

    // Handle delete purchase transaction using SwalHelper
    $(document).on('click', '.delete-purchase', function() {
        var id = $(this).data('id');
        var url = "{{ route($routePrefix . 'upgrades.purchases.destroy', ':id') }}";
        url = url.replace(':id', id);
        
        SwalHelper.deleteConfirmation(url, table, {
            title: "{{ __('Emin misiniz?') }}",
            text: "{{ __('Bu satın alma kaydı kalıcı olarak silinecektir!') }}",
            confirmButtonText: "{{ __('Evet, Sil!') }}",
            cancelButtonText: "{{ __('İptal') }}"
        });
    });
});
</script>
@endpush
