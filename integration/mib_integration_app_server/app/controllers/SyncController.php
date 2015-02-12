<?php

class SyncController extends BaseController{
	public function servertest(){
		$s = array('class' => 'classroom', 'status' => "OK");

		return Response::json($s);
	}

	public function po_sync(){
		$response = array("synced" => NULL, "id" => Input::get('id'), "message" => NULL);
		
		if(is_null(PO::find(Input::get('id')))){
			try{
				$purchase = new PO;
				$purchase->id = Input::get('id');
				$purchase->company_id = Input::get('company_id');
				$purchase->po_number = Input::get('po_number');
				$purchase->supplier_id = Input::get('supplier_id');
				$purchase->po_date = Input::get('po_date');
				$purchase->po_total_amount = Input::get('po_total_amount');
				$purchase->po_paymentterms = Input::get('po_paymentterms');
				$purchase->po_downpayment = Input::get('po_downpayment');
				$purchase->requestor = Input::get('requestor');
				$purchase->requestor_dept_det = Input::get('requestor_dept_det');
				$purchase->po_fullyreceived = Input::get('po_fullyreceived');
				$purchase->po_status = Input::get('po_status');
				$purchase->created_by = Input::get('created_by');
				$purchase->approved_by = Input::get('approved_by');
				$purchase->po_remarks = Input::get('po_remarks');
				$purchase->cancelled = Input::get('cancelled');
				$purchase->apvbedit = Input::get('apvbedit');
				$purchase->apvcredit = Input::get('apvcredit');   

				$purchase->save();

				$response['synced'] = true;
				$response['message'] = "Record Synced"; 
			}catch(Exception $e){
				$response['synced'] = false;
				$response['message'] = "lalalala";//$e->getMessage();//"Unable to sync record, please contact your system administrator";
			}
		}
		else{
			$response['synced'] = true;
			$response['message'] = "Record Already Synced";
		}
		
		return Response::json($response);
	}

	public function bp_sync(){
		$response = array('synced' => NULL, 'id' => Input::get('supplier_name'), 'message' => NULL);

		if(is_null(BP::find(Input::get('id')))){
			try{
				$supplier = new BP;
				$supplier->id =  Input::get('id');
				$supplier->company_id = Input::get('company_id');
				$supplier->supplier_name = Input::get('supplier_name');
				$supplier->address = Input::get('address');
				$supplier->payment_term = Input::get('payment_term');
				$supplier->contact_person = Input::get('contact_person');
				$supplier->contact_number = Input::get('contact_number');
				$supplier->contact_email = Input::get('contact_email');

				$supplier->save();

				$response['synced'] = true;
				$response['message'] = "Record Synced";
			}catch(Exception $e){
				$response['synced'] = false;
				$response['message'] ="error";// $e->getMessage();//"Unable to sync record, please contact your system administrator";
			}
		}
		else{
			$response['synced'] = true;
			$response['message'] = "Record Already Synced";
		}
		
		
		return Response::json($response);
	}

	public function ps_sync(){
		$response = array('synced' => NULL, 'id' => Input::get('ps_entry_no'), 'message' => NULL);

		if(is_null(PS::find(Input::get('ps_entry_no')))){
			try{
				$entry = new PS;
				$entry->ps_entry_no =  Input::get('ps_entry_no');
				$entry->ps_glAccount = Input::get('ps_glAccount');
				$entry->ps_amount = Input::get('ps_amount');
				$entry->ps_memo = Input::get('ps_memo');
				$entry->ps_detachment = Input::get('ps_detachment');
				$entry->company_id = Input::get('company_id');
				$entry->save();

				$response['synced'] = true;
				$response['message'] = "Record Synced";
			}catch(Exception $e){
				$response['synced'] = false;
				$response['message'] = $e->getMessage();//"Unable to sync record, please contact your system administrator";
			}
		}
		else{
			$response['synced'] = true;
			$response['message'] = "Record Already Synced";
		}
		
		
		return Response::json($response);
	}
}