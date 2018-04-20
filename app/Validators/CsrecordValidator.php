<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class CsrecordValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'appid'	=>'	string',
		'csopenid'	=>'	string',
		'useropenid'	=>'	string',
		'mode'	=>  'integer|in:0,1,2,3',
		'Msgid'	=>'	string',
		'type'	=>'	string',
		'Mediaid'	=>'	string',
		'record'	=>'	string',
		'read'	=>'	integer',
		'push'	=>'	integer',
	],
        ValidatorInterface::RULE_UPDATE => [
		'appid'	=>'	string',
		'csopenid'	=>'	string',
		'useropenid'	=>'	string',
        'mode'	=>  'integer|in:0,1,2,3',
		'Msgid'	=>'	string',
		'type'	=>'	string',
		'Mediaid'	=>'	string',
		'record'	=>'	string',
		'read'	=>'	integer',
		'push'	=>'	integer',
	],
   ];
}
