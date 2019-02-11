<?php

namespace App\Http\Controllers\Backend;

use Session;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Model\Customer;
use Illuminate\Support\Facades\Redirect;
use View;
 
class CheckController extends Controller {
	public function index(Request $request) {
        phpinfo();
        exit();
        $data = Customer::all();
        dd($data);
	}
	
}