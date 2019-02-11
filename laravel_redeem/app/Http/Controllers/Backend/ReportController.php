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
use App\Model\TTOLast;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;
use Excel;

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

        //generate TTO
        if (isset($_GET['export'])){
            Excel::create('TTO Campaign', function($excel) {
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
                
                //ambil data list campaign dulu
                $data_list_campaign = CustomerOmzet::select('customer_omzet.kode_campaign','customer_omzet.kode_customer','campaign_h.jenis')->with('agen')
                                        ->leftJoin('campaign_h','customer_omzet.kode_campaign','campaign_h.kode_campaign')
                                        ->where('campaign_h.active', 1);
                if ($mode != "all"){
                    $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
                    $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
                }

                if ($kode_campaign != ""){
                    $data_list_campaign = $data_list_campaign->where('customer_omzet.kode_campaign','=',$kode_campaign);
                }
        
                $data_list_campaign = $data_list_campaign->get();

                //generate no TTO Emas
                $no_tto_emas = "TTO-AAP-".date('ym')."-2";
                $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->latest()->first();
                if ($last_tto_emas){
                    $no_tto_emas = "TTO-AAP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                } else {
                    $no_tto_emas = $no_tto_emas."0000";
                }

                //generate no TTO Non Emas
                $no_tto_non_emas = "TTO-AAP-".date('ym')."-5";
                $last_tto_non_emas = TTOLast::whereRaw("no_tto like'".$no_tto_non_emas."%'")->latest()->first();
                if ($last_tto_non_emas){
                    $no_tto_non_emas = "TTO-AAP-".date('ym')."-".strval((int)(substr($last_tto_non_emas->no_tto, -5)) + 1);
                } else {
                    $no_tto_non_emas = $no_tto_non_emas."0000";
                }

                //generate Header Detail Emas
                $document_no = "";
                $data_emas_header = [];
                $data_emas_detail = [];
                $data_emas_header_item = [];
                $data_emas_detail_item = [];
                $digit = (int)substr($no_tto_emas, -5);
                foreach ($data_list_campaign as $campaign):
                    $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan')
                                            ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                            ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                            ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                            ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                            ->where('redeem_emas.jumlah','>',0)
                                            ->orderBy('redeem_emas.id','ASC')
                                            ->get();
                    $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                    $line_no = 10000;
                    $ctr = 1;
                    $total = 0;
                    $tipe = "Omzet dan Qty Reward";
                    if ($campaign->jenis == "poin"){
                        $tipe = "Point Reward";
                    }
                    foreach ($data_konversi_emas as $detail_emas):
                        $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $detail_emas->jumlah, $detail_emas->jumlah, $detail_emas->harga * $detail_emas->jumlah, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $detail_emas->jumlah, "0", "H"];
                        array_push($data_emas_detail, $data_emas_detail_item);
                        $ctr = $ctr + 1;
                        $total = $total + ($detail_emas->jumlah * $detail_emas->harga);
                    endforeach;
                    if ($total > 0){
                        $digit = $digit + 1;
                        $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('Y-m-d'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                        array_push($data_emas_header, $data_emas_header_item);
                    }
                endforeach;
    
                $excel->sheet('Header Emas', function($sheet) use($data_emas_header) {
                    $sheet->row(1, array(
                        'Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'
                    ));                    
                    $sheet->fromArray($data_emas_header, null, 'A2', false, false);
                });

                $excel->sheet('Detail Emas', function($sheet) use($data_emas_detail) {
                    $sheet->row(1, array(
                        'Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'
                    ));                    
                    $sheet->fromArray($data_emas_detail, null, 'A2', false, false);
                });
                $last_no_tto_emas = $document_no;
                //save last no tto emas
                if ($last_no_tto_emas != ""){
                    $insert = new TTOLast;
                    $insert->no_tto = $last_no_tto_emas;
                    $insert->save();
                }

                //generate Header Detail Non Emas
                $document_no = "";
                $data_non_emas_header = [];
                $data_non_emas_detail = [];
                $data_non_emas_header_item = [];
                $data_non_emas_detail_item = [];
                $digit = (int)substr($no_tto_non_emas, -5);
                foreach ($data_list_campaign as $campaign):
                    $data_redeem_non_emas = RedeemDetail::select('campaign_d_hadiah.kode_catalogue','campaign_d_hadiah.kode_hadiah','redeem_detail.jumlah','campaign_h.jenis','campaign_d_hadiah.harga','campaign_d_hadiah.satuan', DB::raw('campaign_d_hadiah.jumlah as jum_paket'))
                                            ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_detail.id_campaign')
                                            ->leftJoin('campaign_d_hadiah','redeem_detail.id_campaign_hadiah','=','campaign_d_hadiah.id')
                                            ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                            ->where('redeem_detail.kode_customer','=', $campaign->kode_customer)
                                            ->where('redeem_detail.jumlah','>',0)
                                            ->where('campaign_d_hadiah.emas','=',0)
                                            ->orderBy('redeem_detail.id','ASC')
                                            ->get();
                    $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                    $line_no = 10000;
                    $ctr = 1;
                    $total = 0;
                    $tipe = "Omzet dan Qty Reward";
                    if ($campaign->jenis == "poin"){
                        $tipe = "Point Reward";
                    }
                    foreach ($data_redeem_non_emas as $detail_non_emas):
                        $data_non_emas_detail_item = [$document_no, $line_no * $ctr, $detail_non_emas->kode_catalogue, $detail_non_emas->kode_hadiah, $detail_non_emas->jumlah, $detail_non_emas->jumlah * $detail_non_emas->jum_paket, $detail_non_emas->harga * $detail_non_emas->jumlah, $tipe, $detail_non_emas->satuan, $detail_non_emas->jum_paket, $detail_non_emas->harga * $detail_non_emas->jumlah, "0", "H"];
                        array_push($data_non_emas_detail, $data_non_emas_detail_item);
                        $ctr = $ctr + 1;
                        $total = $total + ($detail_non_emas->jumlah * $detail_non_emas->harga);
                    endforeach;
                    if ($total > 0){
                        $digit = $digit + 1;
                        $data_non_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('Y-m-d'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                        array_push($data_non_emas_header, $data_non_emas_header_item);
                    }
                endforeach;

                $excel->sheet('Header Non Emas', function($sheet) use($data_non_emas_header) {
                    $sheet->row(1, array(
                        'Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'
                    ));                    
                    $sheet->fromArray($data_non_emas_header, null, 'A2', false, false);
                });

                $excel->sheet('Detail Non Emas', function($sheet) use($data_non_emas_detail) {
                    $sheet->row(1, array(
                        'Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'
                    ));                    
                    $sheet->fromArray($data_non_emas_detail, null, 'A2', false, false);
                });
                $last_no_tto_non_emas = $document_no;
                //save last no tto non emas
                if ($last_no_tto_non_emas != ""){
                    $insert = new TTOLast;
                    $insert->no_tto = $last_no_tto_non_emas;
                    $insert->save();
                }

                
            })->export('xls');            

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
        $db2 = env('DB_DATABASE_2');
        $data = CustomerOmzet::select('tbuser.cabang', 'customer_omzet.id', 'campaign_h.kode_campaign', 'campaign_h.nama_campaign','campaign_h.jenis','customer_omzet.periode_awal','customer_omzet.periode_akhir','campaign_h.brosur','customer_omzet.omzet_netto','customer_omzet.poin', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'),'customer_omzet.kode_customer')
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
                ->leftJoin($db2.'.tbuser','customer_omzet.kode_customer','tbuser.reldag')
                ->where('campaign_h.active','=',1)
                ->groupBy('customer_omzet.kode_customer')
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

        if ($kode_campaign != ""){
            $data = $data->where('customer_omzet.kode_campaign','=',$kode_campaign);
        }

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
