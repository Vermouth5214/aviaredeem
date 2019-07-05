<?php


namespace App\Http\Controllers\Backend;

use Session;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use App\Model\CampaignH;
use App\Model\UserAvex;
use DB;

class DelRedeemController extends Controller {
	public function index(Request $request) {
        $campaign = CampaignH::where('active', '=', 1)->orderBy('id','DESC')->pluck('kode_campaign','id')->toArray();
        $campaign = array_map('strtoupper', $campaign);

        $list_agen = UserAvex::select(DB::raw("CONCAT(reldag,' - ',cabang) as nama"),'reldag')->where('utrace', 1)->where('posisi','AGEN')->groupBy('reldag')->orderBy('reldag','ASC')->pluck('nama', 'reldag');

        view()->share('campaign', $campaign);
        view()->share('list_agen', $list_agen);
        return view ('backend.delredeem.index');
	}
	
    public function delete(Request $request)
    {
        $kode_campaign = $request->kode_campaign;
        $agen = $request->agen;
        $header_1 = DB::delete("delete from redeem_detail where kode_customer = '".$agen."' and id_campaign='".$kode_campaign."'");
        $header_2 = DB::delete("delete from redeem_emas where kode_customer = '".$agen."' and id_campaign='".$kode_campaign."'");
        return Redirect::to('/backend/delete-redeem/')->with('success', $header_1 + $header_2." rows deleted")->with('mode', 'success');
        
        
    }
	
}