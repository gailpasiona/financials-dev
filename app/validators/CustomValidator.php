<?php //namespace Financials\Validation;//namespace App\Extension\Validation;
 
use \Illuminate\Validation\Validator;

class CustomValidator extends Validator {
 
  public function posting_amounts($attribute, $value, $parameters){
  	$post_total = 0;

	// foreach ($value as $amount) {
	// 	$post_total += $amount;
	// }


	// if(floatval($post_total) == floatval($data['account_value'])) return true;

	// else return false;
	foreach ($data as $input => $value) {
	    if (substr($input, 0, 14) == "account_amount") {
	        // ...use the $value...
	        $post_total += $value;
	    }

	   
	}

	 if($post_total == $data['account_value'])
	    	return true;
	    else return false;
  }
}