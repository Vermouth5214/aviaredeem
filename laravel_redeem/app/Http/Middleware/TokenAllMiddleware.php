<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Session;

class TokenAllMiddleware
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
        if(!Session::get('userinfo')) {
            return redirect('http://localhost/AVIAN/customercare/public/login');
        } else {
            $userinfo = Session::get('userinfo');
            //bukan user redeem
            if ((($userinfo['priv'] == 'VSUPER') || ($userinfo['priv'] == 'VSUPERT') || ($userinfo['priv'] == 'VREDEEM')) || ($userinfo['priv'] == 'RECV' && $userinfo['posisi'] == 'AGEN' && $userinfo['utrace'] == 1)){

            } else {
                return redirect('http://localhost/AVIAN/customercare/public/portal');
            }
        }
        return $next($request);
    }
}