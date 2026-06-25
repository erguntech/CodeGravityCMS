<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\LanguageMiddleware::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\ApiLocalization::class,
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'language' => \App\Http\Middleware\LanguageMiddleware::class,
            'client_module' => \App\Http\Middleware\CheckClientModule::class,
        ]);

        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson() || $request->isXmlHttpRequest() || $request->ajax()) {
                return response()->json([
                    'message' => 'Unauthenticated',
                    'redirect' => route('login')
                ], 401);
            }

            if (!auth()->check()) {
                session()->put('url.intended', url()->previous());
                return redirect()->route('login')->with('error', 'Oturum süreniz doldu, lütfen tekrar giriş yapın.');
            }

            return redirect()->back()->withInput()->with('error', 'Sayfa süresi doldu, lütfen tekrar deneyin.');
        });
    })->create();
