<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class UsersValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'openid'	=>	'string|unique:users,openid,NULL,id,deleted_at,NULL',
		'unionid'	=>	'string|unique:users,unionid',
		'selfcode'	=>	'string|unique:users,selfcode',
		'name'		=>	'string',
		'nickname'	=>	'string',
		'mobile'	=>	'zh_mobile|unique:users,mobile',
		'sex'		=>	'integer|in:1,2',
		'ID_number'	=>	'string',
		'birthday'	=>	'date',
		'industry'	=>	'string',
		'email'		=>	'email',
		'country'	=>	'string',
		'province'	=>	'string',
		'city'		=>	'string',
		'avatar'	=>	'string',
		'age'		=>	'integer',
		'constellation'	=>	'integer',
		'remind'	=>	'integer|in:5,7,10,30',
	],
        ValidatorInterface::RULE_UPDATE => [
		'selfcode'	=>	'string',
		'name'		=>	'string',
		'nickname'	=>	'string',
		'mobile'	=>	'zh_mobile',
		'sex'		=>	'integer|in:1,2,3',
		'ID_number'	=>	'string',
		'birthday'	=>	'string',
		'industry'	=>	'string',
		'email'		=>	'email',
		'country'	=>	'string',
		'province'	=>	'string',
		'city'		=>	'string',
		'avatar'	=>	'string',
		'age'		=>	'integer',
		'constellation'	=>	'integer',
		'remind'	=>	'integer|in:5,7,10,30',
	],
   ];
}
