<?php

namespace Financials\Repos;

use Financials\Entities\Invoiceline;

class InvoiceLineRepository implements InvoiceLineRepositoryInterface {
	
	public function selectAll(){
		
	}

	public function create($data,$ref){
		
		$bulk = array();
		$company = \Company::where('alias', \Session::get('company'))->first()->id;

		foreach ($data as $line) {
			$bulk_line = array('account_id' => $line['account'], 'line_no' => $line['line'], 'description' => $line['description'], 
				'line_amount' => array_get($line, 'amount'), 'entry_type' => $line['type'], 'register_id' => $ref, 
				'company_id' => $company);

			array_push($bulk, $bulk_line);
		}

		return Invoiceline::insert($bulk);
	}

	public function fetchLines(){

	}

	public function updateLines($data, $ref){
		$update = 0;
		foreach($data as $line){
			$item = InvoiceLine::where('register_id','=',$ref)->where('line_no', '=', $line['line'])->first();

			$update =$line['line']; 

			if($item){
				$item->description = array_get($line, 'description');
				$item->line_amount = array_get($line, 'amount');
				$item->account_id = array_get($line, 'account');
				$item->entry_type = array_get($line, 'type');

				$item->save();
			}
			else{
				$company = \Company::where('alias', \Session::get('company'))->first()->id;

				$line = array('entry_type' => $line['type'] ,'account_id' => $line['account'], 'line_no' => $line['line'], 'description' => $line['description'], 
				'line_amount' => array_get($line, 'amount'), 'register_id' => $ref, 
				'company_id' => $company);

				Invoiceline::insert($line);

			}

		}
		//delete remaining lines in DB
		$items_for_delete = Invoiceline::where('register_id','=',$ref)->where('line_no', '>', $update)->delete(); 

		return 1;
	}


}