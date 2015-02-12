<?php

namespace Financials\Repos;

use Financials\Entities\Genledger;

class GenLedgerRepository implements GenLedgerRepositoryInterface {
	
	public function selectAll(){
		
	}

	public function create($data){
		
		$bulk = array();

		foreach ($data['post_data'] as $line) {
			$bulk_line = array('company_id' => array_get($data, 'entity'), 'module_id' => array_get($data, 'module'), 'account_id' => $line['account'], 
				'ledger_date' => date("Y-m-d H:i:s"), 'ledger_debit' => $line['debit'], 'ledger_credit' => $line['credit'], 
				'ledger_reference' => array_get($data, 'reference'));
			array_push($bulk, $bulk_line);
		}

		array_push($bulk, array('company_id' => array_get($data, 'entity'), 'module_id' => array_get($data, 'module'), 'account_id' => array_get($data,'header_account'), 
				'ledger_date' => date("Y-m-d H:i:s"), 'ledger_debit' => array_get($data,'header_debit'), 'ledger_credit' => array_get($data,'header_credit'), 
				'ledger_reference' => array_get($data, 'reference')));

		return Genledger::insert($bulk);
	}
}