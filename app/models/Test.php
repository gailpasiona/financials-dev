<?php

use AuraIsHere\LaravelMultiTenant\Traits\TenantScopedModelTrait;

class Test extends Eloquent
{
	use TenantScopedModelTrait;

	protected $table = 'test';

	//protected $hidden = array('pivot');
	
	public static $rules = array(
        'name' => 'required|between:4,128|unique:roles,name',
    );

}