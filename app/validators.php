<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Validator::extend('alpha_spaces', function($attribute, $value)
{
    return preg_match('/^[\pL\s0-9]+$/u', $value);
});

Validator::extend('alpha_spaces_letteronly', function($attribute, $value)
{
    return preg_match('/^[\pL\s]+$/u', $value);
});

validator::extend('amount', function($attribute, $value)
{
    return preg_match('/^([1-9][0-9]*|0)(\.[0-9]{2})?$/', $value);
});

validator::extend('posting_amounts', function($attribute, $value)
{
	// if ($this->data['a']+$this->data['b']+$this->data['c']+$this->data['d']+$this->data['e']!==18){
	//  return false;
	// }
	// $post_total = 0;

	// foreach ($data['account_amount'] as $amount) {
	// 	$post_total += $amount;
	// }

	// if($value == $post_total) return true;

	// else return false;

});

