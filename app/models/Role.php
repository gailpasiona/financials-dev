<?php
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
	protected $hidden = array('pivot');
	
	public static $rules = array(
        'name' => 'required|between:4,128|unique:roles,name',
    );

    

}