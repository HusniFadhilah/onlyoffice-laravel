<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogOnlyOfficeRequests
{
    public function handle($request, Closure $next)
    {
        if (str_starts_with($request->path(), 'api/onlyoffice/')) {
            Log::info('OO REQ', [
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'ua' => $request->userAgent(),
                'range' => $request->header('Range'),
                'auth' => $request->header('Authorization'),
                'host' => $request->header('Host'),
            ]);
        }

        return $next($request);
    }
}
