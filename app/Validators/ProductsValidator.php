<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class ProductsValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'barcode'	=>'	string|required|unique:products,barcode,NULL,id,deleted_at,NULL',
		'name'	=>'	string|required',
		'manufacturer'	=>'	string',
		'spec'	=>'	string',
		'trademark'	=>'	string',
		'goodstype'	=>'	string',
		'catid'	=>'	integer|required|exists:catalogs,id',
		'price'	=>'	integer|min:0',
		'img'	=>'	string',
		'sptmimg'	=>'	string',
		'spider'	=>'	required|json',
		'url'	=>'	required|json',
	],
        ValidatorInterface::RULE_UPDATE => [
		'name'	=>'	string|required',
		'manufacturer'	=>'	string',
		'spec'	=>'	string',
		'trademark'	=>'	string',
		'goodstype'	=>'	string',
		'catid'	=>'	integer|required|exists:catalogs,id',
		'price'	=>'	integer|min:0',
		'img'	=>'	string',
		'sptmimg'	=>'	string',
		'spider'	=>'	required|json',
		'url'	=>'	required|json',
	],
   ];
}
