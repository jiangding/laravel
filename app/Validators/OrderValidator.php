<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class OrderValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'orderid'	=>'	string|required|unique:orders,orderid,NULL,id,deleted_at,NULL',
		'userid'	=>'	integer|required|exists:users,id',
		'kfid'	=>'	integer|required|exists:users,id',
		'platform'	=>'	string|in:TMALL,JD,YHD',
		'address'	=>'	string',
		'address_name'	=>'	string',
		'address_phone'	=>'	string',
		'arrival'	=>'	date',
		'invoice_type'	=>'	string|in:普通发票,电子发票',
		'invoice_name'	=>'	string',
		'invoice_content'	=>'	string',
		'product'	=>'	json',
		'total'	=>'	string',
		'payid'	=>'	integer',
		'logisticsid'	=>'	integer',
		'status'	=>'	integer',
	],
        ValidatorInterface::RULE_UPDATE => [
            'orderid'	=>'	string|required|unique:orders,orderid,NULL,id,deleted_at,NULL',
            'userid'	=>'	integer|required|exists:users,id',
            'kfid'	=>'	integer|required|exists:users,id',
            'platform'	=>'	string|in:TMALL,JD,YHD',
            'address'	=>'	string',
            'address_name'	=>'	string',
            'address_phone'	=>'	string',
            'arrival'	=>'	date',
            'invoice_type'	=>'	string|in:普通发票,电子发票',
            'invoice_name'	=>'	string',
            'invoice_content'	=>'	string',
            'product'	=>'	json',
            'total'	=>'	string',
            'payid'	=>'	integer|required|exists:pay,id',
            'logisticsid'	=>'	integer|required|exists:logistics,id',
            'status'	=>'	integer',
	],
   ];
}
