<?php

namespace App\Http\Controllers\Backend;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\CampaignH;
use App\Model\CampaignDHadiah;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;
use Image;

class CampaignController extends Controller
{
    public function index()
    {
        //
		return view ('backend.campaign.index');
    }

    public function create()
    {
        //
		return view ('backend.campaign.update');
    }

    public function store(Request $request)
    {
        //
        $cek = CampaignH::where('kode_campaign', trim($request->kode_campaign))->where('active','>',0)->count();
        if ($cek == 0){
            $data = new CampaignH();
            $data->kode_campaign = $request->kode_campaign;
            $data->nama_campaign = $request->nama_campaign;
            $data->jenis = $request->jenis;
            if (isset($_POST['TPP'])){
                $data->TPP = 1;
            } else {
                $data->TPP = 0;
            }
            if ($request->hasFile('brosur')) {
                $file = $request->file('brosur');
                $ext = $file->getClientOriginalExtension();
                $save_name = 'BROSUR-'.str_replace('/','',$request->kode_campaign)."-".time().".".$ext;
                $image = Image::make($file)->resize(1000, null,function ($constraint) {$constraint->aspectRatio();});
                $image->save('upload/Brosur/'.$save_name);
                $data->brosur = $save_name;
            }
            $data->active = 2;
            $data->user_modified = Session::get('userinfo')['uname'];
            if($data->save()){
                foreach ($_POST['kode_catalogue'] as $ctr=>$kode_catalogue):
                    $insert = new CampaignDHadiah;
                    $insert->id_campaign = $data->id;
                    $insert->kode_catalogue = $_POST['kode_catalogue'][$ctr];
                    $insert->kode_hadiah = $_POST['kode_hadiah'][$ctr];
                    $insert->nama_hadiah = $_POST['nama_hadiah'][$ctr];
                    $insert->jumlah = $_POST['jumlah'][$ctr];
                    $insert->harga = $_POST['harga'][$ctr];
                    $insert->pilihan = 0;
                    if (isset($_POST['pilihan'][$ctr])){
                        $insert->pilihan = 1;
                    }
                    $insert->emas = 0;
                    if (isset($_POST['emas'][$ctr])){
                        $insert->emas = 1;
                    }
                    $insert->save();
                endforeach;
                return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
            }
        } else {
            return Redirect::to('/backend/campaign/create')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }

    }

    public function show($id)
    {
        //
		$data = UserLevel::with(['user_modify'])->where('id', $id)->get();
		$userinfo = Session::get('userinfo');
		if ($data->count() > 0){
			if($userinfo['user_level_id'] > $data[0]->id){
				return redirect('/backend');
			}
			return view ('backend.userlevel.view', ['data' => $data]);
		}
    }

    public function edit($id)
    {
        //
		$data = CampaignH::where('id', $id)->where('active', '!=', 0)->get();
		if ($data->count() > 0){
            $detail = CampaignDHadiah::where('id_campaign','=',$id)->orderBy('id','ASC')->get();
            return view ('backend.campaign.update', ['data' => $data, 'detail' => $detail]);
		}
    }

    public function update(Request $request, $id)
    {
        //
        $cek = CampaignH::where('kode_campaign', trim($request->kode_campaign))->where('id','<>',$id)->where('active','>',0)->count();
        if ($cek == 0){
            $data = CampaignH::find($id);
            $data->kode_campaign = $request->kode_campaign;
            $data->nama_campaign = $request->nama_campaign;
            $data->jenis = $request->jenis;
            if (isset($_POST['TPP'])){
                $data->TPP = 1;
            } else {
                $data->TPP = 0;
            }
            if ($request->hasFile('brosur')) {
                $file = $request->file('brosur');
                $ext = $file->getClientOriginalExtension();
                $save_name = 'BROSUR-'.str_replace('/','',$request->kode_campaign)."-".time().".".$ext;
                $image = Image::make($file)->resize(1000, null,function ($constraint) {$constraint->aspectRatio();});
                $image->save('upload/Brosur/'.$save_name);
                $data->brosur = $save_name;
            }
            $data->user_modified = Session::get('userinfo')['uname'];
            if($data->save()){
                $deleteDetail = CampaignDHadiah::where('id_campaign',$id)->delete();
                foreach ($_POST['kode_catalogue'] as $ctr=>$kode_catalogue):
                    $data = new CampaignDHadiah;
                    $data->id_campaign = $id;
                    $data->kode_catalogue = $_POST['kode_catalogue'][$ctr];
                    $data->kode_hadiah = $_POST['kode_hadiah'][$ctr];
                    $data->nama_hadiah = $_POST['nama_hadiah'][$ctr];
                    $data->jumlah = $_POST['jumlah'][$ctr];
                    $data->harga = $_POST['harga'][$ctr];
                    $data->pilihan = $_POST['pilihan'][$ctr];
                    $data->emas = $_POST['emas'][$ctr];
                    $data->save();
                endforeach;
                return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
            }
        } else {
            return Redirect::to('/backend/campaign/'.$id.'/edit')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }

    }

    public function destroy(Request $request, $id)
    {
        //
		$data = UserLevel::find($id);
		$userinfo = Session::get('userinfo');
		if($userinfo['user_level_id'] <= $data->id){
			$data->active = 0;
			$data->user_modified = Session::get('userinfo')['user_id'];
			if($data->save()){
				Session::flash('success', 'Data deleted successfully');
				Session::flash('mode', 'success');
				return new JsonResponse(["status"=>true]);
			}else{
				return new JsonResponse(["status"=>false]);
			}
		}
		return new JsonResponse(["status"=>false]);		
    }
	
	public function datatable() {	
		$data = CampaignH::where('active', '!=', 0);
        return Datatables::of($data)
            ->editColumn('jenis', function($data) {
                return strtoupper($data->jenis);
            })
            ->editColumn('brosur', function($data) {
                return "<a href='".url('upload/Brosur/'.$data->brosur)."' target='_blank'>".$data->brosur."</a>";
            })
			->addColumn('action', function ($data) {
				$userinfo = Session::get('userinfo');
				$url_edit = url('backend/campaign/'.$data->id.'/edit');
				$url = url('backend/campaign/'.$data->id);
				$view = "<a class='btn-action btn btn-primary btn-view' href='".$url."' title='View'><i class='fa fa-eye'></i></a>";
				$edit = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit."' title='Edit'><i class='fa fa-edit'></i></a>";
				$delete = "<button data-url='".$url."' onclick='deleteData(this)' class='btn-action btn btn-danger btn-delete' title='Delete'><i class='fa fa-trash-o'></i></button>";
				return $view." ".$edit." ".$delete;

            })			
            ->rawColumns(['action','brosur'])
            ->make(true);
	}
 	
}
