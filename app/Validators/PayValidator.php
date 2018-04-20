<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class PayValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
		'appid'	=>'	string',
		'mch_id'	=>'	string',
		'device_info'	=>'	string',
		'sign'	=>'	string',
		'sign_type'	=>'	string',
		'result_code'	=>'	string',
		'err_code'	=>'	string',
		'err_code_des'	=>'	string',
		'openid'	=>'	string',
		'is_subscribe'	=>'	string',
		'trade_type'	=>'	string',
		'bank_type'	=>'	string',
		'total_fee'	=>'	string',
		'settlement_total_fee'	=>'	string',
		'fee_type'	=>'	string',
		'cash_fee'	=>'	string',
		'cash_fee_type'	=>'	string',
		'coupon_fee'	=>'	string',
		'coupon_count'	=>'	string',
		'transaction_id'	=>'	string',
		'out_trade_no'	=>'	string',
		'time_end'	=>'	string',
		'custom_type'	=>'	string',
		'status'	=>'	integer',
	],
        ValidatorInterface::RULE_UPDATE => [
            'appid'	=>'	string',
            'mch_id'	=>'	string',
            'device_info'	=>'	string',
            'sign'	=>'	string',
            'sign_type'	=>'	string',
            'result_code'	=>'	string',
            'err_code'	=>'	string',
            'err_code_des'	=>'	string',
            'openid'	=>'	string',
            'is_subscribe'	=>'	string',
            'trade_type'	=>'	string',
            'bank_type'	=>'	string',
            'total_fee'	=>'	string',
            'settlement_total_fee'	=>'	string',
            'fee_type'	=>'	string',
            'cash_fee'	=>'	string',
            'cash_fee_type'	=>'	string',
            'coupon_fee'	=>'	string',
            'coupon_count'	=>'	string',
            'transaction_id'	=>'	string',
            'out_trade_no'	=>'	string',
            'time_end'	=>'	string',
            'custom_type'	=>'	string',
            'status'	=>'	integer',
	],
   ];
}
