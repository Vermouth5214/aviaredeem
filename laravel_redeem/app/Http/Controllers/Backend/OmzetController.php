<?php

namespace App\Http\Controllers\Backend;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\CustomerOmzet;
use App\Model\RedeemDetail;
use App\Model\CampaignH;
use App\Model\UserAvex;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;
use App\Imports\OmzetImport;
use Excel;

class OmzetController extends Controller
{
    public function index()
    {
        //
		return view ('backend.customer_omzet.index');
    }

    public function create()
    {
        //
		return view ('backend.customer_omzet.update');
    }

    public function store(Request $request)
    {
        //kode customer dan kode campaign sudah ada
        $cek = CustomerOmzet::where('kode_campaign', $request->kode_campaign)->where('kode_customer',$request->kode_customer)->where('active',1)->count();
        if ($cek == 0){
            $cek_campaign = CampaignH::where('kode_campaign', $request->kode_campaign)->where('active',1)->count();
            $cek_user = UserAvex::where('reldag', $request->kode_customer)->count();
            if ($cek_campaign == 0){
                return Redirect::to('/backend/master-omzet/create')->with('success', "Kode Campaign tidak ditemukan")->with('mode', 'danger');
            } else 
            if ($cek_user == 0){
                return Redirect::to('/backend/master-omzet/create')->with('success', "Kode Customer tidak ditemukan")->with('mode', 'danger');
            } else
            if (strtotime($request->periode_akhir) < strtotime($request->periode_awal)){
                //periode akhir < periode awal
                return Redirect::to('/backend/master-omzet/create')->with('success', "Periode Akhir tidak boleh lebih kecil dari Periode Awal")->with('mode', 'danger');
            } else 
            if (($request->omzet == 0) && ($request->poin == 0)){
                //jika omzet dan poin = 0
                return Redirect::to('/backend/master-omzet/create')->with('success', "Omzet dan Poin tidak boleh 0 bersamaan")->with('mode', 'danger');
            } else 
            if (($request->omzet > 0) && ($request->poin > 0)){
                //jika omzet dan poin diisi bersamaan
                return Redirect::to('/backend/master-omzet/create')->with('success', "Omzet dan Poin tidak boleh diisi bersamaan")->with('mode', 'danger');
            } else 
            if (($request->omzet < 0) || ($request->poin < 0)){
                //jika omzet lebih kecil dari 0 atau poin lebih kecil dari 0
                return Redirect::to('/backend/master-omzet/create')->with('success', "Omzet dan Poin tidak boleh minus")->with('mode', 'danger');
            } else {
                $data = new CustomerOmzet();
                $data->kode_campaign = $request->kode_campaign;
                $data->kode_customer = $request->kode_customer;
                $data->periode_awal = date('Y-m-d',strtotime($request->periode_awal));
                $data->periode_akhir = date('Y-m-d',strtotime($request->periode_akhir));
                $data->omzet_tepat_waktu = $request->omzet_tepat_waktu;
                $data->disc_pembelian = $request->disc_pembelian;
                $data->disc_penjualan = $request->disc_penjualan;
                $data->omzet_netto = $request->omzet;
                $data->poin = $request->poin;
                $data->active = 1;
                $data->user_modified = Session::get('userinfo')['uname'];
                if($data->save()){
                    return Redirect::to('/backend/master-omzet/')->with('success', "Data saved successfully")->with('mode', 'success');
                }
            }
        } else {
            return Redirect::to('/backend/master-omzet/create')->with('success', "Data Omzet untuk Customer ".$request->kode_customer." dan Campaign ".$request->kode_campaign." sudah ada")->with('mode', 'danger');
        }

    }

    public function show($id)
    {
        //
		$data = CustomerOmzet::where('id', $id)->where('active', '!=', 0)->get();
		if ($data->count() > 0){
			return view ('backend.customer_omzet.view', ['data' => $data]);
		}
    }

    public function edit($id)
    {
        //
		$data = CustomerOmzet::where('id', $id)->where('active', '!=', 0)->get();
		if ($data->count() > 0){
			return view ('backend.customer_omzet.update', ['data' => $data]);
		}
    }

    public function update(Request $request, $id)
    {
        //
        $cek = CustomerOmzet::where('kode_campaign', $request->kode_campaign)->where('kode_customer',$request->kode_customer)->where('id','<>',$id)->where('active',1)->count();
        if ($cek == 0){
            $cek_campaign = CampaignH::where('kode_campaign', $request->kode_campaign)->where('active', 1)->count();
            $cek_user = UserAvex::where('reldag', $request->kode_customer)->count();
            if ($cek_campaign == 0){
                return Redirect::to('/backend/master-omzet/create')->with('success', "Kode Campaign tidak ditemukan")->with('mode', 'danger');
            } else 
            if ($cek_user == 0){
                return Redirect::to('/backend/master-omzet/create')->with('success', "Kode Customer tidak ditemukan")->with('mode', 'danger');
            } else
            if (strtotime($request->periode_akhir) < strtotime($request->periode_awal)){
                //periode akhir < periode awal
                return Redirect::to('/backend/master-omzet/'.$id.'/edit')->with('success', "Periode Akhir tidak boleh lebih kecil dari Periode Awal")->with('mode', 'danger');
            } else 
            if (($request->omzet == 0) && ($request->poin == 0)){
                //jika omzet 0 dan poin = 0
                return Redirect::to('/backend/master-omzet/'.$id.'/edit')->with('success', "Omzet dan Poin tidak boleh 0 bersamaan")->with('mode', 'danger');
            } else 
            if (($request->omzet > 0) && ($request->poin > 0)){
                //jika omzet dan poin diisi
                return Redirect::to('/backend/master-omzet/'.$id.'/edit')->with('success', "Omzet dan Poin tidak boleh diisi bersamaan")->with('mode', 'danger');
            } else 
            if (($request->omzet < 0) || ($request->poin < 0)){
                //jika omzet lebih kecil 0 atau poin lebih kecil 0
                return Redirect::to('/backend/master-omzet/'.$id.'/edit')->with('success', "Omzet dan Poin tidak boleh minus")->with('mode', 'danger');
            } else {
                $data = CustomerOmzet::find($id);
                $data->kode_campaign = $request->kode_campaign;
                $data->kode_customer = $request->kode_customer;
                $data->periode_awal = date('Y-m-d', strtotime($request->periode_awal));
                $data->periode_akhir = date('Y-m-d', strtotime($request->periode_akhir));
                $data->omzet_tepat_waktu = $request->omzet_tepat_waktu;
                $data->disc_pembelian = $request->disc_pembelian;
                $data->disc_penjualan = $request->disc_penjualan;
                $data->omzet_netto = $request->omzet;
                $data->poin = $request->poin;
                $data->user_modified = Session::get('userinfo')['uname'];
                if($data->save()){
                    return Redirect::to('/backend/master-omzet/')->with('success', "Data saved successfully")->with('mode', 'success');
                }
            }
        } else {
            return Redirect::to('/backend/master-omzet/'.$id.'/edit')->with('success', "Data Omzet untuk Customer ".$request->kode_customer." dan Campaign ".$request->kode_campaign." sudah ada")->with('mode', 'danger');
        }
    }

    public function destroy(Request $request, $id)
    {
        //
        $data = CustomerOmzet::find($id);
        $kode_campaign = CampaignH::where('kode_campaign',$data->kode_campaign)->first();
        $check_redeem = 0;
        if ($kode_campaign){
            $check_redeem = RedeemDetail::where('kode_customer', $data->kode_customer)->where('id_campaign', $kode_campaign->id)->count();
        }
        if ($check_redeem == 0){
            $data->active = 0;
            $data->user_modified = Session::get('userinfo')['uname'];
            if($data->save()){
                Session::flash('success', 'Data deleted successfully');
                Session::flash('mode', 'success');
                return new JsonResponse(["status"=>true]);
            }else{
                return new JsonResponse(["status"=>false]);
            }
        } else {
            Session::flash('success', 'Data tidak dapat dihapus karena Customer sudah melakukan redeem');
            Session::flash('mode', 'danger');
            return new JsonResponse(["status"=>true]);
        }
		return new JsonResponse(["status"=>false]);		
    }

    public function deleteAll(Request $request)
    {
		if (!(empty($_POST['checkall'])))
		{
            $notdelete = 0;
			foreach($_POST['checkall'] as $item)
			{ 
                $data = CustomerOmzet::find($item);
                $kode_campaign = CampaignH::where('kode_campaign',$data->kode_campaign)->first();
                $check_redeem = 0;
                if ($kode_campaign){
                    $check_redeem = RedeemDetail::where('kode_customer', $data->kode_customer)->where('id_campaign', $kode_campaign->id)->count();
                }
                if ($check_redeem == 0){
                    $data->active = 0;
                    $data->user_modified = Session::get('userinfo')['uname'];
                    $data->save();
                } else {
                    $notdelete = 1;
                }
            } 
            if ($notdelete == 0){
                return Redirect::to('/backend/master-omzet/')->with('success', "Data(s) deleted successfully")->with('mode', 'success');
            } else {
                return Redirect::to('/backend/master-omzet/')->with('success', "Ada omzet yang tidak bisa di hapus")->with('mode', 'danger');
            }
		} else {
            return Redirect::to('/backend/master-omzet/');
        }
    }
	
	public function datatable() {	
		$data = CustomerOmzet::where('active', '!=', 0);
        return Datatables::of($data)
            ->editColumn('periode_awal', function($data) {
                return date('d M Y', strtotime($data->periode_awal));
            })
            ->editColumn('periode_akhir', function($data) {
                return date('d M Y', strtotime($data->periode_akhir));
            })
            ->editColumn('omzet_tepat_waktu', function($data) {
                return number_format($data->omzet_tepat_waktu,0,',','.');
            })
            ->editColumn('omzet_netto', function($data) {
                return number_format($data->omzet_netto,0,',','.');
            })
            ->editColumn('disc_pembelian', function($data) {
                return number_format($data->disc_pembelian,5,',','.');
            })
            ->editColumn('poin', function($data) {
                return number_format($data->poin,0,',','.');
            })
			->addColumn('action', function ($data) {
				$url_edit = url('backend/master-omzet/'.$data->id.'/edit');
				$url = url('backend/master-omzet/'.$data->id);
				$view = "<a class='btn-action btn btn-primary btn-view' href='".$url."' title='View'><i class='fa fa-eye'></i></a>";
				$edit = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit."' title='Edit'><i class='fa fa-edit'></i></a>";
                $delete = "<button type='button' data-url='".$url."' onclick='deleteData(this)' class='btn-action btn btn-danger btn-delete' title='Delete'><i class='fa fa-trash-o'></i></button>";
                
                $kode_campaign = CampaignH::where('kode_campaign',$data->kode_campaign)->first();
                $check_redeem = 0;
                if ($kode_campaign){
                    $check_redeem = RedeemDetail::where('kode_customer', $data->kode_customer)->where('id_campaign', $kode_campaign->id)->count();
                }
                if ($check_redeem > 0){
                    $edit = "";
                    $delete = "";
                }
				return $view." ".$edit." ".$delete;
            })
            ->addColumn('check', function ($data) {
                return "
                    <span class='uni'>
                        <input type='checkbox' value='".$data->id."' name='checkall[]' />
                    </span>
                ";
            })
            ->rawColumns(['action', 'check'])
            ->make(true);
	}

    public function upload()
    {
        //
		return view ('backend.customer_omzet.upload');
    }
    
    public function upload_store(Request $request){
        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');

            $import = new OmzetImport;
            Excel::import($import, $file);
            $error = $import->getError();

            return Redirect::to('/backend/master-omzet/')->with('success', "Data saved successfully")->with('mode', 'success')->with('error', $error);
        } else {
            return Redirect::to('/backend/master-omzet/')->with('success', "File not found")->with('mode', 'danger');
        }            
    }
}
