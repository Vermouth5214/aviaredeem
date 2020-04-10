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

use App\Exports\ExportExcell;
use App\Exports\ExportExcellRedeem;
use Excel;

class ReportController extends Controller
{
    public function general_report()
    {
        //
        $kode_campaign = "";
        $status = 999;
        $category = 999;
        $jenis = 999;
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
			if (isset($_GET['category'])){
				$category = $_GET['category'];
            }
			if (isset($_GET['jenis'])){
				$jenis = $_GET['jenis'];
            }
        }

        //generate TTO
        if (isset($_GET['export'])){
            if ($category == "CAT"):
                if ($jenis == "omzet"):
                    //ambil data list campaign CAT dulu
                    $data_list_campaign = CustomerOmzet::select('customer_omzet.kode_campaign','customer_omzet.kode_customer','campaign_h.jenis', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'))->with('agen')
                                            ->leftJoin('campaign_h','customer_omzet.kode_campaign','campaign_h.kode_campaign')
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
                                            ->where('campaign_h.active', 1)
                                            ->where('customer_omzet.active', 1)
                                            ->where('campaign_h.category', "CAT")
                                            ->where('campaign_h.jenis', "omzet")
                                            ->groupBy('customer_omzet.kode_customer')
                                            ->groupBy('customer_omzet.kode_campaign')
                                            ->orderBy('redeem_emas.created_at','ASC')
                                            ->orderBy('redeem_detail.created_at','ASC');
                    if ($mode != "all"){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
                    }

                    if ($kode_campaign != ""){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.kode_campaign','=',$kode_campaign);
                    }
                    $data_list_campaign = $data_list_campaign->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
                    $data_list_campaign = $data_list_campaign->get();

                    //generate no TTO Emas
                    $no_tto_emas = "TTO-AAP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTO-AAP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Emas
                    $document_no = "";
                    $data_emas_header = [];
                    $data_emas_detail = [];
                    $data_emas_header_item = [];
                    $data_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);

                    //emas 0.5 gram
                    foreach ($data_list_campaign as $campaign):
                        $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan',DB::raw('campaign_d_emas.jumlah as jumlah_gram'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                                ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_emas.jumlah','>',0)
                                                ->where('campaign_d_emas.kode_hadiah','=','HEMAS05')
                                                ->orderBy('campaign_d_emas.jumlah','DESC')
                                                ->get();
                        $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                        $line_no = 10000;
                        $ctr = 1;
                        $total = 0;
                        $tipe = "Omzet dan Qty Reward";
                        if ($campaign->jenis == "poin"){
                            $tipe = "Point Reward";
                        }
                        $total_gram = 0;
                        $jumlah_emas = 1;
                        foreach ($data_konversi_emas as $detail_emas):
                            $jumlah_emas = 1;
                            for ($i=1;$i<=$detail_emas->jumlah;$i++){
                                if ($total_gram + $detail_emas->jumlah_gram >= 100){
                                    //generate detail emas
                                    if ($jumlah_emas * $detail_emas->jumlah_gram > 100){
                                        $jumlah_emas = $jumlah_emas - 1;
                                        $i = $i - 1;
                                    }
                                    $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                                    $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $jumlah_emas, $jumlah_emas, $detail_emas->harga * $jumlah_emas, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $jumlah_emas, "0", "H"];
                                    array_push($data_emas_detail, $data_emas_detail_item);

                                    $total = $total + ($jumlah_emas * $detail_emas->harga);
                                    //generate header emas
                                    $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                                    array_push($data_emas_header, $data_emas_header_item);

                                    $jumlah_emas = 0;
                                    $total_gram = 0;
                                    $total = 0;
                                    $ctr = 0;
                                    $ctr = $ctr + 1;
                                    $digit = $digit + 1;
                                } else {
                                    $total_gram = $total_gram + $detail_emas->jumlah_gram;
                                }
                                $jumlah_emas = $jumlah_emas + 1;
                                
                            }
                            //generate detail emas
                            if (($jumlah_emas - 1) > 0):
                                $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                                $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, ($jumlah_emas - 1), ($jumlah_emas - 1), $detail_emas->harga * ($jumlah_emas - 1), $tipe, $detail_emas->satuan, 1, $detail_emas->harga * ($jumlah_emas - 1), "0", "H"];
                                array_push($data_emas_detail, $data_emas_detail_item);
                                $total = $total + (($jumlah_emas - 1) * $detail_emas->harga);
                                $ctr = $ctr + 1;
                            endif;
                        endforeach;
                        if ($total > 0):
                            $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                            //generate header emas
                            $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                            array_push($data_emas_header, $data_emas_header_item);

                            $digit = $digit + 1;
                        endif;
                    endforeach;

                    //emas selain 0.5 gram
                    foreach ($data_list_campaign as $campaign):
                        $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan',DB::raw('campaign_d_emas.jumlah as jumlah_gram'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                                ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_emas.jumlah','>',0)
                                                ->where('campaign_d_emas.kode_hadiah','!=','HEMAS05')
                                                ->orderBy('campaign_d_emas.jumlah','DESC')
                                                ->get();
                        $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                        $line_no = 10000;
                        $ctr = 1;
                        $total = 0;
                        $tipe = "Omzet dan Qty Reward";
                        if ($campaign->jenis == "poin"){
                            $tipe = "Point Reward";
                        }
                        $total_gram = 0;
                        $jumlah_emas = 1;
                        foreach ($data_konversi_emas as $detail_emas):
                            $jumlah_emas = 1;
                            for ($i=1;$i<=$detail_emas->jumlah;$i++){
                                if ($total_gram + $detail_emas->jumlah_gram >= 200){
                                    //generate detail emas
                                    if ($jumlah_emas * $detail_emas->jumlah_gram > 200){
                                        $jumlah_emas = $jumlah_emas - 1;
                                        $i = $i - 1;
                                    }
                                    $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                                    $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $jumlah_emas, $jumlah_emas, $detail_emas->harga * $jumlah_emas, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $jumlah_emas, "0", "H"];
                                    array_push($data_emas_detail, $data_emas_detail_item);

                                    $total = $total + ($jumlah_emas * $detail_emas->harga);
                                    //generate header emas
                                    $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                                    array_push($data_emas_header, $data_emas_header_item);

                                    $jumlah_emas = 0;
                                    $total_gram = 0;
                                    $total = 0;
                                    $ctr = 0;
                                    $ctr = $ctr + 1;
                                    $digit = $digit + 1;
                                } else {
                                    $total_gram = $total_gram + $detail_emas->jumlah_gram;
                                }
                                $jumlah_emas = $jumlah_emas + 1;
                                
                            }
                            //generate detail emas
                            if (($jumlah_emas - 1) > 0):
                                $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                                $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, ($jumlah_emas - 1), ($jumlah_emas - 1), $detail_emas->harga * ($jumlah_emas - 1), $tipe, $detail_emas->satuan, 1, $detail_emas->harga * ($jumlah_emas - 1), "0", "H"];
                                array_push($data_emas_detail, $data_emas_detail_item);
                                $total = $total + (($jumlah_emas - 1) * $detail_emas->harga);
                                $ctr = $ctr + 1;
                            endif;
                        endforeach;
                        if ($total > 0):
                            $document_no = "TTO-AAP-".date('ym')."-".strval($digit);
                            //generate header emas
                            $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                            array_push($data_emas_header, $data_emas_header_item);

                            $digit = $digit + 1;
                        endif;
                    endforeach;                  

                    if ($total == 0){
                        $document_no = "TTO-AAP-".date('ym')."-".strval($digit-1);
                    }
                    $last_no_tto_emas = $document_no;
                    //save last no tto emas
                    if ($last_no_tto_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_emas;
                        $insert->save();
                    }
                    $heading_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    //generate Header Detail Non Emas

                    //generate no TTO Emas
                    $no_tto_emas = "TTO-AAP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTO-AAP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    $document_no = "";
                    $data_non_emas_header = [];
                    $data_non_emas_detail = [];
                    $data_non_emas_header_item = [];
                    $data_non_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_redeem_non_emas = RedeemDetail::select('campaign_d_hadiah.kode_catalogue','campaign_d_hadiah.kode_hadiah','redeem_detail.jumlah','campaign_h.jenis','campaign_d_hadiah.harga','campaign_d_hadiah.satuan', DB::raw('campaign_d_hadiah.jumlah as jum_paket'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_detail.id_campaign')
                                                ->leftJoin('campaign_d_hadiah','redeem_detail.id_campaign_hadiah','=','campaign_d_hadiah.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_detail.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_detail.jumlah','>',0)
                                                ->where('campaign_d_hadiah.emas','=',0)
                                                ->orderBy('redeem_detail.id_campaign_hadiah','ASC')
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
                            $data_non_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "AAP-TTO", "MBTRHSL", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                            array_push($data_non_emas_header, $data_non_emas_header_item);
                        }
                    endforeach;

                    $last_no_tto_non_emas = $document_no;
                    //save last no tto non emas
                    if ($last_no_tto_non_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_non_emas;
                        $insert->save();
                    }

                    $heading_non_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_non_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    return Excel::download(new ExportExcell($heading_emas, $data_emas_header, $heading_detail_emas, $data_emas_detail, $heading_non_emas, $data_non_emas_header, $heading_detail_non_emas, $data_non_emas_detail), 'Campaign CAT TTO '.date('Y-m-d').'.xlsx');
                endif;
                if ($jenis == "poin"):
                    $data_list_campaign = CustomerOmzet::select('customer_omzet.kode_campaign','customer_omzet.kode_customer','campaign_h.jenis', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'))->with('agen')
                                            ->leftJoin('campaign_h','customer_omzet.kode_campaign','campaign_h.kode_campaign')
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
                                            ->where('campaign_h.active', 1)
                                            ->where('customer_omzet.active', 1)
                                            ->where('campaign_h.category', "CAT")
                                            ->where('campaign_h.jenis', "poin")
                                            ->groupBy('customer_omzet.kode_customer')
                                            ->groupBy('customer_omzet.kode_campaign')
                                            ->orderBy('redeem_emas.created_at','ASC')
                                            ->orderBy('redeem_detail.created_at','ASC');
                    if ($mode != "all"){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
                    }

                    if ($kode_campaign != ""){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.kode_campaign','=',$kode_campaign);
                    }
                    $data_list_campaign = $data_list_campaign->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
                    $data_list_campaign = $data_list_campaign->get();

                    //generate no TTO Emas
                    $no_tto_emas = "TTP-AAP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTP-AAP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Emas
                    $document_no = "";
                    $data_emas_header = [];
                    $data_emas_detail = [];
                    $data_emas_header_item = [];
                    $data_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan',DB::raw('campaign_d_emas.jumlah as jumlah_gram'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                                ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_emas.jumlah','>',0)
                                                ->orderBy('campaign_d_emas.jumlah','DESC')
                                                ->get();
                        $document_no = "TTP-AAP-".date('ym')."-".strval($digit);
                        $line_no = 10000;
                        $ctr = 1;
                        $total = 0;
                        $tipe = "Omzet dan Qty Reward";
                        if ($campaign->jenis == "poin"){
                            $tipe = "Point Reward";
                        }
                        $total_gram = 0;
                        $jumlah_emas = 1;
                        foreach ($data_konversi_emas as $detail_emas):
                            $jumlah_emas = 1;
                            for ($i=1;$i<=$detail_emas->jumlah;$i++){
                                if ($total_gram + $detail_emas->jumlah_gram >= 200){
                                    //generate detail emas
                                    if ($jumlah_emas * $detail_emas->jumlah_gram > 200){
                                        $jumlah_emas = $jumlah_emas - 1;
                                        $i = $i - 1;
                                    }
                                    $document_no = "TTP-AAP-".date('ym')."-".strval($digit);
                                    $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $jumlah_emas, $jumlah_emas, $detail_emas->harga * $jumlah_emas, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $jumlah_emas, "0", "H"];
                                    array_push($data_emas_detail, $data_emas_detail_item);

                                    $total = $total + ($jumlah_emas * $detail_emas->harga);
                                    //generate header emas
                                    $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "AAP-TTP", "MBTRPOIN", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                                    array_push($data_emas_header, $data_emas_header_item);

                                    $jumlah_emas = 0;
                                    $total_gram = 0;
                                    $total = 0;
                                    $ctr = 0;
                                    $ctr = $ctr + 1;
                                    $digit = $digit + 1;
                                } else {
                                    $total_gram = $total_gram + $detail_emas->jumlah_gram;
                                }
                                $jumlah_emas = $jumlah_emas + 1;
                                
                            }
                            //generate detail emas
                            if (($jumlah_emas - 1) > 0):
                                $document_no = "TTP-AAP-".date('ym')."-".strval($digit);
                                $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, ($jumlah_emas - 1), ($jumlah_emas - 1), $detail_emas->harga * ($jumlah_emas - 1), $tipe, $detail_emas->satuan, 1, $detail_emas->harga * ($jumlah_emas - 1), "0", "H"];
                                array_push($data_emas_detail, $data_emas_detail_item);
                                $total = $total + (($jumlah_emas - 1) * $detail_emas->harga);
                                $ctr = $ctr + 1;
                            endif;
                        endforeach;
                        if ($total > 0):
                            $document_no = "TTP-AAP-".date('ym')."-".strval($digit);
                            //generate header emas
                            $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "AAP-TTP", "MBTRPOIN", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                            array_push($data_emas_header, $data_emas_header_item);

                            $digit = $digit + 1;
                        endif;
                    endforeach;

                    $last_no_tto_emas = $document_no;
                    //save last no tto emas
                    if ($last_no_tto_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_emas;
                        $insert->save();
                    }

                    $heading_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Status', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    //generate no TTO Emas
                    $no_tto_emas = "TTP-AAP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTP-AAP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Non Emas
                    $document_no = "";
                    $data_non_emas_header = [];
                    $data_non_emas_detail = [];
                    $data_non_emas_header_item = [];
                    $data_non_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_redeem_non_emas = RedeemDetail::select('campaign_d_hadiah.kode_catalogue','campaign_d_hadiah.kode_hadiah','redeem_detail.jumlah','campaign_h.jenis','campaign_d_hadiah.harga','campaign_d_hadiah.satuan', DB::raw('campaign_d_hadiah.jumlah as jum_paket'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_detail.id_campaign')
                                                ->leftJoin('campaign_d_hadiah','redeem_detail.id_campaign_hadiah','=','campaign_d_hadiah.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_detail.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_detail.jumlah','>',0)
                                                ->where('campaign_d_hadiah.emas','=',0)
                                                ->orderBy('redeem_detail.id_campaign_hadiah','ASC')
                                                ->get();
                        $document_no = "TTP-AAP-".date('ym')."-".strval($digit);
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
                            $data_non_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "AAP-TTP", "MBTRPOIN", "DEFAULT", "AAP-FGBAIK", "AAP", $campaign->agen->salesperson];
                            array_push($data_non_emas_header, $data_non_emas_header_item);
                        }
                    endforeach;

                    $last_no_tto_non_emas = $document_no;
                    //save last no tto non emas
                    if ($last_no_tto_non_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_non_emas;
                        $insert->save();
                    }

                    $heading_non_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Status', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_non_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    return Excel::download(new ExportExcell($heading_emas, $data_emas_header, $heading_detail_emas, $data_emas_detail, $heading_non_emas, $data_non_emas_header, $heading_detail_non_emas, $data_non_emas_detail), 'Campaign CAT TTP '.date('Y-m-d').'.xlsx');

                endif;
            endif;

            if ($category == "PIPA"):
                /*========================================================*/
                /*                      PIPA                              */
                /*========================================================*/
                if ($jenis == "omzet"):
                    $data_list_campaign = CustomerOmzet::select('customer_omzet.kode_campaign','customer_omzet.kode_customer','campaign_h.jenis', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'))->with('agen')
                                            ->leftJoin('campaign_h','customer_omzet.kode_campaign','campaign_h.kode_campaign')
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
                                            ->where('campaign_h.active', 1)
                                            ->where('customer_omzet.active', 1)
                                            ->where('campaign_h.category', "PIPA")
                                            ->where('campaign_h.jenis', "omzet")
                                            ->groupBy('customer_omzet.kode_customer')
                                            ->groupBy('customer_omzet.kode_campaign')
                                            ->orderBy('redeem_emas.created_at','ASC')
                                            ->orderBy('redeem_detail.created_at','ASC');
                    if ($mode != "all"){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
                    }

                    if ($kode_campaign != ""){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.kode_campaign','=',$kode_campaign);
                    }
                    $data_list_campaign = $data_list_campaign->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
                    $data_list_campaign = $data_list_campaign->get();
                    
                    //generate no TTO Emas
                    $no_tto_emas = "TTO-IPP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTO-IPP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Emas
                    $document_no = "";
                    $data_emas_header = [];
                    $data_emas_detail = [];
                    $data_emas_header_item = [];
                    $data_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan',DB::raw('campaign_d_emas.jumlah as jumlah_gram'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                                ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_emas.jumlah','>',0)
                                                ->orderBy('campaign_d_emas.jumlah','DESC')
                                                ->get();
                        $document_no = "TTO-IPP-".date('ym')."-".strval($digit);
                        $line_no = 10000;
                        $ctr = 1;
                        $total = 0;
                        $tipe = "Omzet dan Qty Reward";
                        if ($campaign->jenis == "poin"){
                            $tipe = "Point Reward";
                        }
                        $total_gram = 0;
                        $jumlah_emas = 1;
                        foreach ($data_konversi_emas as $detail_emas):
                            $jumlah_emas = 1;
                            for ($i=1;$i<=$detail_emas->jumlah;$i++){
                                if ($total_gram + $detail_emas->jumlah_gram >= 200){
                                    //generate detail emas
                                    if ($jumlah_emas * $detail_emas->jumlah_gram > 200){
                                        $jumlah_emas = $jumlah_emas - 1;
                                        $i = $i - 1;
                                    }
                                    $document_no = "TTO-IPP-".date('ym')."-".strval($digit);
                                    $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $jumlah_emas, $jumlah_emas, $detail_emas->harga * $jumlah_emas, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $jumlah_emas, "0", "H"];
                                    array_push($data_emas_detail, $data_emas_detail_item);

                                    $total = $total + ($jumlah_emas * $detail_emas->harga);
                                    //generate header emas
                                    $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "IPP-TTO", "MBTRHSL", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                                    array_push($data_emas_header, $data_emas_header_item);

                                    $jumlah_emas = 0;
                                    $total_gram = 0;
                                    $total = 0;
                                    $ctr = 0;
                                    $ctr = $ctr + 1;
                                    $digit = $digit + 1;
                                } else {
                                    $total_gram = $total_gram + $detail_emas->jumlah_gram;
                                }
                                $jumlah_emas = $jumlah_emas + 1;
                                
                            }
                            //generate detail emas
                            if (($jumlah_emas - 1) > 0):
                                $document_no = "TTO-IPP-".date('ym')."-".strval($digit);
                                $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, ($jumlah_emas - 1), ($jumlah_emas - 1), $detail_emas->harga * ($jumlah_emas - 1), $tipe, $detail_emas->satuan, 1, $detail_emas->harga * ($jumlah_emas - 1), "0", "H"];
                                array_push($data_emas_detail, $data_emas_detail_item);
                                $total = $total + (($jumlah_emas - 1) * $detail_emas->harga);
                                $ctr = $ctr + 1;
                            endif;
                        endforeach;
                        if ($total > 0):
                            $document_no = "TTO-IPP-".date('ym')."-".strval($digit);
                            //generate header emas
                            $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "IPP-TTO", "MBTRHSL", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                            array_push($data_emas_header, $data_emas_header_item);

                            $digit = $digit + 1;
                        endif;
                    endforeach;

                    $last_no_tto_emas = $document_no;
                    //save last no tto emas
                    if ($last_no_tto_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_emas;
                        $insert->save();
                    }
        
                    $heading_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];


                    //generate no TTO Emas
                    $no_tto_emas = "TTO-IPP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTO-IPP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Non Emas
                    $document_no = "";
                    $data_non_emas_header = [];
                    $data_non_emas_detail = [];
                    $data_non_emas_header_item = [];
                    $data_non_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_redeem_non_emas = RedeemDetail::select('campaign_d_hadiah.kode_catalogue','campaign_d_hadiah.kode_hadiah','redeem_detail.jumlah','campaign_h.jenis','campaign_d_hadiah.harga','campaign_d_hadiah.satuan', DB::raw('campaign_d_hadiah.jumlah as jum_paket'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_detail.id_campaign')
                                                ->leftJoin('campaign_d_hadiah','redeem_detail.id_campaign_hadiah','=','campaign_d_hadiah.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_detail.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_detail.jumlah','>',0)
                                                ->where('campaign_d_hadiah.emas','=',0)
                                                ->orderBy('redeem_detail.id_campaign_hadiah','ASC')
                                                ->get();
                        $document_no = "TTO-IPP-".date('ym')."-".strval($digit);
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
                            $data_non_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), $total, "New", "0", $tipe, "IPP-TTO", "MBTRHSL", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                            array_push($data_non_emas_header, $data_non_emas_header_item);
                        }
                    endforeach;

                    $last_no_tto_non_emas = $document_no;
                    //save last no tto non emas
                    if ($last_no_tto_non_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_non_emas;
                        $insert->save();
                    }

                    $heading_non_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Total OQ', 'Status', 'Discount %', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_non_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    return Excel::download(new ExportExcell($heading_emas, $data_emas_header, $heading_detail_emas, $data_emas_detail, $heading_non_emas, $data_non_emas_header, $heading_detail_non_emas, $data_non_emas_detail), 'Campaign PIPA TTO '.date('Y-m-d').'.xlsx');
                endif;
                if ($jenis == "poin"):
                    $data_list_campaign = CustomerOmzet::select('customer_omzet.kode_campaign','customer_omzet.kode_customer','campaign_h.jenis', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'))->with('agen')
                                            ->leftJoin('campaign_h','customer_omzet.kode_campaign','campaign_h.kode_campaign')
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
                                            ->where('campaign_h.active', 1)
                                            ->where('customer_omzet.active', 1)
                                            ->where('campaign_h.category', "PIPA")
                                            ->where('campaign_h.jenis', "poin")
                                            ->groupBy('customer_omzet.kode_customer')
                                            ->groupBy('customer_omzet.kode_campaign')
                                            ->orderBy('redeem_emas.created_at','ASC')
                                            ->orderBy('redeem_detail.created_at','ASC');
                    if ($mode != "all"){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_awal','>=', date('Y-m-d 00:00:00',strtotime($startDate)));
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.periode_akhir','<=',date('Y-m-d 23:59:59',strtotime($endDate)));
                    }

                    if ($kode_campaign != ""){
                        $data_list_campaign = $data_list_campaign->where('customer_omzet.kode_campaign','=',$kode_campaign);
                    }
                    $data_list_campaign = $data_list_campaign->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
                    $data_list_campaign = $data_list_campaign->get();

                    //generate no TTO Emas
                    $no_tto_emas = "TTP-IPP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTP-IPP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Emas
                    $document_no = "";
                    $data_emas_header = [];
                    $data_emas_detail = [];
                    $data_emas_header_item = [];
                    $data_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_konversi_emas = RedeemEmas::select('campaign_d_emas.kode_catalogue','campaign_d_emas.kode_hadiah','redeem_emas.jumlah','campaign_h.jenis','campaign_d_emas.harga','campaign_d_emas.satuan',DB::raw('campaign_d_emas.jumlah as jumlah_gram'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_emas.id_campaign')
                                                ->leftJoin('campaign_d_emas','redeem_emas.id_campaign_emas','=','campaign_d_emas.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_emas.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_emas.jumlah','>',0)
                                                ->orderBy('campaign_d_emas.jumlah','DESC')
                                                ->get();
                        $document_no = "TTP-IPP-".date('ym')."-".strval($digit);
                        $line_no = 10000;
                        $ctr = 1;
                        $total = 0;
                        $tipe = "Omzet dan Qty Reward";
                        if ($campaign->jenis == "poin"){
                            $tipe = "Point Reward";
                        }
                        $total_gram = 0;
                        $jumlah_emas = 1;
                        foreach ($data_konversi_emas as $detail_emas):
                            $jumlah_emas = 1;
                            for ($i=1;$i<=$detail_emas->jumlah;$i++){
                                if ($total_gram + $detail_emas->jumlah_gram >= 200){
                                    //generate detail emas
                                    if ($jumlah_emas * $detail_emas->jumlah_gram > 200){
                                        $jumlah_emas = $jumlah_emas - 1;
                                        $i = $i - 1;
                                    }
                                    $document_no = "TTP-IPP-".date('ym')."-".strval($digit);
                                    $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, $jumlah_emas, $jumlah_emas, $detail_emas->harga * $jumlah_emas, $tipe, $detail_emas->satuan, 1, $detail_emas->harga * $jumlah_emas, "0", "H"];
                                    array_push($data_emas_detail, $data_emas_detail_item);

                                    $total = $total + ($jumlah_emas * $detail_emas->harga);
                                    //generate header emas
                                    $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "IPP-TTP", "MBTRPOIN", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                                    array_push($data_emas_header, $data_emas_header_item);

                                    $jumlah_emas = 0;
                                    $total_gram = 0;
                                    $total = 0;
                                    $ctr = 0;
                                    $ctr = $ctr + 1;
                                    $digit = $digit + 1;
                                } else {
                                    $total_gram = $total_gram + $detail_emas->jumlah_gram;
                                }
                                $jumlah_emas = $jumlah_emas + 1;
                                
                            }
                            //generate detail emas
                            if (($jumlah_emas - 1) > 0):
                                $document_no = "TTP-IPP-".date('ym')."-".strval($digit);
                                $data_emas_detail_item = [$document_no, $line_no * $ctr, $detail_emas->kode_catalogue, $detail_emas->kode_hadiah, ($jumlah_emas - 1), ($jumlah_emas - 1), $detail_emas->harga * ($jumlah_emas - 1), $tipe, $detail_emas->satuan, 1, $detail_emas->harga * ($jumlah_emas - 1), "0", "H"];
                                array_push($data_emas_detail, $data_emas_detail_item);
                                $total = $total + (($jumlah_emas - 1) * $detail_emas->harga);
                                $ctr = $ctr + 1;
                            endif;
                        endforeach;
                        if ($total > 0):
                            $document_no = "TTP-IPP-".date('ym')."-".strval($digit);
                            //generate header emas
                            $data_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "IPP-TTP", "MBTRPOIN", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                            array_push($data_emas_header, $data_emas_header_item);

                            $digit = $digit + 1;
                        endif;
                    endforeach;
                    
                    $last_no_tto_emas = $document_no;
                    //save last no tto emas
                    if ($last_no_tto_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_emas;
                        $insert->save();
                    }

                    $heading_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Status', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    //generate no TTO Emas
                    $no_tto_emas = "TTP-IPP-".date('ym')."-2";
                    $last_tto_emas = TTOLast::whereRaw("no_tto like'".$no_tto_emas."%'")->orderBy('id', 'DESC')->first();
                    if ($last_tto_emas){
                        $no_tto_emas = "TTP-IPP-".date('ym')."-".strval((int)(substr($last_tto_emas->no_tto, -5)) + 1);
                    } else {
                        $no_tto_emas = $no_tto_emas."0000";
                    }

                    //generate Header Detail Non Emas
                    $document_no = "";
                    $data_non_emas_header = [];
                    $data_non_emas_detail = [];
                    $data_non_emas_header_item = [];
                    $data_non_emas_detail_item = [];
                    $digit = (int)substr($no_tto_emas, -5);
                    foreach ($data_list_campaign as $campaign):
                        $data_redeem_non_emas = RedeemDetail::select('campaign_d_hadiah.kode_catalogue','campaign_d_hadiah.kode_hadiah','redeem_detail.jumlah','campaign_h.jenis','campaign_d_hadiah.harga','campaign_d_hadiah.satuan', DB::raw('campaign_d_hadiah.jumlah as jum_paket'))
                                                ->leftJoin('campaign_h', 'campaign_h.id','=','redeem_detail.id_campaign')
                                                ->leftJoin('campaign_d_hadiah','redeem_detail.id_campaign_hadiah','=','campaign_d_hadiah.id')
                                                ->where('campaign_h.kode_campaign','=', $campaign->kode_campaign)
                                                ->where('redeem_detail.kode_customer','=', $campaign->kode_customer)
                                                ->where('redeem_detail.jumlah','>',0)
                                                ->where('campaign_d_hadiah.emas','=',0)
                                                ->orderBy('redeem_detail.id_campaign_hadiah','ASC')
                                                ->get();
                        $document_no = "TTP-IPP-".date('ym')."-".strval($digit);
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
                            $data_non_emas_header_item = [$document_no, $campaign->kode_customer, $campaign->kode_campaign, date('d-m-Y'), "New", $tipe, "IPP-TTP", "MBTRPOIN", "DEFAULT", "IPP-FG", "IPP", $campaign->agen->salesperson];
                            array_push($data_non_emas_header, $data_non_emas_header_item);
                        }
                    endforeach;

                    $last_no_tto_non_emas = $document_no;
                    //save last no tto non emas
                    if ($last_no_tto_non_emas != ""){
                        $insert = new TTOLast;
                        $insert->no_tto = $last_no_tto_non_emas;
                        $insert->save();
                    }

                    $heading_non_emas = ['Document No.', 'Customer No.', 'Campaign Code', 'Posting Date', 'Status', 'Type', 'No. Series', 'Journal Template Name', 'Journal Batch Name', 'Location Code', 'Branch Code', 'Salesperson Code'];
                    $heading_detail_non_emas = ['Document No.', 'Line No.', 'Catalog Code', 'Item No.', 'Qty. Catalog', 'Quantity', 'OQ Needed', 'Type', 'Unit of Measure', 'Quantity Per.', 'OQ Needed After Disc.', 'Paket', 'Bin Code'];

                    return Excel::download(new ExportExcell($heading_emas, $data_emas_header, $heading_detail_emas, $data_emas_detail, $heading_non_emas, $data_non_emas_header, $heading_detail_non_emas, $data_non_emas_detail), 'Campaign PIPA TTP '.date('Y-m-d').'.xlsx');
                endif;                
            endif;
        }
        
		view()->share('startDate',$startDate);
		view()->share('endDate',$endDate);
        view()->share('status',$status);
        view()->share('jenis',$jenis);
        view()->share('mode',$mode);
        view()->share('kode_campaign',$kode_campaign);
        view()->share('campaign',$campaign);
        view()->share('category',$category);

		return view ('backend.report.index');
    }

	public function datatable() {
        $db2 = env('DB_DATABASE_2');
        $data = CustomerOmzet::select('tbuser.cabang', 'customer_omzet.id', 'campaign_h.kode_campaign', 'campaign_h.nama_campaign','campaign_h.jenis','campaign_h.category','customer_omzet.periode_awal','customer_omzet.periode_akhir','campaign_h.brosur','customer_omzet.omzet_netto','customer_omzet.poin', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'),'customer_omzet.kode_customer')
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
                ->leftJoin($db2.'.tbuser', function ($join){
                    $join->on('customer_omzet.kode_customer', '=', 'tbuser.reldag');
                    $join->on('tbuser.cabang','<>', DB::raw("''"));
                })
                ->where('campaign_h.active','=',1)
                ->where('customer_omzet.active','=',1)
                ->groupBy('customer_omzet.kode_customer')
                ->groupBy('customer_omzet.kode_campaign');
        $kode_campaign = "";                
        $status = 999;
        $category = 999;
        $jenis = 999;
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
            if (isset($_GET['category'])){
                $category = $_GET['category'];
            }
            if (isset($_GET['jenis'])){
                $jenis = $_GET['jenis'];
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

        if ($category != 999){
            $data = $data->where('campaign_h.category', $category);
        }
        if ($jenis != 999){
            $data = $data->where('campaign_h.jenis', $jenis);
        }

        if ($status == "1"){
            $data = $data->havingRaw('(jum_redeem_detail > 0 and jum_emas = 0) or jum_redeem_emas > 0');
        }
        if ($status == '2'){
            $data = $data->havingRaw("jum_redeem_detail = 0 and customer_omzet.periode_akhir <'". date('Y-m-d') ."'" );
        }
        if ($status == '3'){
            $data = $data->havingRaw("(jum_redeem_detail > 0 and jum_redeem_emas = 0) and jum_emas > 0 and customer_omzet.periode_akhir <'". date('Y-m-d') ."'" );
        }
        if ($status == '4'){
            $data = $data->havingRaw("jum_redeem_detail = 0 and customer_omzet.periode_akhir >='". date('Y-m-d') ."'" );
        }
        if ($status == '5'){
            $data = $data->havingRaw("jum_emas > 0 and jum_redeem_detail > 0 and jum_redeem_emas = 0 and customer_omzet.periode_akhir >='". date('Y-m-d') ."'");
        }
        if ($status == '6'){
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
                $brosur = explode(";",$data->brosur);
                $im = "";
                foreach ($brosur as $ctr=>$image):
                    if ($ctr > 0):
                        $im = $im . '<a href="'.url('upload/Brosur/'.$image).'" target="_blank">'.$image.'</a><br/>';
                    endif;
                endforeach;
                return $im;
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
                } else
                if ($data->jum_redeem_detail == 0 && (date('Y-m-d',strtotime($data->periode_akhir)) < date('Y-m-d'))){
                    $status = 2;
                } else 
                if ($data->jum_redeem_detail > 0 && $data->jum_emas > 0 && $data->jum_redeem_emas == 0 && (date('Y-m-d',strtotime($data->periode_akhir)) < date('Y-m-d'))){
                    $status = 3;
                } else 
                if ($data->jum_redeem_detail == 0 && (date('Y-m-d',strtotime($data->periode_akhir)) >= date('Y-m-d'))){
                    $status = 4;
                } else 
                if (($data->jum_emas > 0) && ($data->jum_redeem_detail > 0) && ($data->jum_redeem_emas == 0) && (date('Y-m-d',strtotime($data->periode_akhir)) >= date('Y-m-d'))){
                    $status = 5;
                } else 
                if (date('Y-m-d') < date('Y-m-d',strtotime($data->periode_awal))){
                    $status = 6;
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
            $data_redeem = RedeemDetail::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $data_omzet[0]->kode_customer)->orderBy('id_campaign_hadiah', 'ASC')->get();

            //data konversi emas
            $data_konversi = RedeemEmas::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $data_omzet[0]->kode_customer)->orderBy('id_campaign_emas', 'ASC')->get();

            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            view()->share('data_redeem', $data_redeem);
            view()->share('data_konversi', $data_konversi);

            return view ('backend.report.view');
        }
    }

    public function redeem_report(){
        $db2 = env('DB_DATABASE_2');
        $kode_campaign = 999;
        $nomor_campaign = "";
        if (isset($_GET['kode_campaign'])){
            $kode_campaign = $_GET['kode_campaign'];
            $get_campaign = CampaignH::find($kode_campaign);
            if ($get_campaign){
                $nomor_campaign = $get_campaign->kode_campaign;
            }
        }
        $campaign = CampaignH::where('active', '=', 1)->orderBy('id','DESC')->pluck('kode_campaign','id')->prepend('Pilih Campaign','999')->toArray();
        $campaign = array_map('strtoupper', $campaign);

        $data = DB::select(
                "
                    select al.kode_campaign, al.kode_customer, ".$db2.".tbuser.cabang, co.omzet_netto, co.poin,
                            al.kode_hadiah, al.nama_hadiah, al.jumlah, al.jumlah_paket, al.jumlah_total, emas, idS
                            from(
                                select ch.kode_campaign, rd.kode_customer, 
                                        dh.kode_hadiah, dh.nama_hadiah, 
                                        dh.jumlah, rd.jumlah as jumlah_paket, (dh.jumlah*rd.jumlah) as jumlah_total, dh.emas, 0 as idS
                                    from 
                                    redeem_detail rd
                                    left join
                                    campaign_h ch
                                    on rd.id_campaign = ch.id
                                    left join
                                    campaign_d_hadiah dh
                                    on rd.id_campaign_hadiah = dh.id
                                    where dh.emas = 0 and rd.id_campaign = '".$kode_campaign."'
                                union all
                                select * from (
                                    select ch.kode_campaign, rd.kode_customer, 
                                            dh.kode_hadiah, dh.nama_hadiah, 
                                            dh.jumlah, rd.jumlah as jumlah_paket, (dh.jumlah*rd.jumlah) as jumlah_total, 1 as emas, dh.id as idS
                                        from 
                                        redeem_emas rd
                                        left join
                                        campaign_h ch
                                        on rd.id_campaign = ch.id
                                        left join
                                        campaign_d_emas dh
                                        on rd.id_campaign_emas = dh.id
                                        where rd.id_campaign = '".$kode_campaign."'
                                        order by dh.id ASC
                                ) ccc
                    ) al
                    left join 
                    ".$db2.".tbuser
                    on ".$db2.".tbuser.uname = al.kode_customer
                    left join
                    customer_omzet co
                    on co.kode_campaign = al.kode_campaign and co.kode_customer = al.kode_customer
                    where jumlah_paket <> 0
                    group by al.kode_campaign, kode_customer, kode_hadiah
                    order by kode_customer, idS ASC
                "
        );

        if (isset($_GET['export'])){
            return Excel::download(new ExportExcellRedeem($data), 'Redeem Campaign '.str_replace("/","_",$nomor_campaign)." ".date('Y-m-d').'.xlsx');
        }

        view()->share('campaign', $campaign);
        view()->share('kode_campaign', $kode_campaign);
        view()->share('data', $data);
        return view ('backend.report.redeem');
    }

}
