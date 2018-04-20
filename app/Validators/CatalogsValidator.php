<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class CatalogsValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'shortcode'	=>'	string|required|unique:catalogs,shortcode,NULL,id,deleted_at,NULL',
		'name'	=>'	string|required',
		'keyword'	=>'	string|required',
		'icon'	=>'	string',
	],
        ValidatorInterface::RULE_UPDATE => [
		'shortcode'	=>'	string|required',
		'name'	=>'	string|required',
		'keyword'	=>'	string|required',
		'icon'	=>'	string',
	],
   ];
}
