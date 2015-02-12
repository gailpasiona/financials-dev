<?php

class Company extends Eloquent
{
	protected $table = 'companies';

	//protected $hidden = array('pivot');
	
	public static $rules = array(
        'name' => 'required|between:4,128|unique:companies,name',
        'alias' => 'required|between:4,128|unique:companies,alias',
    );

    public static function validate($input){
    	$validator = Validator::make($input, static::$rules);
        
        return $validator;
    }

    public function register()
    {
        return $this->hasMany('Financials\Entities\Register');
    }

}
