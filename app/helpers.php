<?php

class Helpers{

	public static function recordNumGen($record_num){
		$id = date("Y-m") . '-' . $record_num;
		return $id;
	}

	public static function cvNumGen(){

	}
}