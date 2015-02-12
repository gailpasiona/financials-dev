<?php

class SyncController extends BaseController{
	public function servertest(){
		$s = array('class' => Input::get('bp_contact_person'), 'status' => "OK");

		return Response::json($s);
	}

	public function sync_po(){

	}

	public function sync_bp(){
		
	}
}