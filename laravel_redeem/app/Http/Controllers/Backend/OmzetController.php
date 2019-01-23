<?php

namespace App\Http\Controllers\Backend;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\CustomerOmzet;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;
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
                $data->omzet = $request->omzet;
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
                $data->omzet = $request->omzet;
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
    	$data->active = 0;
		$data->user_modified = Session::get('userinfo')['uname'];
        if($data->save()){
            Session::flash('success', 'Data deleted successfully');
            Session::flash('mode', 'success');
            return new JsonResponse(["status"=>true]);
        }else{
            return new JsonResponse(["status"=>false]);
        }
		return new JsonResponse(["status"=>false]);		
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
            ->editColumn('omzet', function($data) {
                return number_format($data->omzet,0,',','.');
            })
            ->editColumn('poin', function($data) {
                return number_format($data->poin,0,',','.');
            })
			->addColumn('action', function ($data) {
				$url_edit = url('backend/master-omzet/'.$data->id.'/edit');
				$url = url('backend/master-omzet/'.$data->id);
				$view = "<a class='btn-action btn btn-primary btn-view' href='".$url."' title='View'><i class='fa fa-eye'></i></a>";
				$edit = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit."' title='Edit'><i class='fa fa-edit'></i></a>";
				$delete = "<button data-url='".$url."' onclick='deleteData(this)' class='btn-action btn btn-danger btn-delete' title='Delete'><i class='fa fa-trash-o'></i></button>";
				return $view." ".$edit." ".$delete;
            })			
            ->rawColumns(['action'])
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
            $result = Excel::selectSheetsByIndex(0)->load($file, function ($reader) {
                $reader->noHeading();
            })->get();
            $result = $result->toArray();
            $i = 1;
            $j = 1;
            $error = array();
            foreach ($result as $row):
                $cek = CustomerOmzet::where('kode_campaign', trim($row[0]))->where('kode_customer', trim($row[1]))->where('active',1)->count();
                if ($cek > 0){
                    //kembar
                    $text = "Baris ".$i." : Data sudah ada";
                    array_push($error,$text);
                } else
                if ($row[3] < $row[2]){
                    //periode akhir < periode awal
                    $text = "Baris ".$i." : Tanggal periode akhir lebih kecil dari periode awal";
                    array_push($error,$text);
                } else 
                if (($row[4] == 0) && ($row[5] == 0)){
                    //jika omzet 0 dan poin = 0
                    $text = "Baris ".$i." : Omzet dan Poin 0";
                    array_push($error,$text);
                } else 
                if (($row[4] > 0) && ($row[5] > 0)){
                    //jika omzet dan poin diisi
                    $text = "Baris ".$i." : Omzet dan Poin > 0";
                    array_push($error,$text);
                } else 
                if (($row[4] < 0) || ($row[5] < 0)){
                    //jika omzet lebih kecil 0 atau poin lebih kecil 0
                    $text = "Baris ".$i." : Omzet atau Poin < 0";
                    array_push($error,$text);
                } else {
                    $data = new CustomerOmzet;
                    $data->kode_campaign = trim($row[0]);
                    $data->kode_customer = trim($row[1]);
                    $data->periode_awal = $row[2];
                    $data->periode_akhir = $row[3];
                    $data->omzet = $row[4] / 1;
                    $data->poin = $row[5] / 1;
                    $data->active = 1;
                    $data->user_modified = Session::get('userinfo')['uname'];
                    $data->save();
                }
                $i++;
            endforeach;

            return Redirect::to('/backend/master-omzet/')->with('success', "Data saved successfully")->with('mode', 'success')->with('error', $error);
        } else {
            return Redirect::to('/backend/master-omzet/')->with('success', "File not found")->with('mode', 'danger');
        }            
    }
}
