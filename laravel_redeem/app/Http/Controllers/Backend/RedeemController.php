<?php

namespace App\Http\Controllers\Backend;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\CampaignH;
use App\Model\CampaignDHadiah;
use App\Model\CampaignDBagi;
use App\Model\CampaignDEmas;
use App\Model\CustomerOmzet;
use App\Model\RedeemDetail;
use App\Model\RedeemEmas;
use App\Model\UserAvex;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;
use Image;

use App\Model\Customer;

class RedeemController extends Controller
{
    public function index()
    {
        //
		return view ('backend.redeem.index');
    }
	
	public function datatable() {	
		$data = CustomerOmzet::select('customer_omzet.id', 'campaign_h.kode_campaign', 'campaign_h.nama_campaign','campaign_h.jenis','campaign_h.jenis','customer_omzet.periode_awal','customer_omzet.periode_akhir','campaign_h.jenis','campaign_h.brosur','customer_omzet.omzet_netto','customer_omzet.poin')->leftJoin('campaign_h','customer_omzet.kode_campaign','=','campaign_h.kode_campaign')->where('campaign_h.active','=',1)->where('periode_awal','<=',date('Y-m-d'));
        return Datatables::of($data)
            ->editColumn('jenis', function($data) {
                return strtoupper($data->jenis);
            })
            ->editColumn('periode_awal', function($data) {
                return date('d M Y',strtotime($data->periode_awal));
            })
            ->editColumn('periode_akhir', function($data) {
                return date('d M Y',strtotime($data->periode_akhir));
            })
            ->editColumn('brosur', function($data) {
                return "<a href='".url('upload/Brosur/'.$data->brosur)."' target='_blank'>".$data->brosur."</a>";
            })
            ->editColumn('omzet_netto', function($data) {
                return number_format($data->omzet_netto,0,',','.');
            })
            ->editColumn('poin', function($data) {
                return number_format($data->poin,0,',','.');
            })
			->addColumn('status', function ($data) {
                //ambil data header
                $status = 0;
                $data_campaign_header = CampaignH::where('kode_campaign','=', $data->kode_campaign)->get();
                if ($data->count()){
                    //cek ada hadiah emas atau ga
                    $cek_emas_pilihan = CampaignDHadiah::where('emas','=',1)->where('id_campaign','=',$data_campaign_header[0]->id)->count();
                    //cek sudah klaim apa belum
                    $cek_klaim = RedeemDetail::where('kode_customer','=',$data->kode_customer)->where('id_campaign','='.$data_campaign_header[0]->id)->count();
                    //cek convert emas
                    $cek_emas = RedeemEmas::where('kode_customer','=',$data->kode_customer)->where('id_campaign','='.$data_campaign_header[0]->id)->count();

                    if (($cek_klaim > 0) && ($cek_emas_pilihan == 0)){
                        //sudah klaim
                        $status = 1;
                    }
                    if ($cek_emas > 0){
                        //sudah klaim
                        $status = 1;
                    }
                    if (($cek_klaim == 0) && (date('Y-m-d',strtotime($data->periode_akhir)) < date('Y-m-d'))){
                        //expired belum klaim
                        $status = 2;
                    }
                    if (($cek_klaim == 0) && (date('Y-m-d',strtotime($data->periode_akhir)) >= date('Y-m-d'))){
                        //not klaim
                        $status = 3;
                    }
                }
                return $status;
            })			
			->addColumn('action', function ($data) {
                $status = 0;
                $data_campaign_header = CampaignH::where('kode_campaign','=', $data->kode_campaign)->get();

                //cek ada hadiah emas atau ga
                $cek_emas_pilihan = CampaignDHadiah::where('emas','=',1)->where('id_campaign','=',$data_campaign_header[0]->id)->count();
                //cek sudah klaim apa belum
                $cek_klaim = RedeemDetail::where('kode_customer','=',$data->kode_customer)->where('id_campaign','='.$data_campaign_header[0]->id)->count();
                //cek convert emas
                $cek_emas = RedeemEmas::where('kode_customer','=',$data->kode_customer)->where('id_campaign','='.$data_campaign_header[0]->id)->count();

                if (($cek_klaim > 0) && ($cek_emas_pilihan == 0)){
                    //sudah klaim
                    $status = 1;
                }
                if ($cek_emas > 0){
                    //sudah klaim
                    $status = 1;
                }
                if (($cek_klaim == 0) && (date('Y-m-d',strtotime($data->periode_akhir)) < date('Y-m-d'))){
                    //expired belum klaim
                    $status = 2;
                }
                if (($cek_klaim == 0) && (date('Y-m-d',strtotime($data->periode_akhir)) >= date('Y-m-d'))){
                    //not klaim
                    $status = 3;
                }

                $url_view = url('backend/redeem-hadiah/'.$data->id);
                $url_klaim = url('backend/redeem-hadiah/'.$data->id.'/klaim-hadiah');
                $url_konvert = url('backend/redeem-hadiah/'.$data->id.'/konversi-emas');

                $view = "<a class='btn-action btn btn-primary' href='".$url_view."' title='View'><i class='fa fa-eye'></i> View</a>";  
                $klaim = "<a class='btn-action btn btn-success' href='".$url_klaim."' title='Klaim Hadiah'><i class='fa fa-gift'></i> Klaim Hadiah</a>";  
                $konvert = "<a class='btn-action btn btn-success' href='".$url_konvert."' title='Konversi Emas'><i class='fa fa-exchange'></i> Konversi Emas</a>";  

                if ($status == 1){
                    $klaim = "";
                    $konvert = "";
                }
                if ($status == 2 ){
                    $klaim = "";
                    $konvert = "";
                }
                if ($status == 3){
                    if ($cek_klaim > 0){
                        $klaim = "";
                    }
                    if ($cek_emas_pilihan == 0){
                        $konvert = "";
                    }
                    if ($cek_emas > 0){
                        $konvert = "";
                    }
                }

                return $view." ".$klaim;
            })			
            ->rawColumns(['action','brosur'])
            ->make(true);
    }
    
    public function klaim_hadiah($id){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            return view ('backend.redeem.klaim_hadiah');
        }

    }

}
