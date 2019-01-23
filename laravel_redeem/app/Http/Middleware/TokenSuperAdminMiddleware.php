<?php

namespace App\Http\Middleware;

use Cache;
use Closure;
use Session;
use App\Model\User;

class TokenSuperAdminMiddleware
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
			//SUPER ADMIN
        	if($userinfo['priv'] != 'VSUPER'){
                return redirect('/');
            }
        }
        return $next($request);
    }
}