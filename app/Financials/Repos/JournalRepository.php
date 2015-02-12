<?php

namespace Financials\Repos;

use Financials\Entities\Journal;

class JournalRepository implements JournalRepositoryInterface {
	
	public function selectAll(){
		
	}

	public function create($data){
		
		$bulk = array();

		foreach ($data['post_data'] as $line) {
			$bulk_line = array('company_id' => array_get($data, 'entity'), 'module_id' => array_get($data, 'module'), 'account_id' => $line['account'], 
				'journal_date' => date("Y-m-d H:i:s"), 'debit' => $line['debit'], 'credit' => $line['credit'], 
				'ref_no' => array_get($data, 'reference'));
			array_push($bulk, $bulk_line);
		}
		//header account
		array_push($bulk, array('company_id' => array_get($data, 'entity'), 'module_id' => array_get($data, 'module'), 'account_id' => $data['header_account'], 
				'journal_date' => date("Y-m-d H:i:s"), 'debit' => $data['header_debit'], 'credit' => $data['header_credit'], 
				'ref_no' => array_get($data, 'reference')));

		return Journal::insert($bulk);
	}


}