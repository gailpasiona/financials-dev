<?php

class PO extends Eloquent{
	protected $table = '_tbl_PO_header';

	public function supplier(){
		return $this->belongsTo('BP','supplier_id');
	}
}