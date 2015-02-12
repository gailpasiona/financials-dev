<?php

class POController extends BaseController{

	public function show(){
		return View::make('templates.mainpage');
	}
	public function records(){
		$records = PO::where('sync','=','0')->with(array('supplier' => function($query){
			$query->select('supplier_name','id');
		}))->get();
		return Response::json($records);
	}

	public function server_sync(){
		$id = Input::get('po_id');
		$post_data = PO::find($id)->toArray();
		$url = array('server'=>'http://localhost/','uri'=>'/mib_integration_server/sync_po');
		$request = json_decode(HttpRequest::post($url,$post_data));
		if($request->synced){
			$supplier = PO::find($id);
			$supplier->sync = '1';
			$supplier->save();
		}
		return 1;
	}
}