<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class InvoiceValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'name'	=>'	string',
	],
        ValidatorInterface::RULE_UPDATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'name'	=>'	string',
	],
   ];
}
