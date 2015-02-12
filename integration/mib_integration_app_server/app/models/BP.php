<?php

class BP extends Eloquent{
	protected $table = '_tbl_business_partner';


	public function purchase_order(){
		return $this->hasMany('PO','bp_id');
	}
}