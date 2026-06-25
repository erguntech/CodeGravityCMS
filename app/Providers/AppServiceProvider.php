<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as BaseValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::resolver(function ($translator, $data, $rules, $messages, $customAttributes) {
            return new class($translator, $data, $rules, $messages, $customAttributes) extends BaseValidator {
                protected function getMessage($attribute, $rule) {
                    $message = parent::getMessage($attribute, $rule);
                    if (is_string($message)) {
                        $trimmed = preg_replace('/^[@\s]+/', '', $message);
                        return '@ ' . $trimmed;
                    }
                    return $message;
                }
            };
        });

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            function ($event) {
                activity('auth')
                    ->causedBy($event->user)
                    ->log('login');
            }
        );

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            function ($event) {
                if ($event->user) {
                    activity('auth')
                        ->causedBy($event->user)
                        ->log('logout');
                }
            }
        );

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // Share shared variables to backend views
        \Illuminate\Support\Facades\View::composer(['layouts.backend', 'pages.backend.*'], function ($view) {
            $appName = \Illuminate\Support\Facades\Cache::rememberForever('system_app_name', function () {
                $setting = \App\Models\Setting::where('key', 'app_name')->first();
                return $setting ? $setting->value : config('app.name');
            });
            $view->with('appName', $appName);

            if (auth()->check()) {
                $routePrefix = '';
                if(auth()->user()->hasRole('Admin')) $routePrefix = 'admin.';
                elseif(auth()->user()->hasRole('Client')) $routePrefix = 'client.';
                
                $view->with('routePrefix', $routePrefix);
            }
        });
    }
}
