@extends('layouts.backend')

@section('title', __('Ürün Sıralama'))

@push('styles')
<style>
    .drag-handle {
        cursor: move !important;
    }
    .object-fit-cover {
        object-fit: cover !important;
    }
</style>
@endpush

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Ürün Sıralama') }}</h1>
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
        
        @if(!$products->isEmpty())
            <!-- Info Alert Box -->
            <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-5 mb-4">
                <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4 mb-2 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column text-warning">
                    <h4 class="fw-bold text-warning">@ Bilgilendirme</h4>
                    <span>- Ürünlerin sol tarafındaki taşıma ikonunu (handle) kullanarak sürükle-bırak yöntemiyle sıralamalarını değiştirebilirsiniz.</span>
                    <span>- Yapılan sıralama değişiklikleri arka planda otomatik olarak kaydedilmektedir.</span>
                </div>
            </div>
        @endif

        <div class="card card-flush mb-5 mb-xl-10">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Ürün Sıralama Bilgileri') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar">
                    <a href="{{ route($routePrefix . 'products.index') }}" class="btn btn-sm btn-light btn-active-light-primary" style="border-radius: 4px;">{{ __('Geri Dön') }}</a>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body py-6">
                
                @if($products->isEmpty())
                    <!-- Alert Box -->
                    <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-0">
                        <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4 mb-2 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-column text-danger">
                            <h4 class="fw-bold text-danger">@ Dikkat</h4>
                            <span>- Sıralanacak herhangi bir ürün bulunamadı.</span>
                        </div>
                    </div>
                @else

                    <!-- Sortable Vertical List -->
                    <div class="d-flex flex-column gap-3 mb-0" id="kt_products_sortable">
                        @foreach($products as $product)
                            <div class="d-flex align-items-center justify-content-between border border-gray-300 rounded p-4 bg-light" data-id="{{ $product->id }}">
                                <div class="d-flex align-items-center gap-4">
                                    <span class="btn btn-icon btn-sm btn-active-color-primary drag-handle" title="{{ __('Taşı') }}">
                                        <i class="ki-duotone ki-element-11 fs-3">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                        </i>
                                    </span>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-40px h-40px rounded object-fit-cover" />
                                    @else
                                        <div class="w-40px h-40px rounded bg-secondary d-flex align-items-center justify-content-center text-muted fw-bold">
                                            {{ substr($product->title, 0, 1) }}
                                        </div>
                                    @endif
                                    <span class="fw-bold text-gray-800 fs-6">{{ $product->title }}</span>
                                    <span class="badge badge-light-secondary fs-8">{{ $product->category ? $product->category->title : '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge badge-light-{{ $product->status == 'active' ? 'success' : 'danger' }} fw-bold">{{ $product->status == 'active' ? __('Aktif') : __('Pasif') }}</span>
                                    <span class="badge badge-light-primary fw-bold">#{{ $product->id }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        var el = document.getElementById('kt_products_sortable');
        if (el) {
            var sortable = Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function (evt) {
                    var order = [];
                    $('#kt_products_sortable > div').each(function() {
                        order.push($(this).data('id'));
                    });
                    
                    $.ajax({
                        url: "{{ route($routePrefix . 'products.reorder.update') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order: order
                        },
                        success: function(response) {
                            // Silent update
                        },
                        error: function() {
                            // Silent error or standard console log
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
