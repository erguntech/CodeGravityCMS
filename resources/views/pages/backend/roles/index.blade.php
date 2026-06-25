@extends('layouts.backend')

@section('title', __('Kullanıcı Rolleri'))

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Kullanıcı Rolleri') }}</h1>
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
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Kullanıcı Rolleri Tablosu') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <div class="d-flex align-items-center position-relative my-1 me-5">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <input type="text" data-kt-role-table-filter="search" class="form-control form-control-sm form-control-solid w-250px ps-13" placeholder="{{ __('Kullanıcı Rolü Ara') }}" id="role-search-input" />
                    </div>
                    @if(auth()->user()->can('roles.manage'))
                    <a href="{{ route($routePrefix . 'roles.create') }}" class="btn btn-icon btn-light-success btn-sm me-3" title="Rol Ekle">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </a>
                    @else
                    <a href="javascript:void(0)" class="btn btn-icon btn-light-success btn-sm me-3 unauthorized-action-btn" title="Rol Ekle">
                        <i class="ki-duotone ki-plus fs-2"></i>
                    </a>
                    @endif
                    <button type="button" class="btn btn-icon btn-light-warning btn-sm" id="kt_roles_table_reload" title="Tabloyu Yenile">
                        <i class="ki-duotone ki-arrows-circle fs-2">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6 pb-4">
                <table class="table align-middle table-row-dashed fs-6 gy-2" id="kt_roles_table">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-50px">REF NO</th>
                            <th class="min-w-200px">Rol Adı</th>
                            <th class="min-w-250px">İzinler</th>
                            <th class="text-end min-w-100px" style="width: 10%">İşlemler</th>
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
        var table = $('#kt_roles_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route($routePrefix . 'roles.index') }}",
            lengthChange: true,
            columns: [
                {data: 'id', name: 'id', width: '5%', className: 'text-start align-middle'},
                {data: 'name', name: 'name', className: 'align-middle'},
                {data: 'permissions', name: 'permissions', className: 'align-middle'},
                {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%', className: 'text-end align-middle'},
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

        $('#role-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        $('#kt_roles_table_reload').on('click', function() {
            table.ajax.reload();
        });

        // Silme İşlemi
        $(document).on('click', '.delete-role', function() {
            var id = $(this).data('id');
            var url = "{{ route($routePrefix . 'roles.destroy', ':id') }}";
            url = url.replace(':id', id);
            SwalHelper.deleteConfirmation(url, table);
        });
    });
</script>
@endpush

