<?php


class BPController extends BaseController{

	public function show(){
		return View::make('templates.mainpage_bp');
	}

	public function records(){
		$records = BP::where('sync','=','0')->get();
		return Response::json($records);
	}

	public function server_sync(){
		$id = Input::get('bp_id');
		$post_data = BP::find($id)->toArray();
		$url = array('server'=>'http://localhost/','uri'=>'/mib_integration_server/sync_bp');
		$request = json_decode(HttpRequest::post($url,$post_data));
		if($request->synced){
			$supplier = BP::find($id);
			$supplier->sync = '1';
			$supplier->save();
		}
		return 1;//Response::json($request);
	}

}