<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

Route::get('/languages', [ApiController::class, 'languages']);
Route::get('/company-info', [ApiController::class, 'companyInfo']);
Route::post('/track-visit', [ApiController::class, 'trackVisit']);
Route::get('/welcome-message', [ApiController::class, 'welcomeMessage']);
Route::get('/sliders', [ApiController::class, 'sliders']);

Route::get('/news', [ApiController::class, 'newsList']);
Route::get('/news/{id}', [ApiController::class, 'newsDetail']);
Route::get('/news/{id}/gallery', [ApiController::class, 'newsGallery']);

Route::get('/media', [ApiController::class, 'mediaList']);
Route::get('/media/{id}', [ApiController::class, 'mediaDetail']);
Route::get('/media/{id}/gallery', [ApiController::class, 'mediaGallery']);

Route::get('/references', [ApiController::class, 'referencesList']);
Route::get('/references/{id}', [ApiController::class, 'referenceDetail']);
Route::get('/project-categories', [ApiController::class, 'projectCategories']);
Route::get('/projects', [ApiController::class, 'projectsList']);
Route::get('/projects/{id}', [ApiController::class, 'projectDetail']);
Route::get('/projects/{id}/gallery', [ApiController::class, 'projectGallery']);

Route::get('/blog-post-categories', [ApiController::class, 'blogPostCategories']);
Route::get('/blog-posts', [ApiController::class, 'blogPostsList']);
Route::get('/blog-posts/{id}', [ApiController::class, 'blogPostDetail']);
Route::get('/blog-posts/{id}/gallery', [ApiController::class, 'blogPostGallery']);

Route::get('/service-categories', [ApiController::class, 'serviceCategories']);
Route::get('/services', [ApiController::class, 'servicesList']);
Route::get('/services/{id}', [ApiController::class, 'serviceDetail']);
Route::get('/services/{id}/gallery', [ApiController::class, 'serviceGallery']);

Route::get('/product-categories', [ApiController::class, 'productCategories']);
Route::get('/products', [ApiController::class, 'productsList']);
Route::get('/products/{id}', [ApiController::class, 'productDetail']);
Route::get('/brands', [ApiController::class, 'brandsList']);
Route::get('/brands/{id}', [ApiController::class, 'brandDetail']);

Route::get('/products/{id}/gallery', [ApiController::class, 'productGallery']);
