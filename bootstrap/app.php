<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
                $middleware->encryptCookies(except: [
        '_fbp',
        '_fbc',
        '_ttp',
        '_sfdid',
        '_sfud',
        '_sfsid',
        'master_dl',
        'custom_fbc',
        'debugmode',
    ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetConversionsApiUserData::class,
        ]);

        //  $middleware->append(\App\Http\Middleware\TrackVisits::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
