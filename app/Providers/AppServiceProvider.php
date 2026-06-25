<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator as BaseValidator;
use Throwable;

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
                protected function getMessage($attribute, $rule)
                {
                    $message = parent::getMessage($attribute, $rule);

                    if (is_string($message)) {
                        $trimmed = preg_replace('/^[@\s]+/', '', $message);

                        return '@ ' . $trimmed;
                    }

                    return $message;
                }
            };
        });

        Event::listen(Login::class, function ($event) {
            activity('auth')
                ->causedBy($event->user)
                ->log('login');
        });

        Event::listen(Logout::class, function ($event) {
            if ($event->user) {
                activity('auth')
                    ->causedBy($event->user)
                    ->log('logout');
            }
        });

        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        View::composer(['layouts.backend', 'pages.backend.*'], function ($view) {
            $appName = config('app.name');

            try {
                if (! app()->runningInConsole() && Schema::hasTable('settings')) {
                    $appName = Cache::rememberForever('system_app_name', function () {
                        $setting = Setting::where('key', 'app_name')->first();

                        return $setting ? $setting->value : config('app.name');
                    });
                }
            } catch (Throwable $exception) {
                report($exception);

                $appName = config('app.name');
            }

            $view->with('appName', $appName);

            $routePrefix = '';

            try {
                if (auth()->check()) {
                    if (auth()->user()->hasRole('Admin')) {
                        $routePrefix = 'admin.';
                    } elseif (auth()->user()->hasRole('Client')) {
                        $routePrefix = 'client.';
                    }
                }
            } catch (Throwable $exception) {
                report($exception);
            }

            $view->with('routePrefix', $routePrefix);
        });
    }
}