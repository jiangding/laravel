<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class LogisticsValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'platform'	=>'	string',
		'platformid'	=>'	string',
		'platform_time'	=>'	date',
		'detail'	=>'	string',
	],
        ValidatorInterface::RULE_UPDATE => [
            'platform'	=>'	string',
            'platformid'	=>'	string',
            'platform_time'	=>'	date',
            'detail'	=>'	string',
	],
   ];
}
