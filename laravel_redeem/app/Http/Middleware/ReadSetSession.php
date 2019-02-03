<?php

namespace App\Http\Middleware;

use Closure;
use Session;

/**
 * Class PreferredDomain
 * @package App\Http\Middleware
 */
class ReadSetSession
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
        $sessid=session_id();
        if (isset($_SESSION["uname"][$sessid])){
            if ($request->session()->has('userinfo')){
                if ($request->session()->get('userinfo')['uname'] != $_SESSION["uname"][$sessid]){
                    $request->session()->flush();
                }
            }
            $userinfo['uname'] = $_SESSION["uname"][$sessid];
            $userinfo['priv'] = $_SESSION["priv"][$sessid];
            $userinfo['sub_priv'] = $_SESSION["sub_priv"][$sessid];
            $userinfo['posisi'] = $_SESSION["posisi"][$sessid];
            $userinfo['cabang'] = $_SESSION["cabang"][$sessid];
            $userinfo['reldag'] = $_SESSION["reldag"][$sessid];
            $userinfo['utrace'] = $_SESSION["utrace"][$sessid];
            Session::put ('userinfo', $userinfo);
        } else {
            return redirect('http://localhost/AVIAN/customercare/public/login');
        }

        return $next($request);
    }
}