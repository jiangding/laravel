<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class AddressValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'area'	=>'	string',
		'areaid'	=>'	string',
		'name'	=>'	string',
		'phone'	=>'	string',
		'zip'	=>'	string',
		'address'	=>'	string',
		'label'	=>'	string',
		'default'	=>'	integer|boolean',
	],
        ValidatorInterface::RULE_UPDATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'area'	=>'	string',
		'areaid'	=>'	string',
		'name'	=>'	string',
		'phone'	=>'	string',
		'zip'	=>'	string',
		'address'	=>'	string',
		'label'	=>'	string',
		'default'	=>'	integer|boolean',
	],
   ];
}
