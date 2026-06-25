@extends('layouts.backend')

@section('title', __('API Erişimi'))

@push('styles')
<style>
    .font-mono {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
    }
    .endpoint-badge {
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    .endpoint-url {
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.95rem;
        font-weight: 600;
    }
</style>
@endpush

@section('page_title')
<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3 py-5">
    <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{ __('API Erişimi') }}</h1>
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
            $apiToken = auth()->user()->client?->api_token ?? 'geliştirici_api_anahtarı_burada_yer_alacak';
        @endphp

        <!-- Overview Card -->
        <div class="card card-flush mb-6">
            <div class="card-header align-items-center gap-2 gap-md-5 position-relative ribbon ribbon-start min-h-60px">
                <div class="ribbon-label bg-primary fs-6" style="padding: 10px 15px;">
                    <span class="d-flex text-white fw-bolder fs-6">{{ __('API Genel Bilgileri') }}</span>
                </div>
            </div>
            <div class="separator separator-dashed border-gray-200"></div>
            <div class="card-body pt-6 pb-6">
                <p class="fs-6 text-gray-700 mb-4">
                    {{ __('Sistemimizdeki tüm modüllerin verilerine dış uygulamalardan erişebilmeniz için geliştirilmiş REST API uçları aşağıda listelenmiştir. Her istemci yalnızca kendi verilerini (multi-tenant) çekebilir.') }}
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-gray-800 w-150px">{{ __('API Base URL:') }}</span>
                        <span class="font-mono fs-6 text-gray-700">{{ request()->root() }}/api</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-gray-800 w-150px">{{ __('İçerik Tipi:') }}</span>
                        <span class="font-mono fs-7 text-gray-700">application/json</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="fw-bold text-gray-800 w-150px">{{ __('Yetkilendirme:') }}</span>
                        <span class="fs-7 text-gray-700">{{ __('Bearer Token') }}</span>
                    </div>
                    <div class="d-flex align-items-center mt-2">
                        <span class="fw-bold text-gray-800 w-150px">{{ __('API Anahtarınız:') }}</span>
                        <div class="d-flex align-items-center bg-light-warning border border-warning border-dashed rounded p-2">
                            <code class="text-warning font-mono fs-6 me-2">{{ $apiToken }}</code>
                            <button class="btn btn-icon btn-sm btn-active-color-warning" onclick="navigator.clipboard.writeText('{{ $apiToken }}'); toastr.success('{{ __('Kopyalandı!') }}');" title="{{ __('Kopyala') }}">
                                <i class="ki-duotone ki-copy fs-3"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="separator separator-dashed my-6"></div>

                <h4 class="fw-bold text-gray-800 mb-3">{{ __('Yetkilendirme Yöntemi') }}</h4>
                <p class="fs-7 text-gray-600 mb-3">
                    {{ __('API isteklerinizde kimlik doğrulaması yapmak için HTTP Authorization başlığını kullanmanız gerekmektedir:') }}
                </p>
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="bg-light rounded p-4">
                            <h5 class="fw-bold fs-7 text-gray-800 mb-2">{{ __('HTTP Bearer Token') }}</h5>
                            <p class="fs-8 text-gray-600 mb-2">{{ __('Her istekte Authorization başlığını aşağıdaki formatta gönderin:') }}</p>
                            <span class="font-mono fs-7 text-gray-800">Authorization: Bearer {{ $apiToken }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endpoints Section -->
        <div class="row g-6">
            <!-- Navigation Sidebar -->
            <div class="col-lg-3">
                <div class="card card-flush sticky-top" style="top: 80px; max-height: 80vh; overflow-y: auto; z-index: 100;">
                    <div class="card-header pt-5 min-h-auto">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-6">{{ __('API Menüsü') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-3">
                        <div class="menu menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary menu-state-title-primary fw-semibold fs-7" id="kt_api_doc_menu">
                            
                            <div class="menu-item mb-1">
                                <a href="#endpoint-languages" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Sistem Dilleri & Çoklu Dil') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-company-info" class="menu-link py-2 active">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Firma Bilgileri') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-track-visit" class="menu-link py-2">
                                      <span class="badge badge-light-primary me-2">POST</span> {{ __('Ziyaretçi Takibi') }}
                                  </a>
                              </div>
                              <div class="menu-item mb-1">
                                  <a href="#endpoint-welcome-message" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Açılış Mesajı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-sliders" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Slider Resimleri') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-news-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Haberleri Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-news-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Haber Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-news-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Haber Galerisi') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-media-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Medya Galerilerini Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-media-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Medya Galerisi Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-media-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Medya Galerisi Resimleri') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-references-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Referansları Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-reference-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Referans Detayı') }}
                                </a>
                            </div>
                            
                            <div class="menu-item mb-1">
                                <a href="#endpoint-project-categories" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Proje Kategorilerini Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-projects-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Projeleri Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-project-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Proje Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-project-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Proje Galerisi') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-product-categories" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Ürün Kategorilerini Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-products-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Ürünleri Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-product-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Ürün Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-brands-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Markaları Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-brand-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Marka Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-product-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Ürün Galerisi') }}
                                </a>
                            </div>
                        
                            <div class="menu-item mb-1">
                                <a href="#endpoint-blog-categories" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Blog Kategorilerini Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-blog-posts" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Blog Yazılarını Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-blog-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Blog Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-blog-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Blog Galerisi') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-service-categories" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Hizmet Kategorilerini Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-services-list" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Hizmetleri Listele') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-service-detail" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Hizmet Detayı') }}
                                </a>
                            </div>
                            <div class="menu-item mb-1">
                                <a href="#endpoint-service-gallery" class="menu-link py-2">
                                    <span class="badge badge-light-success me-2">GET</span> {{ __('Hizmet Galerisi') }}
                                </a>
                            </div>
                            

</div>
                    </div>
                </div>
            </div>

            <!-- Docs Content -->
            <div class="col-lg-9">
                
                
                <!-- System Languages & Localization -->
                <div class="card card-flush mb-6" id="endpoint-languages">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Sistem Dilleri ve Çoklu Dil Kullanımı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Müşteriye ait aktif dilleri listeler ve API geneli çoklu dil kullanımını açıklar.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/languages</span>
                        </div>
                        
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                            <i class="ki-duotone ki-information fs-2tx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">{{ __('Çoklu Dil Verilerini Çekmek (Tüm Endpointler İçin Geçerli)') }}</h4>
                                    <div class="fs-6 text-gray-700">API'deki diğer tüm içerikleri (Ürünler, Haberler vb.) istediğiniz dilde çekmek için iki yöntemden birini kullanabilirsiniz:
                                    <ul class="mt-2 mb-0">
                                        <li><strong>HTTP Header İle:</strong> İsteğinize <code>Accept-Language: en</code> header'ını ekleyin.</li>
                                        <li><strong>URL Parametresi İle:</strong> URL'nin sonuna <code>?lang=en</code> ekleyin (Örn: <code>/products?lang=en</code>).</li>
                                    </ul>
                                    Eğer bu değerleri göndermezseniz, sistem verileri varsayılan dilde (genellikle TR) dönecektir.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "name": "Türkçe",
      "code": "tr",
      "is_default": 1,
      "icon": "null"
    },
    {
      "name": "English",
      "code": "en",
      "is_default": 0,
      "icon": "null"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 0. Company Info -->
                <div class="card card-flush mb-6" id="endpoint-company-info">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Firma Bilgileri') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Müşteriye ait iletişim ve firma genel bilgilerini getirir.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/company-info</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
    "status": "success",
    "data": {
      "company_name": "CodeGravity Teknoloji",
      "address": "Kadıköy, İstanbul",
      "additional_address": "Kat 3, Daire 12",
      "phone": "+90 212 555 5555",
      "fax": "+90 212 555 5556",
      "additional_contact": "info@codegravity.com",
      "instagram": "https://instagram.com/codegravity",
      "facebook": "https://facebook.com/codegravity",
      "whatsapp": "+90 555 555 5555",
      "coordinates": "40.990422, 29.020739"
    }
  }</pre>
                            </div>
                        </div>
                    </div>
                </div>

                


                <!-- Track Visit -->
                <div class="card card-flush mb-6" id="endpoint-track-visit">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Ziyaretçi Takibi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Siteye giren ziyaretçileri saymak için kullanılır. Aynı gün aynı IP bir kere sayılır.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-primary endpoint-badge me-3">POST</span>
                            <span class="endpoint-url text-gray-800">/track-visit</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('İstek Gövdesi (JSON)') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "url": "https://example.com/hakkimizda"
}</pre>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. Welcome Message -->
                <div class="card card-flush mb-6" id="endpoint-welcome-message">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Açılış Mesajı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Müşteriye ait aktif açılış mesajı (pop-up) verisini getirir.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/welcome-message</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "title": "Hoş Geldiniz",
    "description": "Yeni sezon ürünlerimizde %20 indirim başladı!",
    "image_url": "{{ request()->root() }}/storage/welcome_messages/banner.jpg",
    "status": "active",
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Sliders -->
                <div class="card card-flush mb-6" id="endpoint-sliders">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Slider Resimleri') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Sistemdeki tüm aktif anasayfa slider resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/sliders</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Yeni Koleksiyon",
      "slug": "yeni-koleksiyon",
      "description": "Özel tasarım kıyafetlerimizi keşfedin.",
      "image_url": "{{ request()->root() }}/storage/sliders/slide1.jpg",
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. News List -->
                <div class="card card-flush mb-6" id="endpoint-news-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Haberleri Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Aktif durumdaki tüm kurumsal haber ve duyuruları listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/news</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 5,
      "title": "Yeni Şubemiz Açıldı",
      "slug": "yeni-subemiz-acildi",
      "description": "Kadıköy şubemizle hizmetinizdeyiz.",
      "image_url": "{{ request()->root() }}/storage/news/branch.jpg",
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. News Detail -->
                <div class="card card-flush mb-6" id="endpoint-news-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Haber Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen ID\'li haberin detayını ve alt galeri resimlerini getirir.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/news/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 5,
    "title": "Yeni Şubemiz Açıldı",
    "slug": "yeni-subemiz-acildi",
    "description": "Kadıköy şubemizle hizmetinizdeyiz.",
    "image_url": "{{ request()->root() }}/storage/news/branch.jpg",
    "sort_order": 1,
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4b. News Gallery -->
                <div class="card card-flush mb-6" id="endpoint-news-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Haber Galerisi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen habere ait galeri resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/news/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 10,
      "image_url": "{{ request()->root() }}/storage/news/gallery/pic1.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Media List -->
                <div class="card card-flush mb-6" id="endpoint-media-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Medya Galerilerini Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Fotoğraf albümleri ve medya galerilerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/media</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 2,
      "title": "2026 Fuar Etkinliği",
      "slug": "2026-fuar-etkinligi",
      "description": "Katıldığımız fuardan enstantaneler.",
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Media Detail -->
                <div class="card card-flush mb-6" id="endpoint-media-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Medya Galerisi Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Seçilen medya galerisinin altındaki tüm fotoğrafları listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/media/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 2,
    "title": "2026 Fuar Etkinliği",
    "slug": "2026-fuar-etkinligi",
    "description": "Katıldığımız fuardan enstantaneler.",
    "sort_order": 1,
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6b. Media Gallery -->
                <div class="card card-flush mb-6" id="endpoint-media-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Medya Galerisi Resimleri') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen medya galerisine ait resimleri listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/media/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 14,
      "image_url": "{{ request()->root() }}/storage/media/gallery/photo1.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. References List -->
                <div class="card card-flush mb-6" id="endpoint-references-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Referansları Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Çalıştığımız aktif referans ve iş ortaklarımızı listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/references</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 4,
      "title": "CodeGravity Teknoloji",
      "slug": "codegravity-teknoloji",
      "description": "Yazılım çözüm ortağımız.",
      "image_url": "{{ request()->root() }}/storage/references/logo.png",
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 8. Reference Detail -->
                <div class="card card-flush mb-6" id="endpoint-reference-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Referans Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen ID\'li referansın detayını ve galerisini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/references/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 4,
    "title": "CodeGravity Teknoloji",
    "slug": "codegravity-teknoloji",
    "description": "Yazılım çözüm ortağımız.",
    "image_url": "{{ request()->root() }}/storage/references/logo.png",
    "sort_order": 1,
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 9. Project Categories -->
                <div class="card card-flush mb-6" id="endpoint-project-categories">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Proje Kategorilerini Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Projelerin bağlı olduğu aktif kategorileri listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/project-categories</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 2,
      "title": "Mimari Projeler",
      "slug": "mimari-projeler",
      "description": "Bina ve ofis tasarımlarımız.",
      "image_url": "{{ request()->root() }}/storage/categories/architecture.jpg",
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 10. Projects List -->
                <div class="card card-flush mb-6" id="endpoint-projects-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Projeleri Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Sistemdeki tüm aktif projeleri listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/projects</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                            <th>Açıklama</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">category_id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-warning">{{ __('Opsiyonel') }}</span></td>
                                            <td>{{ __('Belirtilen kategorideki projeleri filtreler.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 11,
      "title": "E-Ticaret Dönüşümü",
      "slug": "e-ticaret-donusumu",
      "description": "Büyük ölçekli perakende entegrasyonu.",
      "image_url": "{{ request()->root() }}/storage/projects/ecommerce.jpg",
      "category": {
        "id": 1,
        "title": "Yazılım",
        "slug": "yazilim"
      },
      "sort_order": 1,
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 11. Project Detail -->
                <div class="card card-flush mb-6" id="endpoint-project-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Proje Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen ID\'li projenin detayını ve galeri fotoğraflarını listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/projects/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 11,
    "title": "E-Ticaret Dönüşümü",
    "slug": "e-ticaret-donusumu",
    "description": "Büyük ölçekli perakende entegrasyonu.",
    "image_url": "{{ request()->root() }}/storage/projects/ecommerce.jpg",
    "category": {
      "id": 1,
      "title": "Yazılım",
      "slug": "yazilim"
    },
    "sort_order": 1,
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 11b. Project Gallery -->
                <div class="card card-flush mb-6" id="endpoint-project-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Proje Galerisi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen projeye ait galeri resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/projects/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 35,
      "image_url": "{{ request()->root() }}/storage/projects/gallery/step1.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 12. Product Categories -->
                <div class="card card-flush mb-6" id="endpoint-product-categories">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Ürün Kategorilerini Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Ürünlerin bağlı olduğu aktif ürün kategorilerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/product-categories</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 3,
      "title": "Aksesuar",
      "slug": "aksesuar",
      "description": "Akıllı aksesuarlar ve aparatlar.",
      "image_url": "{{ request()->root() }}/storage/categories/accessories.jpg",
      "sort_order": 1,
      "created_at": "2026-06-05T16:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 13. Products List -->
                <div class="card card-flush mb-6" id="endpoint-products-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Ürünleri Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Sistemde tanımlı olan tüm aktif ürünleri listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/products</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                            <th>Açıklama</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">category_id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-warning">{{ __('Opsiyonel') }}</span></td>
                                            <td>{{ __('Kategoriye göre ürünleri filtreler.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 12,
      "title": "Akıllı Saat Pro",
      "slug": "akilli-saat-pro",
      "description": "50m suya dayanıklı akıllı saat.",
      "image_url": "{{ request()->root() }}/storage/products/watch.jpg",
      "category": {
        "id": 3,
        "title": "Aksesuar",
        "slug": "aksesuar"
      },
      "sort_order": 1,
      "created_at": "2026-06-05T17:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 14. Product Detail -->
                <div class="card card-flush mb-6" id="endpoint-product-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Ürün Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen ID\'li ürünün detaylarını ve galerisini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/products/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 12,
    "title": "Akıllı Saat Pro",
    "slug": "akilli-saat-pro",
    "description": "50m suya dayanıklı akıllı saat.",
    "image_url": "{{ request()->root() }}/storage/products/watch.jpg",
    "category": {
      "id": 3,
      "title": "Aksesuar",
      "slug": "aksesuar"
    },
    "sort_order": 1,
    "created_at": "2026-06-05T17:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 11. Brands List -->
                <div class="card card-flush mb-6" id="endpoint-brands-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Markaları Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Müşteriye ait tüm aktif markaları sıralı şekilde döndürür.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/brands</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <span class="text-gray-500 fs-7">{{ __('Bu endpoint iÃ§in URL parametresi bulunmamaktadÄ±r.') }}</span>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Ã–rnek YanÄ±t') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Örnek Marka",
      "slug": "ornek-marka",
      "description": "Marka açıklaması",
      "image_url": "{{ request()->root() }}/storage/brands/logo.png",
      "website_url": "https://ornek.com",
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 11a. Brand Detail -->
                <div class="card card-flush mb-6" id="endpoint-brand-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Marka Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen markanın detay bilgilerini döndürür.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/brands/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Ã–rnek YanÄ±t') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Örnek Marka",
    "slug": "ornek-marka",
    "description": "Marka açıklaması",
    "image_url": "{{ request()->root() }}/storage/brands/logo.png",
    "website_url": "https://ornek.com",
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- 14b. Product Gallery -->
                <div class="card card-flush mb-6" id="endpoint-product-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Ürün Galerisi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen ürüne ait galeri resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/products/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 45,
      "image_url": "{{ request()->root() }}/storage/products/gallery/watch_side.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

            
                <!-- 15. Blog Categories -->
                <div class="card card-flush mb-6" id="endpoint-blog-categories">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Blog Kategorilerini Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Aktif blog kategorilerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/blog-post-categories</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Teknoloji",
      "slug": "teknoloji",
      "description": "Teknoloji haberleri",
      "image_url": "{{ request()->root() }}/storage/blog_categories/tech.jpg"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 16. Blog Posts -->
                <div class="card card-flush mb-6" id="endpoint-blog-posts">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Blog Yazılarını Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Aktif blog yazılarını listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/blog-posts</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Yapay Zeka ve Gelecek",
      "slug": "yapay-zeka-ve-gelecek",
      "description": "Yapay zeka hayatımızı nasıl değiştirecek?",
      "image_url": "{{ request()->root() }}/storage/blog_posts/ai.jpg",
      "category": { "id": 1, "title": "Teknoloji" },
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 17. Blog Detail -->
                <div class="card card-flush mb-6" id="endpoint-blog-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Blog Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen blog yazısının detayını ve içeriğini getirir.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/blog-posts/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Yapay Zeka ve Gelecek",
    "slug": "yapay-zeka-ve-gelecek",
    "description": "Özet açıklama...",
    "content": "<p>Tüm HTML içerik burada yer alır.</p>",
    "image_url": "{{ request()->root() }}/storage/blog_posts/ai.jpg",
    "category": { "id": 1, "title": "Teknoloji" },
    "gallery": [],
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 17b. Blog Gallery -->
                <div class="card card-flush mb-6" id="endpoint-blog-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Blog Galerisi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen blog yazısına ait galeri resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/blog-posts/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "image_url": "{{ request()->root() }}/storage/blog_posts/gallery/pic1.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 18. Service Categories -->
                <div class="card card-flush mb-6" id="endpoint-service-categories">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Hizmet Kategorilerini Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Aktif hizmet kategorilerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/service-categories</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Danışmanlık",
      "slug": "danismanlik",
      "description": "Kurumsal Danışmanlık",
      "image_url": "{{ request()->root() }}/storage/service_categories/consult.jpg"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 19. Services -->
                <div class="card card-flush mb-6" id="endpoint-services-list">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Hizmetleri Listele') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Aktif hizmetleri listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/services</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Sorgu Parametreleri') }}</h4>
                                <p class="text-muted fs-7">{{ __('Bu uç nokta için ek sorgu parametresi bulunmamaktadır.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "Dijital Pazarlama",
      "slug": "dijital-pazarlama",
      "description": "SEO ve SEM hizmetleri...",
      "image_url": "{{ request()->root() }}/storage/services/marketing.jpg",
      "category": { "id": 1, "title": "Danışmanlık" },
      "created_at": "2026-06-05T18:00:00+03:00"
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 20. Service Detail -->
                <div class="card card-flush mb-6" id="endpoint-service-detail">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Hizmet Detayı') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen hizmetin detayını ve içeriğini getirir.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/services/{id}</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": {
    "id": 1,
    "title": "Dijital Pazarlama",
    "slug": "dijital-pazarlama",
    "description": "Özet açıklama...",
    "content": "<p>Tüm HTML içerik burada yer alır.</p>",
    "image_url": "{{ request()->root() }}/storage/services/marketing.jpg",
    "category": { "id": 1, "title": "Danışmanlık" },
    "gallery": [],
    "created_at": "2026-06-05T18:00:00+03:00"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 20b. Service Gallery -->
                <div class="card card-flush mb-6" id="endpoint-service-gallery">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900 fs-5">{{ __('Hizmet Galerisi') }}</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Belirtilen hizmete ait galeri resimlerini listeler.') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center mb-6 bg-light rounded p-4">
                            <span class="badge badge-success endpoint-badge me-3">GET</span>
                            <span class="endpoint-url text-gray-800">/services/{id}/gallery</span>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-6 mb-md-0">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('URL Parametreleri') }}</h4>
                                <table class="table align-middle table-row-dashed fs-7 gy-3">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-8 text-uppercase gs-0">
                                            <th>Parametre</th>
                                            <th>Tip</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600">
                                        <tr>
                                            <td class="font-mono text-primary">id</td>
                                            <td class="font-mono">Integer</td>
                                            <td><span class="badge badge-light-danger">{{ __('Zorunlu') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="fw-bold fs-6 text-gray-800 mb-3">{{ __('Örnek Yanıt') }}</h4>
                                <pre class="bg-gray-900 text-gray-100 rounded p-4 font-mono fs-8 overflow-auto">{
  "status": "success",
  "data": [
    {
      "id": 1,
      "image_url": "{{ request()->root() }}/storage/services/gallery/pic1.jpg",
      "sort_order": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

</div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kt_api_doc_menu a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);

            $('#kt_api_doc_menu a').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
@endpush
