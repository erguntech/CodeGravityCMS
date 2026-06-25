<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\ClientController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\LogController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\SystemSettingsController;
use App\Http\Controllers\Backend\ProductCategoryController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProjectCategoryController;
use App\Http\Controllers\Backend\ProjectController;
use App\Http\Controllers\Backend\BlogPostCategoryController;
use App\Http\Controllers\Backend\BlogPostController;
use App\Http\Controllers\Backend\ServiceCategoryController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\NewsController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\ReferenceController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\WelcomeMessageController;
use App\Http\Controllers\FrontendController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');
Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    
    // Profile
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Support Group
    Route::get('destek/guncelleme-notlari', [\App\Http\Controllers\Backend\SupportController::class, 'updateNotes'])->name('support.updates');

    // Admin Group
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        // Users & Clients
        Route::resource('users', UserController::class);
        Route::resource('clients', ClientController::class);

        Route::get('clients/{client}/client-settings', [ClientController::class, 'clientSettings'])->name('clients.client-settings');
        Route::post('clients/{client}/client-settings', [ClientController::class, 'updateClientSettings'])->name('clients.client-settings.update');

        // Roles & Permissions
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        // Logs
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');

        // Update Management
        Route::resource('updates', \App\Http\Controllers\Backend\UpdateNoteController::class);

        // Settings
        Route::get('settings/system', [SystemSettingsController::class, 'index'])->name('settings.system')->middleware('can:systemsettings.view');
        Route::post('settings/system/update', [SystemSettingsController::class, 'update'])->name('settings.system.update')->middleware('can:systemsettings.manage');


        
        // API Access
        Route::get('api-access', [App\Http\Controllers\Backend\ApiAccessController::class, 'index'])->name('api-access')->middleware('can:api_access.view');
    });

    // Client Group
    Route::middleware(['role:Client'])->prefix('client')->name('client.')->group(function () {
        // Product Management for Client
        Route::middleware(['client_module:product_management'])->group(function () {
            Route::get('product-categories/reorder', [ProductCategoryController::class, 'reorder'])->name('product-categories.reorder');
            Route::post('product-categories/reorder', [ProductCategoryController::class, 'updateOrder'])->name('product-categories.reorder.update');
            Route::resource('product-categories', ProductCategoryController::class);
            Route::get('products/reorder', [ProductController::class, 'reorder'])->name('products.reorder');
            Route::post('products/reorder', [ProductController::class, 'updateOrder'])->name('products.reorder.update');
            Route::resource('products', ProductController::class);
            Route::get('products/{product}/gallery', [ProductController::class, 'gallery'])->name('products.gallery');
            Route::post('products/{product}/gallery', [ProductController::class, 'storeGallery'])->name('products.gallery.store');
            Route::delete('products/{product}/gallery/{image}', [ProductController::class, 'destroyGallery'])->name('products.gallery.destroy');
            Route::post('products/{product}/gallery/reorder', [ProductController::class, 'reorderGallery'])->name('products.gallery.reorder');
        });

        // Project Management for Client
        Route::middleware(['client_module:project_management'])->group(function () {
            Route::get('project-categories/reorder', [ProjectCategoryController::class, 'reorder'])->name('project-categories.reorder');
            Route::post('project-categories/reorder', [ProjectCategoryController::class, 'updateOrder'])->name('project-categories.reorder.update');
            Route::resource('project-categories', ProjectCategoryController::class);
            Route::get('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
            Route::post('projects/reorder', [ProjectController::class, 'updateOrder'])->name('projects.reorder.update');
            Route::resource('projects', ProjectController::class);
            Route::get('projects/{project}/gallery', [ProjectController::class, 'gallery'])->name('projects.gallery');
            Route::post('projects/{project}/gallery', [ProjectController::class, 'storeGallery'])->name('projects.gallery.store');
            Route::delete('projects/{project}/gallery/{image}', [ProjectController::class, 'destroyGallery'])->name('projects.gallery.destroy');
            Route::post('projects/{project}/gallery/reorder', [ProjectController::class, 'reorderGallery'])->name('projects.gallery.reorder');
        });

        // Blog Management for Client
        Route::middleware(['client_module:blog_management'])->group(function () {
            Route::get('blog-post-categories/reorder', [BlogPostCategoryController::class, 'reorder'])->name('blog-post-categories.reorder');
            Route::post('blog-post-categories/reorder', [BlogPostCategoryController::class, 'updateOrder'])->name('blog-post-categories.reorder.update');
            Route::resource('blog-post-categories', BlogPostCategoryController::class);
            Route::get('blog-posts/reorder', [BlogPostController::class, 'reorder'])->name('blog-posts.reorder');
            Route::post('blog-posts/reorder', [BlogPostController::class, 'updateOrder'])->name('blog-posts.reorder.update');
            Route::resource('blog-posts', BlogPostController::class);
            Route::get('blog-posts/{blog_post}/gallery', [BlogPostController::class, 'gallery'])->name('blog-posts.gallery');
            Route::post('blog-posts/{blog_post}/gallery', [BlogPostController::class, 'storeGallery'])->name('blog-posts.gallery.store');
            Route::delete('blog-posts/{blog_post}/gallery/{image}', [BlogPostController::class, 'destroyGallery'])->name('blog-posts.gallery.destroy');
            Route::post('blog-posts/{blog_post}/gallery/reorder', [BlogPostController::class, 'reorderGallery'])->name('blog-posts.gallery.reorder');
        });

        // Service Management for Client
        Route::middleware(['client_module:service_management'])->group(function () {
            Route::get('service-categories/reorder', [ServiceCategoryController::class, 'reorder'])->name('service-categories.reorder');
            Route::post('service-categories/reorder', [ServiceCategoryController::class, 'updateOrder'])->name('service-categories.reorder.update');
            Route::resource('service-categories', ServiceCategoryController::class);
            Route::get('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
            Route::post('services/reorder', [ServiceController::class, 'updateOrder'])->name('services.reorder.update');
            Route::resource('services', ServiceController::class);
            Route::get('services/{service}/gallery', [ServiceController::class, 'gallery'])->name('services.gallery');
            Route::post('services/{service}/gallery', [ServiceController::class, 'storeGallery'])->name('services.gallery.store');
            Route::delete('services/{service}/gallery/{image}', [ServiceController::class, 'destroyGallery'])->name('services.gallery.destroy');
            Route::post('services/{service}/gallery/reorder', [ServiceController::class, 'reorderGallery'])->name('services.gallery.reorder');
        });

        // Slider Management for Client
        Route::middleware(['client_module:slider_management'])->group(function () {
            Route::get('sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');
            Route::post('sliders/reorder', [SliderController::class, 'updateOrder'])->name('sliders.reorder.update');
            Route::resource('sliders', SliderController::class);
        });

        // News Management for Client
        Route::middleware(['client_module:news_management'])->group(function () {
            Route::get('news/reorder', [NewsController::class, 'reorder'])->name('news.reorder');
            Route::post('news/reorder', [NewsController::class, 'updateOrder'])->name('news.reorder.update');
            Route::resource('news', NewsController::class);
            Route::get('news/{news}/gallery', [NewsController::class, 'gallery'])->name('news.gallery');
            Route::post('news/{news}/gallery', [NewsController::class, 'storeGallery'])->name('news.gallery.store');
            Route::delete('news/{news}/gallery/{image}', [NewsController::class, 'destroyGallery'])->name('news.gallery.destroy');
            Route::post('news/{news}/gallery/reorder', [NewsController::class, 'reorderGallery'])->name('news.gallery.reorder');
        });

        // Media Gallery Management for Client
        Route::middleware(['client_module:media_gallery'])->group(function () {
            Route::get('media/reorder', [MediaController::class, 'reorder'])->name('media.reorder');
            Route::post('media/reorder', [MediaController::class, 'updateOrder'])->name('media.reorder.update');
            Route::resource('media', MediaController::class)->parameters(['media' => 'media']);
            Route::get('media/{media}/gallery', [MediaController::class, 'gallery'])->name('media.gallery');
            Route::post('media/{media}/gallery', [MediaController::class, 'storeGallery'])->name('media.gallery.store');
            Route::delete('media/{media}/gallery/{image}', [MediaController::class, 'destroyGallery'])->name('media.gallery.destroy');
            Route::post('media/{media}/gallery/reorder', [MediaController::class, 'reorderGallery'])->name('media.gallery.reorder');
        });

        // Reference Management for Client
        Route::middleware(['client_module:reference_management'])->group(function () {
            Route::get('references/reorder', [ReferenceController::class, 'reorder'])->name('references.reorder');
            Route::post('references/reorder', [ReferenceController::class, 'updateOrder'])->name('references.reorder.update');
            Route::resource('references', ReferenceController::class)->parameters(['references' => 'reference']);
        });

        // Brand Management for Client
        Route::middleware(['client_module:brand_management'])->group(function () {
            Route::get('brands/reorder', [BrandController::class, 'reorder'])->name('brands.reorder');
            Route::post('brands/reorder', [BrandController::class, 'updateOrder'])->name('brands.reorder.update');
            Route::resource('brands', BrandController::class)->parameters(['brands' => 'brand']);
        });


        // Welcome Message Management for Client
        Route::middleware(['client_module:welcome_message'])->group(function () {
            Route::get('welcome-message', [WelcomeMessageController::class, 'edit'])->name('welcome-message.edit');
            Route::put('welcome-message', [WelcomeMessageController::class, 'update'])->name('welcome-message.update');
        });

        // API Access
        Route::get('api-access', [App\Http\Controllers\Backend\ApiAccessController::class, 'index'])->name('api-access')->middleware('can:api_access.view');

        // Client Site Settings
        Route::get('site-settings', [App\Http\Controllers\Backend\ClientSiteSettingsController::class, 'index'])->name('site-settings.index');
        Route::post('site-settings/update', [App\Http\Controllers\Backend\ClientSiteSettingsController::class, 'update'])->name('site-settings.update');

        // Client Language Settings
        Route::get('languages', [App\Http\Controllers\Backend\ClientLanguageController::class, 'index'])->name('languages.index');
        Route::post('languages/update', [App\Http\Controllers\Backend\ClientLanguageController::class, 'update'])->name('languages.update');
    });

});
