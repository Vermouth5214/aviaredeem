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
            return redirect('http://localhost/AVIAN/customercare/public/login');
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