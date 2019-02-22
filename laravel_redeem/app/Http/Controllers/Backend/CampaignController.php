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
use App\Model\UserAvex;
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
        $mode = "insert";
        view()->share('mode_c', $mode);
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
            $data->TPP = $request->TPP;
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
                    $insert->satuan = $_POST['satuan'][$ctr];
                    $insert->pilihan = $_POST['pilihan'][$ctr];
                    $insert->emas = $_POST['emas'][$ctr];
                    $insert->user_modified = Session::get('userinfo')['uname'];
                    $insert->save();
                endforeach;
                return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
            }
        } else {
            return Redirect::to('/backend/campaign/create')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }

    }

    public function edit($id)
    {
        //
        $data = CampaignH::where('id', $id)->where('active', '!=', 0)->get();
        $mode = "edit_header";
        view()->share('mode_c', $mode);
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
            $data->TPP = $request->TPP;
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
                return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
            }
        } else {
            return Redirect::to('/backend/campaign/'.$id.'/edit')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }

    }

    public function edit_list_hadiah($id)
    {
        //
        $data = CampaignH::where('id', $id)->where('active', '!=', 0)->get();
        $mode = "edit_list_hadiah";
        view()->share('mode_c', $mode);
		if ($data->count() > 0){
            $detail = CampaignDHadiah::where('id_campaign','=',$id)->orderBy('id','ASC')->get();
            return view ('backend.campaign.update', ['data' => $data, 'detail' => $detail]);
		}
    }

    public function update_list_hadiah(Request $request, $id)
    {
        //
        $cek = CampaignH::where('kode_campaign', trim($request->kode_campaign))->where('id','<>',$id)->where('active','>',0)->count();
        if ($cek == 0){
            //hapus data tabel detail hadiah
            $deleteDetail = CampaignDHadiah::where('id_campaign',$id)->delete();

            //hapus data tabel pembagian hadiah
            $deletePembagianHadiah = CampaignDBagi::where('id_campaign',$id)->delete();

            //hapus data tabel master emas
            $deleteMasterEmas = CampaignDEmas::where('id_campaign',$id)->delete();

            //update status campaign header jadi 2 = non active mode awal
            $dataH = CampaignH::find($id);
            $dataH->active = 2;
            $dataH->user_modified = Session::get('userinfo')['uname'];
            $dataH->save();

            foreach ($_POST['kode_catalogue'] as $ctr=>$kode_catalogue):
                $data = new CampaignDHadiah;
                $data->id_campaign = $id;
                $data->kode_catalogue = $_POST['kode_catalogue'][$ctr];
                $data->kode_hadiah = $_POST['kode_hadiah'][$ctr];
                $data->nama_hadiah = $_POST['nama_hadiah'][$ctr];
                $data->jumlah = $_POST['jumlah'][$ctr];
                $data->harga = $_POST['harga'][$ctr];
                $data->satuan = $_POST['satuan'][$ctr];
                $data->pilihan = $_POST['pilihan'][$ctr];
                $data->emas = $_POST['emas'][$ctr];
                $data->user_modified = Session::get('userinfo')['uname'];
                $data->save();
            endforeach;
            return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
        } else {
            return Redirect::to('/backend/campaign/'.$id.'/edit')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }
    }

    public function destroy(Request $request, $id)
    {
        //
		$data = CampaignH::find($id);
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
                $url_edit_header = url('backend/campaign/'.$data->id.'/edit');
                $url_edit_list_hadiah = url('backend/campaign/'.$data->id.'/edit-list-hadiah');
                $url_edit_pembagian_hadiah = url('backend/campaign/'.$data->id.'/edit-pembagian-hadiah');
                $url_edit_master_emas = url('backend/campaign/'.$data->id.'/edit-master-emas');
				$url = url('backend/campaign/'.$data->id);
                $view = "<a class='btn-action btn btn-primary' href='".$url."' title='View'><i class='fa fa-eye'></i> View</a>";
                $edit_header = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit_header."' title='Edit Header'><i class='fa fa-edit'></i> Edit Header</a>";
                $edit_list_hadiah = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit_list_hadiah."' title='Edit List Hadiah'><i class='fa fa-edit'></i> Edit List Hadiah</a>";
                
                $edit_pembagian_hadiah = "";
                $count = CampaignDHadiah::where('id_campaign', $data->id)->where('pilihan',1)->count();
                // jika pilihan lebih dari 0 munculkan button edit pembagian hadiah
                if ($count > 0){
                    $edit_pembagian_hadiah = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit_pembagian_hadiah."' title='Edit Pembagian Hadiah'><i class='fa fa-edit'></i> Edit Pembagian Hadiah</a>";
                }
                $edit_master_emas = "";
                //cek jika status campaign = 3 atau jumlah pilihan nya 0 (alias tidak perlu edit pembagian hadiah) atau status = 1
                if (($count == 0) || ($data->active == 3) || ($data->active == 1)){
                    $edit_master_emas = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit_master_emas."' title='Edit Master Emas'><i class='fa fa-edit'></i> Edit Master Emas</a>";
                }
                $delete = "<button data-url='".$url."' onclick='deleteData(this)' class='btn-action btn btn-danger btn-delete' title='Delete'><i class='fa fa-trash-o'></i> Delete</button>";
                
                $jum_redeem = RedeemDetail::where('id_campaign', $data->id)->count();
                if ($jum_redeem > 0){
                    $edit_header = "";
                    $edit_list_hadiah = "";
                    $edit_pembagian_hadiah = "";
                    $edit_master_emas = "";
                    $delete = "";
                }

				return $view." ".$edit_header." ".$edit_list_hadiah." ".$edit_pembagian_hadiah." ".$edit_master_emas." ".$delete;
            })			
            ->rawColumns(['action','brosur'])
            ->make(true);
	}

    public function edit_pembagian_hadiah($id)
    {
        //data campaign header
        $data_header = CampaignH::where('id', $id)->where('active', '!=', 0)->get();
        //data hadiah pilihan
        $list_hadiah_pilihan = CampaignDHadiah::where('id_campaign', $id)->where('pilihan', 1)->orderBy('id','ASC')->get();
        // data campaign bagi hadiah soale ini create dan update sekalian
        $data = CampaignDBagi::with(['agen'])->where('id_campaign', $id)->orderBy('id','ASC')->get();
        //list agen dipake jika mode create
        $list_agen = UserAvex::where('utrace', 1)->where('posisi','AGEN')->groupBy('reldag')->orderBy('reldag','ASC')->get();
        return view ('backend.campaign.update_bagi_hadiah', ['data' => $data, 'list_agen' => $list_agen, 'list_hadiah_pilihan' => $list_hadiah_pilihan, 'data_header' => $data_header]);
    }

    public function update_pembagian_hadiah(Request $request, $id)
    {
        $cek = CampaignH::where('kode_campaign', trim($request->kode_campaign))->where('id','<>',$id)->where('active','>',0)->count();
        if ($cek == 0){
            //hapus list bagi yang lama
            $deleteBagiHadiah = CampaignDBagi::where('id_campaign',$id)->delete();
            //ubah status header campaign
            $data_h = CampaignH::find($id);
            if ($data_h->active == 2){
                $data_h->active = 3;
                $data_h->save();
            }
            $list_agen = UserAvex::where('utrace', 1)->where('posisi','AGEN')->groupBy('reldag')->orderBy('reldag','ASC')->get();
            foreach ($list_agen as $agen):
                if (isset($_POST['hadiah_'.$agen->reldag])){
                    $insert = new CampaignDBagi();
                    $insert->id_campaign = $id;
                    $insert->kode_agen = $agen->reldag;
                    $insert->id_campaign_d_hadiah = $_POST['hadiah_'.$agen->reldag];
                    $insert->user_modified = Session::get('userinfo')['uname'];
                    $insert->save();
                }
            endforeach;
            return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
        } else {
            return Redirect::to('/backend/campaign/'.$id.'/edit')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }
    }


    public function edit_master_emas($id)
    {
        //data campaign header
        $data_header = CampaignH::where('id', $id)->where('active', '!=', 0)->get();
        //data master emas
        $data = CampaignDEmas::where('id_campaign',$id)->orderBy('id', 'ASC')->get();
        return view ('backend.campaign.update_master_emas', ['data' => $data, 'data_header' => $data_header]);
    }

    public function update_master_emas(Request $request, $id)
    {
        //
        $cek = CampaignH::where('kode_campaign', trim($request->kode_campaign))->where('id','<>',$id)->where('active','>',0)->count();
        if ($cek == 0){
            //hapus data tabel master emas
            $deleteMasterEmas = CampaignDEmas::where('id_campaign',$id)->delete();

            //update status campaign header jadi 2 = non active mode awal
            $dataH = CampaignH::find($id);
            $dataH->active = 5;
            $dataH->user_modified = Session::get('userinfo')['uname'];
            $dataH->save();

            foreach ($_POST['kode_catalogue'] as $ctr=>$kode_catalogue):
                $data = new CampaignDEmas;
                $data->id_campaign = $id;
                $data->kode_catalogue = $_POST['kode_catalogue'][$ctr];
                $data->kode_hadiah = $_POST['kode_hadiah'][$ctr];
                $data->nama_hadiah = $_POST['nama_hadiah'][$ctr];
                $data->jumlah = $_POST['jumlah'][$ctr];
                $data->harga = $_POST['harga'][$ctr];
                $data->satuan = $_POST['satuan'][$ctr];
                $data->user_modified = Session::get('userinfo')['uname'];
                $data->save();
            endforeach;
            return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
        } else {
            return Redirect::to('/backend/campaign/'.$id.'/edit')->with('success', "Kode Campaign sudah ada")->with('mode', 'danger');
        }
    }

    public function show($id)
    {
        //ambil data campaign header
        $data_header = CampaignH::where('id', $id)->where('active','!=',0)->get();
        //ambil data list hadiah
        $data_list_hadiah = CampaignDHadiah::where('id_campaign',$id)->orderBy('id','ASC')->get();
        //ambil data pembagian hadiah
        $data_pembagian_hadiah = CampaignDBagi::with('agen')->where('id_campaign', $id)->orderBy('id','ASC')->get();
        //ambil data master emas
        $data_master_emas = CampaignDEmas::where('id_campaign', $id)->orderBy('id','ASC')->get();
        //data hadiah pilihan
        $list_hadiah_pilihan = CampaignDHadiah::where('id_campaign', $id)->where('pilihan', 1)->orderBy('id','ASC')->get();

        view()->share('data_header', $data_header);
        view()->share('data_list_hadiah', $data_list_hadiah);
        view()->share('data_pembagian_hadiah', $data_pembagian_hadiah);
        view()->share('data_master_emas', $data_master_emas);
        view()->share('list_hadiah_pilihan', $list_hadiah_pilihan);

    	return view ('backend.campaign.view');
    }
    
    public function view_approval($id){
        //ubah status campaign jadi active
        $data = CampaignH::find($id);
        $data->active = 1;
        if ($data->save()){
            return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');            
        }
    }

}
