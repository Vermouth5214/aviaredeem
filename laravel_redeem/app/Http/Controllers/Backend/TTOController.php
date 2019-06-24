<?php

namespace App\Http\Controllers\Backend;

use Session;
use Illuminate\Http\Request;
use App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Model\TTOLast;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redirect;
use Datatables;


class TTOController extends Controller
{
    public function index()
    {
        //
		return view ('backend.tto.index');
    }

    public function create()
    {
        //
		return view ('backend.tto.update');
    }

    public function store(Request $request)
    {
        $data = new TTOLast();
        $data->no_tto = $request->no_tto;
        if($data->save()){
            return Redirect::to('/backend/last-tto/')->with('success', "Data saved successfully")->with('mode', 'success');
        }

    }

    public function edit($id)
    {
        //
		$data = TTOLast::where('id', $id)->get();
		if ($data->count() > 0){
			return view ('backend.tto.update', ['data' => $data]);
		}
    }

    public function update(Request $request, $id)
    {
        //
        $data = TTOLast::find($id);
        $data->no_tto = $request->no_tto;
        if($data->save()){
            return Redirect::to('/backend/last-tto/')->with('success', "Data saved successfully")->with('mode', 'success');
        }
    }

    public function destroy(Request $request, $id)
    {
        //
        TTOLast::destroy($id);
        Session::flash('success', 'Data deleted successfully');
        Session::flash('mode', 'success');
        return new JsonResponse(["status"=>true]);
    }

	public function datatable() {	
		$data = TTOLast::all();
        return Datatables::of($data)
			->addColumn('action', function ($data) {
				$url_edit = url('backend/last-tto/'.$data->id.'/edit');
				$url = url('backend/last-tto/'.$data->id);
				$view = "<a class='btn-action btn btn-primary btn-view' href='".$url."' title='View'><i class='fa fa-eye'></i></a>";
				$edit = "<a class='btn-action btn btn-info btn-edit' href='".$url_edit."' title='Edit'><i class='fa fa-edit'></i></a>";
                $delete = "<button type='button' data-url='".$url."' onclick='deleteData(this)' class='btn-action btn btn-danger btn-delete' title='Delete'><i class='fa fa-trash-o'></i></button>";
                
				return $edit." ".$delete;
            })
            ->rawColumns(['action'])
            ->make(true);
	}

}
