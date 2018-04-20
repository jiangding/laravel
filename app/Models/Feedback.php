<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'userid',
        'type',
        'value',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'userid', 'id');
    }
}
