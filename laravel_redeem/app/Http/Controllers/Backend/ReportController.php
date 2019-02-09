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
use App\Model\RedeemDetail;
use App\Model\RedeemEmas;
use App\Model\UserAvex;
use App\Model\CustomerOmzet;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;

class ReportController extends Controller
{
    public function general_report()
    {
        //
        $kode_campaign = "";
        $status = 999;
    	$startDate = "01"."-".date('m-Y');
        $endDate = date('d-m-Y');
        $mode = "limited";
        $campaign = CampaignH::where('active', '=', 1)->orderBy('id','DESC')->pluck('kode_campaign','kode_campaign')->prepend('All','')->toArray();
        $campaign = array_map('strtoupper', $campaign);

        if (isset($_GET["startDate"]) || isset($_GET["endDate"]) || isset($_GET["status"]) || isset($_GET["mode"])){
			if ((isset($_GET['startDate'])) && ($_GET['startDate'] != "")){
				$startDate = $_GET["startDate"];
			}
			if ((isset($_GET['endDate'])) && ($_GET['endDate'] != "")){
				$endDate = $_GET["endDate"];
            }
			if ((isset($_GET['status'])) && ($_GET['status'] != "")){
				$status = $_GET["status"];
            }
			if (isset($_GET["mode"])){
				$mode = $_GET['mode'];
            }
			if (isset($_GET['kode_campaign'])){
				$kode_campaign = $_GET['kode_campaign'];
            }
        }

		view()->share('startDate',$startDate);
		view()->share('endDate',$endDate);
        view()->share('status',$status);
        view()->share('mode',$mode);
        view()->share('kode_campaign',$kode_campaign);
        view()->share('campaign',$campaign);

		return view ('backend.report.index');
    }

	public function datatable() {
        $data = CustomerOmzet::select('customer_omzet.id', 'campaign_h.kode_campaign', 'campaign_h.nama_campaign','campaign_h.jenis','customer_omzet.periode_awal','customer_omzet.periode_akhir','campaign_h.brosur','customer_omzet.omzet_netto','customer_omzet.poin', DB::raw('count(campaign_d_hadiah.id) as jum_emas'),DB::raw('count(redeem_detail.id) as jum_redeem_detail'),DB::raw('count(redeem_emas.id) as jum_redeem_emas'))
                ->leftJoin('campaign_h','customer_omzet.kode_campaign','=','campaign_h.kode_campaign')
                ->leftJoin('campaign_d_hadiah', function($join){
                    $join->on('campaign_d_hadiah.id_campaign', '=', 'campaign_h.id');
                    $join->on('campaign_d_hadiah.emas','=', DB::raw('1'));
                })
                ->leftJoin('redeem_detail', function($join){
                    $join->on('redeem_detail.kode_customer', '=', 'customer_omzet.kode_customer');
                    $join->on('redeem_detail.id_campaign','=','campaign_h.id');
                })
                ->leftJoin('redeem_emas', function($join){
                    $join->on('redeem_emas.kode_customer', '=', 'customer_omzet.kode_customer');
                    $join->on('redeem_emas.id_campaign','=','campaign_h.id');
                })
                ->where('campaign_h.active','=',1)
                ->groupBy('customer_omzet.kode_campaign');

        $kode_campaign = "";                
        $status = 999;
    	$startDate = "01"."-".date('m-Y');
        $endDate = date('d-m-Y');
        $mode = "limited";

        if (isset($_GET["startDate"]) || isset($_GET["endDate"]) || isset($_GET["status"]) || isset($_GET["mode"])){
			if ((isset($_GET['startDate'])) && ($_GET['startDate'] != "")){
				$startDate = $_GET["startDate"];
			}
			if ((isset($_GET['endDate'])) && ($_GET['endDate'] != "")){
				$endDate = $_GET["endDate"];
            }
			if ((isset($_GET['status'])) && ($_GET['status'] != "")){
				$status = $_GET["status"];
            }
			if (isset($_GET["mode"])){
				$mode = $_GET['mode'];
            }
			if (isset($_GET['kode_campaign'])){
				$kode_campaign = $_GET['kode_campaign'];
            }
        }

        $data = $data->whereRaw("customer_omzet.kode_campaign like'%".$kode_campaign."%'");

        //cek mode
        if ($mode != "all"){
            $data = $data->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
            $data = $data->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
        }

        if ($status == "1"){
            $data = $data->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
        }
        if ($status == '2'){
            $data = $data->havingRaw("jum_redeem_detail = 0 and customer_omzet.periode_akhir <'". date('Y-m-d') ."'" );
        }
        if ($status == '3'){
            $data = $data->havingRaw("jum_redeem_detail = 0 and customer_omzet.periode_akhir >='". date('Y-m-d') ."'" );
        }
        if ($status == '4'){
            $data = $data->havingRaw('jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0');
        }
        if ($status == '5'){
            $data = $data->havingRaw("customer_omzet.periode_awal >'".date('Y-m-d')."'");
        }
       
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
			->addColumn('action', function ($data) {
                $url_view = url('backend/general-report/view/'.$data->id);

                $view = "<a class='btn-action btn btn-primary' href='".$url_view."' title='View'><i class='fa fa-eye'></i> View</a>";  

                return $view;
            })            
			->addColumn('status', function ($data) {
                $status = 0;
                if (($data->jum_redeem_detail > 0 && $data->jum_emas == 0) || ($data->jum_redeem_emas > 0)){
                    $status = 1;
                }
                if ($data->jum_redeem_detail == 0 && (date('Y-m-d',strtotime($data->periode_akhir)) < date('Y-m-d'))){
                    $status = 2;
                }
                if ($data->jum_redeem_detail == 0 && (date('Y-m-d',strtotime($data->periode_akhir)) >= date('Y-m-d'))){
                    $status = 3;
                }
                if (($data->jum_emas > 0) && ($data->jum_redeem_detail > 0) && ($data->jum_redeem_emas == 0)){
                    $status = 4;
                }
                if (date('Y-m-d') < date('Y-m-d',strtotime($data->periode_awal))){
                    $status = 5;
                }

                return $status;
            })			
            ->rawColumns(['brosur','action'])
            ->make(true);
    }

    public function view($id){
        $data_omzet = CustomerOmzet::with('agen')->where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //data redeem hadiah
            $data_redeem = RedeemDetail::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $data_omzet[0]->kode_customer)->get();

            //data konversi emas
            $data_konversi = RedeemEmas::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $data_omzet[0]->kode_customer)->get();

            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            view()->share('data_redeem', $data_redeem);
            view()->share('data_konversi', $data_konversi);

            return view ('backend.report.view');
        }
    }

}
