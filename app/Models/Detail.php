<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $fillable = [
        'uuid',
        'userid',
        'kfid',
        'commit',
        'addressid',
        'invoice_name',
        'invoice_type',
        'invoice_content',
        'platform',
        'postage',
        'product',
        'type',
        'end_at',
        'status'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'userid');
    }
}
