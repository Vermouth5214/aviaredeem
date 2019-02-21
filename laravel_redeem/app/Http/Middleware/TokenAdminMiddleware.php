<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Session;

class TokenAdminMiddleware
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
			//Jika bukan admin
        	if ($userinfo['priv'] == "RECV") {
                return redirect('/backend/redeem-hadiah');
            }

        }
        return $next($request);
    }
}