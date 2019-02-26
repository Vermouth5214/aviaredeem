<?php


namespace App\Http\Controllers\Backend;

use Session;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Model\CampaignH;
 
class DashboardController extends Controller {
	public function dashboard(Request $request) {
		// $data_campaign = CampaignH::
		return view ('backend.dashboard');
	}
}