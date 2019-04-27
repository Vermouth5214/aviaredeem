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
        $status = 999;
        if (isset($_GET['status'])){
            $status = $_GET['status'];
        }
        view()->share('status', $status);
		return view ('backend.redeem.index');
    }
	
	public function datatable() {
        $userinfo = Session::get('userinfo');
        $data = CustomerOmzet::select('customer_omzet.id', 'campaign_h.kode_campaign', 'campaign_h.nama_campaign','campaign_h.jenis','customer_omzet.periode_awal','customer_omzet.periode_akhir','campaign_h.brosur','customer_omzet.omzet_netto','customer_omzet.poin', DB::raw('count(distinct campaign_d_hadiah.id) as jum_emas'),DB::raw('count(distinct redeem_detail.id) as jum_redeem_detail'),DB::raw('count(distinct redeem_emas.id) as jum_redeem_emas'))
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
                ->where('customer_omzet.active','=',1)
                ->where('customer_omzet.periode_awal','<=',date('Y-m-d'))
                ->where('customer_omzet.kode_customer',$userinfo['reldag'])
                ->groupBy('customer_omzet.kode_campaign');

        $status = 999;
        if (isset($_GET['status'])){
            $status = $_GET['status'];
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
            ->editColumn('periode_awal', function($data) {
                return date('d M Y',strtotime($data->periode_awal));
            })
            ->editColumn('periode_akhir', function($data) {
                return date('d M Y',strtotime($data->periode_akhir));
            })
            ->editColumn('omzet_netto', function($data) {
                return number_format($data->omzet_netto,0,',','.');
            })
            ->editColumn('poin', function($data) {
                return number_format($data->poin,0,',','.');
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
			->addColumn('action', function ($data) {
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

                $url_view = url('backend/redeem-hadiah/'.$data->id);
                $url_klaim = url('backend/redeem-hadiah/'.$data->id.'/klaim-hadiah');
                $url_edit_klaim = url('backend/redeem-hadiah/'.$data->id.'/edit/klaim-hadiah');
                $url_konvert = url('backend/redeem-hadiah/'.$data->id.'/konversi-emas');

                $view = "<a class='btn-action btn btn-primary' href='".$url_view."' title='View'><i class='fa fa-eye'></i> View</a>";  
                $klaim = "<a class='btn-action btn btn-warning' href='".$url_klaim."' title='Klaim Hadiah'><i class='fa fa-gift'></i> Klaim Hadiah</a>";  
                $konvert = "<a class='btn-action btn btn-danger' href='".$url_konvert."' title='Konversi Emas'><i class='fa fa-exchange'></i> Konversi Emas</a>";  
                $edit_klaim = "";  

                if ($status == 1){
                    $klaim = "";
                    $konvert = "";
                }
                if ($status == 2 ){
                    $klaim = "";
                    $konvert = "";
                }
                if ($status == 3 ){
                    $klaim = "";
                    $konvert = "";
                }
                if ($status == 4){
                    $konvert = "";
                }
                if ($status == 5){
                    $klaim = "";
                    $edit_klaim = "<a class='btn-action btn btn-warning' href='".$url_edit_klaim."' title='Edit Klaim Hadiah'><i class='fa fa-gift'></i> Edit Klaim Hadiah</a>";  
                }
                if ($status == 6){
                    $klaim = "";
                    $konvert = "";
                }

                return $view." ".$klaim." ".$edit_klaim." ".$konvert;
            })			
            ->rawColumns(['action','brosur'])
            ->make(true);
    }
    
    public function klaim_hadiah($id){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah di klaim atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek tanggal klaim
            if (date('Y-m-d') < date('Y-m-d',strtotime($data_omzet[0]->periode_awal))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kedaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //data list hadiah
            $data_list_hadiah_emas =  CampaignDHadiah::select('id','nama_hadiah','harga','satuan')
                                    ->where('id_campaign', $data_header[0]->id)
                                    ->where('pilihan',0)->orderBy('id','ASC')->get();
            
            $data_list_hadiah_pilihan = CampaignDBagi::select('campaign_d_hadiah.id','campaign_d_hadiah.nama_hadiah','campaign_d_hadiah.harga','campaign_d_hadiah.satuan')
                                        ->leftJoin('campaign_d_hadiah','campaign_d_bagi.id_campaign_d_hadiah','=','campaign_d_hadiah.id')
                                        ->where('campaign_d_bagi.id_campaign',$data_header[0]->id)
                                        ->where('campaign_d_bagi.kode_agen',$userinfo['reldag'])->orderBy('campaign_d_hadiah.id','ASC')->get();

            $data_list_hadiah = $data_list_hadiah_emas->merge($data_list_hadiah_pilihan);

            //ambil harga terendah
            $harga_terendah = 999999999;
            foreach ($data_list_hadiah as $hadiah ):
                if ($hadiah['harga'] < $harga_terendah){
                    $harga_terendah = $hadiah['harga'];
                }
            endforeach;

            view()->share('harga_terendah', $harga_terendah);
            view()->share('data_list_hadiah', $data_list_hadiah);
            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            return view ('backend.redeem.klaim_hadiah');
        }
    }

    public function klaim_hadiah_update($id, Request $request){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah di klaim atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek tanggal klaim
            if (date('Y-m-d') < date('Y-m-d',strtotime($data_omzet[0]->periode_awal))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kedaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //data list hadiah
            $data_list_hadiah_emas =  CampaignDHadiah::select('id','nama_hadiah','harga')
                                    ->where('id_campaign', $data_header[0]->id)
                                    ->where('pilihan',0)->orderBy('id','ASC');
            $data_list_hadiah_pilihan = CampaignDBagi::select('campaign_d_hadiah.id','campaign_d_hadiah.nama_hadiah','campaign_d_hadiah.harga')
                                        ->leftJoin('campaign_d_hadiah','campaign_d_bagi.id_campaign_d_hadiah','=','campaign_d_hadiah.id')
                                        ->where('campaign_d_bagi.id_campaign',$data_header[0]->id)
                                        ->where('campaign_d_bagi.kode_agen',$userinfo['reldag'])->orderBy('campaign_d_hadiah.id','ASC');

            $data_list_hadiah = $data_list_hadiah_emas->union($data_list_hadiah_pilihan)->get();

            //ambil harga terendah
            $harga_terendah = 999999999;
            foreach ($data_list_hadiah as $hadiah ):
                if ($hadiah['harga'] < $harga_terendah){
                    $harga_terendah = $hadiah['harga'];
                }
            endforeach;

            //cek total
            $total = 0;
            $subtotal = 0;
            if ($data_header[0]->jenis == "omzet"){
                $total = $data_omzet[0]->omzet_netto;
            }
            if ($data_header[0]->jenis == "poin"){
                $total = $data_omzet[0]->poin;
            }
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $subtotal = $subtotal + ( floor($_POST['jumlah'][$ctr] / 1) * $_POST['harga'][$ctr]);
            endforeach;

            $sisa = $total - $subtotal;
            if ($sisa < 0){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/klaim-hadiah')->with('success', "Penukaran hadiah melebihi omzet / poin")->with('mode', 'danger');
            }
            if ($sisa >= $harga_terendah){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/klaim-hadiah')->with('success', "Sisa omzet / poin masih bisa ditukarkan dengan hadiah lain")->with('mode', 'danger');
            }

            //insert data
            $ada_emas = 0;
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $data = new RedeemDetail();
                $data->kode_customer = $data_omzet[0]->kode_customer;
                $data->id_campaign = $data_header[0]->id;
                $data->id_campaign_hadiah = $id_hadiah;
                $data->jumlah = floor($_POST['jumlah'][$ctr] / 1);
                //cek ada redeem emas atau ga
                $cek_emas = CampaignDHadiah::where('id', $id_hadiah)->get();
                if ($cek_emas){
                    if (($data->jumlah > 0) && ($cek_emas[0]->emas == 1)) {
                        $ada_emas = 1;
                    }
                }
                $data->save();
            endforeach;

            if ($ada_emas == 0){
                //simpan redeem emas menjadi 0 agar jadi status sudah klaim
                $data_konversi = CampaignDEmas::where('id_campaign',$data_header[0]->id)->orderBy('id','ASC')->get();
                if ($data_konversi){
                    foreach ($data_konversi as $detail):
                        $data = new RedeemEmas();
                        $data->kode_customer = $data_omzet[0]->kode_customer;
                        $data->id_campaign = $data_header[0]->id;
                        $data->id_campaign_emas = $detail->id;
                        $data->jumlah = 0;
                        $data->save();
                    endforeach;
                }
                return Redirect::to('/backend/redeem-hadiah/')->with('success', "Data saved successfully")->with('mode', 'success');
            } else {
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/konversi-emas');
            }
        }
    }

    public function konversi_emas($id){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah redeem detail atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem == 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek sudah konversi emas atau belum
            $jum_konversi = RedeemEmas::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_konversi > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kadaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }


            //hitung total gram emas
            $data_redeem = RedeemDetail::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer',$userinfo['reldag'])->orderBy('id_campaign_hadiah', 'ASC')->get();
            $total_gram = 0;
            foreach ($data_redeem as $detail):
                if ($detail->campaign_hadiah->emas == 1){
                    $total_gram = $total_gram + ($detail->jumlah * $detail->campaign_hadiah->jumlah);
                }
            endforeach;

            //list konversi emas
            $data_konversi = CampaignDEmas::where('id_campaign',$data_header[0]->id)->orderBy('id','ASC')->get();

            view()->share('total_gram', $total_gram);
            view()->share('data_konversi', $data_konversi);
            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            view()->share('data_redeem', $data_redeem);
            return view ('backend.redeem.konversi_emas');
        }
    }

    public function konversi_emas_update($id, Request $request){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah redeem detail atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem == 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek sudah konversi emas atau belum
            $jum_konversi = RedeemEmas::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_konversi > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kedaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //hitung total gram emas
            $data_redeem = RedeemDetail::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer',$userinfo['reldag'])->get();
            $total_gram = 0;
            foreach ($data_redeem as $detail):
                if ($detail->campaign_hadiah->emas == 1){
                    $total_gram = $total_gram + ($detail->jumlah * $detail->campaign_hadiah->jumlah);
                }
            endforeach;

            $subtotal = 0;
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $subtotal = $subtotal + ( floor($_POST['jumlah'][$ctr] / 1) * $_POST['jumlah_emas'][$ctr]);
            endforeach;

            $sisa = $total_gram - $subtotal;
            if ($sisa < 0){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/konversi-emas')->with('success', "Konversi Emas melebihi omzet")->with('mode', 'danger');
            }
            if ($sisa > 0){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/konversi-emas')->with('success', "Sisa omzet masih bisa dikonversikan")->with('mode', 'danger');
            }

            //insert data
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $data = new RedeemEmas();
                $data->kode_customer = $data_omzet[0]->kode_customer;
                $data->id_campaign = $data_header[0]->id;
                $data->id_campaign_emas = $id_hadiah;
                $data->jumlah = floor($_POST['jumlah'][$ctr] / 1);
                $data->save();
            endforeach;

            foreach ($data_redeem as $detail):
                $update_keterangan = RedeemDetail::find($detail->id);
                $update_keterangan->keterangan = $request->keterangan;
                $update_keterangan->save();
            endforeach;

            return Redirect::to('/backend/redeem-hadiah/')->with('success', "Data saved successfully")->with('mode', 'success');            
        }
    }

    public function view($id){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }
            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //data redeem hadiah
            $data_redeem = RedeemDetail::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $userinfo['reldag'])->orderBy('id','ASC')->get();

            //data konversi emas
            $data_konversi = RedeemEmas::with('campaign_hadiah')->where('id_campaign', $data_header[0]->id)->where('kode_customer', $userinfo['reldag'])->orderBy('id','ASC')->get();

            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            view()->share('data_redeem', $data_redeem);
            view()->share('data_konversi', $data_konversi);

            return view ('backend.redeem.view');
        }
    }

    public function edit_klaim_hadiah($id){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah di konversi atau belum
            $jum_konversi = RedeemEmas::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_konversi > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek sudah di redeem atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem == 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek tanggal klaim
            if (date('Y-m-d') < date('Y-m-d',strtotime($data_omzet[0]->periode_awal))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kedaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //data list hadiah
            $data_list_hadiah_emas =  CampaignDHadiah::select('id','nama_hadiah','harga','satuan')
                                    ->where('id_campaign', $data_header[0]->id)
                                    ->where('pilihan',0)->orderBy('id','ASC')->get();
            
            $data_list_hadiah_pilihan = CampaignDBagi::select('campaign_d_hadiah.id','campaign_d_hadiah.nama_hadiah','campaign_d_hadiah.harga','campaign_d_hadiah.satuan')
                                        ->leftJoin('campaign_d_hadiah','campaign_d_bagi.id_campaign_d_hadiah','=','campaign_d_hadiah.id')
                                        ->where('campaign_d_bagi.id_campaign',$data_header[0]->id)
                                        ->where('campaign_d_bagi.kode_agen',$userinfo['reldag'])->orderBy('campaign_d_hadiah.id','ASC')->get();

            $data_list_hadiah = $data_list_hadiah_emas->merge($data_list_hadiah_pilihan);

            //ambil harga terendah
            $harga_terendah = 999999999;
            foreach ($data_list_hadiah as $hadiah ):
                if ($hadiah['harga'] < $harga_terendah){
                    $harga_terendah = $hadiah['harga'];
                }
            endforeach;

            //data redeem hadiah
            $data_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->get();

            view()->share('harga_terendah', $harga_terendah);
            view()->share('data_list_hadiah', $data_list_hadiah);
            view()->share('data_omzet', $data_omzet);
            view()->share('data_header', $data_header);
            view()->share('data_redeem', $data_redeem);
            return view ('backend.redeem.klaim_hadiah');
        }
    }

    public function edit_klaim_hadiah_update($id, Request $request){
        $data_omzet = CustomerOmzet::where('id', $id)->where('active',1)->get();
        if (count($data_omzet)){
            //cek data sendiri atau bukan
            $userinfo = Session::get('userinfo');
            if ($data_omzet[0]->kode_customer != $userinfo['reldag']){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            $data_header = CampaignH::where('kode_campaign', '=', $data_omzet[0]->kode_campaign)->get();

            //cek sudah di konversi atau belum
            $jum_konversi = RedeemEmas::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_konversi > 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek sudah di redeem atau belum
            $jum_redeem = RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->count();
            if ($jum_redeem == 0){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek tanggal klaim
            if (date('Y-m-d') < date('Y-m-d',strtotime($data_omzet[0]->periode_awal))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //cek kedaluarsa
            if (date('Y-m-d') > date('Y-m-d',strtotime($data_omzet[0]->periode_akhir))){
                return Redirect::to('/backend/redeem-hadiah/');
            }

            //data list hadiah
            $data_list_hadiah_emas =  CampaignDHadiah::select('id','nama_hadiah','harga')
                                    ->where('id_campaign', $data_header[0]->id)
                                    ->where('pilihan',0)->orderBy('id','ASC');
            $data_list_hadiah_pilihan = CampaignDBagi::select('campaign_d_hadiah.id','campaign_d_hadiah.nama_hadiah','campaign_d_hadiah.harga')
                                        ->leftJoin('campaign_d_hadiah','campaign_d_bagi.id_campaign_d_hadiah','=','campaign_d_hadiah.id')
                                        ->where('campaign_d_bagi.id_campaign',$data_header[0]->id)
                                        ->where('campaign_d_bagi.kode_agen',$userinfo['reldag'])->orderBy('campaign_d_hadiah.id','ASC');

            $data_list_hadiah = $data_list_hadiah_emas->union($data_list_hadiah_pilihan)->get();

            //ambil harga terendah
            $harga_terendah = 999999999;
            foreach ($data_list_hadiah as $hadiah ):
                if ($hadiah['harga'] < $harga_terendah){
                    $harga_terendah = $hadiah['harga'];
                }
            endforeach;

            //cek total
            $total = 0;
            $subtotal = 0;
            if ($data_header[0]->jenis == "omzet"){
                $total = $data_omzet[0]->omzet_netto;
            }
            if ($data_header[0]->jenis == "poin"){
                $total = $data_omzet[0]->poin;
            }
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $subtotal = $subtotal + ( floor($_POST['jumlah'][$ctr] / 1) * $_POST['harga'][$ctr]);
            endforeach;

            $sisa = $total - $subtotal;
            if ($sisa < 0){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/klaim-hadiah')->with('success', "Penukaran hadiah melebihi omzet / poin")->with('mode', 'danger');
            }
            if ($sisa >= $harga_terendah){
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/klaim-hadiah')->with('success', "Sisa omzet / poin masih bisa ditukarkan dengan hadiah lain")->with('mode', 'danger');
            }

            //insert data
            $ada_emas = 0;
            RedeemDetail::where('kode_customer',$userinfo['reldag'])->where('id_campaign',$data_header[0]->id)->delete();
            foreach ($_POST['id'] as $ctr=>$id_hadiah):
                $data = new RedeemDetail();
                $data->kode_customer = $data_omzet[0]->kode_customer;
                $data->id_campaign = $data_header[0]->id;
                $data->id_campaign_hadiah = $id_hadiah;
                $data->jumlah = floor($_POST['jumlah'][$ctr] / 1);
                //cek ada redeem emas atau ga
                $cek_emas = CampaignDHadiah::where('id', $id_hadiah)->get();
                if ($cek_emas){
                    if (($data->jumlah > 0) && ($cek_emas[0]->emas == 1)) {
                        $ada_emas = 1;
                    }
                }
                $data->save();
            endforeach;

            if ($ada_emas == 0){
                //simpan redeem emas menjadi 0 agar jadi status sudah klaim
                $data_konversi = CampaignDEmas::where('id_campaign',$data_header[0]->id)->orderBy('id','ASC')->get();
                if ($data_konversi){
                    foreach ($data_konversi as $detail):
                        $data = new RedeemEmas();
                        $data->kode_customer = $data_omzet[0]->kode_customer;
                        $data->id_campaign = $data_header[0]->id;
                        $data->id_campaign_emas = $detail->id;
                        $data->jumlah = 0;
                        $data->save();
                    endforeach;
                }
                return Redirect::to('/backend/redeem-hadiah/')->with('success', "Data saved successfully")->with('mode', 'success');
            } else {
                return Redirect::to('/backend/redeem-hadiah/'.$id.'/konversi-emas');
            }
        }
    }

}
