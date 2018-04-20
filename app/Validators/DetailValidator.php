<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class DetailValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'platform'	=>'	string',
		'invoice_name'	=>'	string',
	],
        ValidatorInterface::RULE_UPDATE => [
		'platform'	=>'	string',
		'invoice_name'	=>'	string',
	],
   ];
}
