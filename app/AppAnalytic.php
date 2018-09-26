<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppAnalytic extends Model
{
    protected $fillable = [
        'uid',
        'platform',
        'version',
        'verified'
    ];
}
