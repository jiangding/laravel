<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class StockValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'userid'	=>'	integer|exists:users,id',
		'productid'	=>'	integer',
		'quantity'	=>'	numeric',
		'cycle'	=>'	numeric',
		'last'	=>'	numeric',
		'lastday'	=>'	numeric',
		'status'	=>'	integer',
	],
        ValidatorInterface::RULE_UPDATE => [
            'userid'	=>'	integer|exists:users,id',
            'productid'	=>'	integer',
            'quantity'	=>'	numeric',
            'cycle'	=>'	numeric',
            'last'	=>'	numeric',
            'lastday'	=>'	numeric',
            'status'	=>'	integer',
	],
   ];
}
