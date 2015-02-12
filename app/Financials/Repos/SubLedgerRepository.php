<?php

namespace Financials\Repos;

use Financials\Entities\Subledger;

class SubLedgerRepository implements SubLedgerRepositoryInterface {
	
	public function selectAll(){
		
	}

	public function create($data){
		
		$sub_line = array('company_id' =>  array_get($data, 'entity'), 'subsl_type' => array_get($data, 'vendor'),'subsl_date' => date("Y-m-d H:i:s"),
						'subsl_debit' => $data['debit'], 'subsl_credit' => $data['credit'],
						'subsl_balance' => $data['balance'], 'subsl_refno' => array_get($data, 'reference'));
		
		return Subledger::insert($sub_line);
	}
}