<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('settings')) {
            $settings = Setting::pluck('value', 'key')->all();
            View::share('settings', $settings);
            
            if (isset($settings['app_name'])) {
                config(['app.name' => $settings['app_name']]);
            }
        }
    }
}
