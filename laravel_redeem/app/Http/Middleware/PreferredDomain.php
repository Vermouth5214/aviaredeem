<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

/**
 * Class PreferredDomain
 * @package App\Http\Middleware
 */
class PreferredDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (env('APP_WWW')){
            if (!Str::startsWith($request->header('host'), 'www.')) {
                $host = 'www.'.$request->header('host');
                $request->headers->set('host', $host);

                return Redirect::to($request->fullUrl(), 301);
            }
        }
        return $next($request);
    }
}