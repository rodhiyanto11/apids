<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    //
    protected $table = 'analytics';
    protected $fillable = [
        'id','analytic_ref','analytics_url','analytics_icon'
    ];
}
