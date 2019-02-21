<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Session;

class TokenSuperMiddleware
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
            if (env('APP_STATUS') == "local"):
                return redirect('http://localhost/AVIAN/customercare/public/login');
            endif;
            if (env('APP_STATUS') == "prod"):
                return redirect('https://www.avianbrands.com/customercare/login');
            endif;
        } else{
            $userinfo = Session::get('userinfo');

			//Jika bukan admin super
        	if (($userinfo['priv'] != "VSUPER") && ($userinfo['priv'] != "VSUPERT")){
                return redirect('/');
            }

        }
        return $next($request);
    }
}