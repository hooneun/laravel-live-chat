<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function from()
    {
        return $this->belongsTo(User::class, 'form');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to');
    }
}
