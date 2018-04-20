<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class SceneValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'catid' => 'integer|required|exists:catalogs,id',
		    'name'	=> 'string',
	],
        ValidatorInterface::RULE_UPDATE => [
            'catid' => 'integer|required|exists:catalogs,id',
		    'name'	=> 'string',
	],
   ];
}
