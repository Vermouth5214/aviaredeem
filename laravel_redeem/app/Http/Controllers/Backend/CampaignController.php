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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
		return view ('backend.campaign.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
		return view ('backend.campaign.update');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		$data = new CampaignH();
        $data->kode_campaign = $request->kode_campaign;
        $data->nama_campaign = $request->nama_campaign;
        $data->jenis = $request->jenis;
        if (isset($_POST['TTP'])){
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
			return Redirect::to('/backend/campaign/')->with('success', "Data saved successfully")->with('mode', 'success');
		}

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
		$data = UserLevel::where('id', $id)->where('active', '!=', 0)->get();
		$userinfo = Session::get('userinfo');
		if ($data->count() > 0){
			if($userinfo['user_level_id'] > $data[0]->id){
				return redirect('/backend');
			}
			return view ('backend.userlevel.update', ['data' => $data]);
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
		$data = UserLevel::find($id);
		$userinfo = Session::get('userinfo');
		if($userinfo['user_level_id'] > $data->id){
			return redirect('/backend');
		}
		$data->name = $request->name;
		$data->active = $request->active;
		$data->user_modified = Session::get('userinfo')['user_id'];
		if($data->save()){
			return Redirect::to('/backend/users-level/')->with('success', "Data saved successfully")->with('mode', 'success');
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserLevel  $userLevel
     * @return \Illuminate\Http\Response
     */
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
