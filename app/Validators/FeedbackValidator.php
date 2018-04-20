<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class FeedbackValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'type'	=>'	string|required',
		'value'	=>'	string|required',
	],
        ValidatorInterface::RULE_UPDATE => [
		'userid'	=>'	integer|required|exists:users,id',
		'type'	=>'	string|required',
		'value'	=>'	string|required',
	],
   ];
}
