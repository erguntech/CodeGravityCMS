@extends('layouts.backend')

@section('title', __('Ürün Galerisi Resimleri'))

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
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('Ürün Galerisi Resimleri') }}</h1>
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
        
        <!-- Info Alert Box -->
        @php
            $client = auth()->user()->client;
            $sizeStr = ($client && isset($client->image_sizes['product_gallery'])) ? $client->image_sizes['product_gallery'] . ' px' : null;
        @endphp
        <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-5 mb-4">
            <i class="ki-duotone ki-information-5 fs-2hx text-warning me-4 mb-2 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
            <div class="d-flex flex-column text-warning">
                <h4 class="fw-bold text-warning">@ Bilgilendirme</h4>
                <span>- Yüklenen resimler, sistemde tanımlanmış olan <strong>{{ $sizeStr ?? 'belirlenmiş' }}</strong> en-boy oranında otomatik olarak kesilecektir (crop).</span>
                <span>- Resimlerin sol alt köşesinde bulunan taşıma ikonunu (handle) kullanarak resimlerin sıralamasını dilediğiniz gibi değiştirebilirsiniz.</span>
            </div>
        </div>

        <div class="card card-flush mb-4">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-warning fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('Ürün Galerisi Resimleri') }}</span>
                </div>
                <div class="card-title"></div>
                <div class="card-toolbar"></div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6">
                
                <!-- Upload Form -->
                <form action="{{ route($routePrefix . 'products.gallery.store', $product->id) }}" method="POST" id="kt_products_gallery_upload_form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-0">
                        <label class="form-label required">{{ __('Resimleri Seçiniz') }}</label>
                        <input type="file" name="images[]" class="form-control form-control-solid @error('images') border-danger @enderror @error('images.*') border-danger @enderror" accept="image/*" multiple />
                        @error('images')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="separator separator-dashed my-10"></div>

                    <!-- Action buttons -->
                    <div class="d-flex justify-content-start">
                        <button type="submit" id="kt_products_gallery_upload_submit" class="btn btn-sm btn-warning me-3">
                            <span class="indicator-label">{{ __('Değişiklikleri Kaydet') }}</span>
                            <span class="indicator-progress">{{ __('Lütfen bekleyin...') }} 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <a href="{{ route($routePrefix . 'products.index') }}" class="btn btn-sm btn-light btn-active-light-primary">{{ __('Geri Dön') }}</a>
                    </div>
                </form>

            </div>
        </div>

        <!-- Gallery Grid Section -->
        @if(!$gallery->isEmpty())
            <!-- Sortable Grid (col 2 items -> 6 items per row on medium screen) -->
            <div class="row row-cols-2 row-cols-md-6 g-6 mb-10" id="kt_gallery_sortable">
                @foreach($gallery as $item)
                    <div class="col" data-id="{{ $item->id }}">
                        <div class="card shadow-sm border border-gray-300 rounded overflow-hidden position-relative">
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top h-150px object-fit-cover" alt="Gallery Image" />
                            <div class="card-body p-2 d-flex justify-content-between align-items-center bg-light">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="btn btn-icon btn-sm btn-active-color-primary drag-handle" title="{{ __('Taşı') }}">
                                        <i class="ki-duotone ki-element-11 fs-3">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
                                        </i>
                                    </span>
                                    <a data-fslightbox="product-gallery" href="{{ asset('storage/' . $item->image) }}" class="btn btn-icon btn-sm btn-active-color-success" title="{{ __('Önizleme') }}">
                                        <i class="ki-duotone ki-eye fs-3">
                                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                        </i>
                                    </a>
                                </div>
                                <button type="button" class="btn btn-icon btn-sm btn-light-danger btn-active-danger delete-gallery-item" data-id="{{ $item->id }}" title="{{ __('Sil') }}">
                                    <i class="ki-duotone ki-trash fs-3">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('themes/backend/metronic/assets/plugins/custom/fslightbox/fslightbox.bundle.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        // Upload Button Loading State
        const form = document.getElementById('kt_products_gallery_upload_form');
        const submitButton = document.getElementById('kt_products_gallery_upload_submit');

        if (form && submitButton) {
            form.addEventListener('submit', function (e) {
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;
            });
        }

        // Sortable Setup
        var el = document.getElementById('kt_gallery_sortable');
        if (el) {
            var sortable = Sortable.create(el, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function (evt) {
                    var order = [];
                    $('#kt_gallery_sortable > div').each(function() {
                        order.push($(this).data('id'));
                    });
                    
                    $.ajax({
                        url: "{{ route($routePrefix . 'products.gallery.reorder', $product->id) }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            order: order
                        },
                        success: function(response) {
                        },
                        error: function() {
                        }
                    });
                }
            });
        }

        // AJAX Delete Item
        $(document).on('click', '.delete-gallery-item', function() {
            var button = $(this);
            var id = button.data('id');
            var url = "{{ route($routePrefix . 'products.gallery.destroy', [$product->id, ':id']) }}";
            url = url.replace(':id', id);

            Swal.fire({
                title: "{{ __('Emin misiniz?') }}",
                text: "{{ __('Bu resmi galeriden silmek istediğinize emin misiniz?') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{ __('Evet, Sil!') }}",
                cancelButtonText: "{{ __('İptal') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                button.closest('.col').remove();
                                if (typeof refreshFsLightbox === 'function') {
                                    refreshFsLightbox();
                                }
                                if ($('#kt_gallery_sortable > div').length === 0) {
                                    location.reload();
                                }
                            }
                        },
                        error: function() {
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
