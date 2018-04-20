<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    protected $fillable = [
        'appid',
        'mch_id',
        'prepay_id',
        'device_info',
        'sign',
        'sign_type',
        'result_code',
        'err_code',
        'err_code_des',
        'openid',
        'is_subscribe',
        'trade_type',
        'bank_type',
        'total_fee',
        'settlement_total_fee',
        'fee_type',
        'cash_fee',
        'cash_fee_type',
        'coupon_fee',
        'coupon_count',
        'transaction_id',
        'out_trade_no',
        'time_end',
        'custom_type',
        'status',
    ];
}
