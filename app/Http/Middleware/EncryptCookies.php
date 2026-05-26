<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EncryptCookies extends Middleware
{
    //  protected $except = [
    //     '_fbp',
    //     '_fbc',
    //     '_sfsid',
    //     '_sfud',
    //     '_sfdid',

    // ];
    protected $except = [
    '_fbp',
    '_fbc',
    '_ttp',
    '_sfdid',
    '_sfud',
    'master_dl',
];

public function handle($request, Closure $next)
    {
        Log::info('EncryptCookies middleware called', [
            'path' => $request->path(),
            'raw_cookie' => $request->header('cookie'),
        ]);
    
        return parent::handle($request, $next);
    }
}
