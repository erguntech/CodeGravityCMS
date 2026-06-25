@extends('layouts.backend')

@section('title', __('Log Kayıtları'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Log Kayıtları') }}</h1>
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
                <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Log Kayıtları Tablosu') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <input type="text" class="form-control form-control-sm form-control-solid w-250px ps-13" placeholder="{{ __('Log Kaydı Ara') }}" id="log-search-input" />
                    </div>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6 pb-4">
                <table class="table align-middle table-row-dashed fs-6 gy-2" id="kt_logs_table">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">REF NO</th>
                            <th class="min-w-100px">İşlem</th>
                            <th class="min-w-150px">Modül/Konu</th>
                            <th class="min-w-150px">Yapan Kişi</th>
                            <th class="min-w-150px">Tarih</th>
                            <th class="text-end min-w-50px">İşlemler</th>
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
        var table = $('#kt_logs_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routePrefix . 'logs.index') }}",
            lengthChange: true,
            columns: [
                {data: 'id', name: 'id', width: '5%', className: 'text-start align-middle'},
                {data: 'description', name: 'description', className: 'align-middle'},
                {data: 'subject', name: 'subject', className: 'align-middle'},
                {data: 'causer', name: 'causer', className: 'align-middle'},
                {data: 'created_at', name: 'created_at', className: 'align-middle'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '5%', className: 'text-end align-middle'},
            ],
            language: {
                emptyTable: "Tabloda herhangi bir veri mevcut değil",
                info: "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
                infoEmpty: "Kayıt yok",
                infoFiltered: "(_MAX_ kayıt içerisinden bulunan)",
                infoPostFix: "",
                thousands: ".",
                lengthMenu: "_MENU_",
                loadingRecords: "Yükleniyor...",
                processing: "İşleniyor...",
                search: "Ara:",
                zeroRecords: "Eşleşen kayıt bulunamadı",
                paginate: {
                    first: "İlk",
                    last: "Son",
                    next: "Sonraki",
                    previous: "Önceki"
                },
                aria: {
                    sortAscending: ": artan sütun sıralamasını aktifleştir",
                    sortDescending: ": azalan sütun sıralamasını aktifleştir"
                }
            }
        });

        $('#log-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        $(document).on('click', '.view-log-details', function() {
            var data = table.row($(this).parents('tr')).data();
            var properties = JSON.parse(JSON.stringify(data.properties));
            
            var content = '<div class="text-start fs-7"><pre class="bg-light p-5 rounded">' + JSON.stringify(properties, null, 2) + '</pre></div>';

            Swal.fire({
                title: 'İşlem Detayları',
                html: content,
                icon: 'info',
                confirmButtonText: 'Kapat'
            });
        });
    });
</script>
@endpush

