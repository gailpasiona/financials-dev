<?php

class PSController extends BaseController{

	public function show(){
		return View::make('templates.mainpage_ps');
	}
	public function records(){
		$records = PS::where('sync','=','0')->get();
		return Response::json($records);
	}

	public function server_sync(){
		$id = Input::get('entry_no');
		$post_data = PS::find($id)->toArray();
		$url = array('server'=>'http://localhost/','uri'=>'/mib_integration_server/sync_ps');
		$request = json_decode(HttpRequest::post($url,$post_data));
		if($request->synced){
			$supplier = PS::find($id);
			$supplier->sync = '1';
			$supplier->save();
		}
		return 1;
	}
}