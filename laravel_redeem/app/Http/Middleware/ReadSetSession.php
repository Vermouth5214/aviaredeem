<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Support\Facades\Route;

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
        $currentPath = Route::getFacadeRoot()->current()->uri();
        if (($currentPath != "backup-database") && ($currentPath != "auto-redeem") && ($currentPath != "email-reminder") && ($currentPath != "auto-redeem-satu") && ($currentPath != "api/notifikasi/waktu-klaim") && ($currentPath != "api/notifikasi/belum-klaim") && ($currentPath != "api/notifikasi/belum-konversi") && ($currentPath != "api/data/get-customer/{id}")) {
            $sessid=session_id();
            if (isset($_SESSION["uname"][$sessid])){
                if ($request->session()->has('userinfo')){
                    if ($request->session()->get('userinfo')['uname'] != $_SESSION["uname"][$sessid]){
                        foreach (Session::all() as $key=>$item){
                            if ($key!='_token') Session::forget($key);
                        }
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
                if (env('APP_STATUS') == "local"):
                    return redirect('http://localhost/AVIAN/customercare/public/login');
                endif;
                if (env('APP_STATUS') == "prod"):
                    return redirect('https://www.avianbrands.com/customercare/login');
                endif;
            }
        }
        return $next($request);
    }
}