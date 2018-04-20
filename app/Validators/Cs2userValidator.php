<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class Cs2userValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'appid'	=>'	string',
		'csopenid'	=>'	string',
		'useropenid'	=>'	string',
	],
        ValidatorInterface::RULE_UPDATE => [
		'appid'	=>'	string',
		'csopenid'	=>'	string',
		'useropenid'	=>'	string',
	],
   ];
}
