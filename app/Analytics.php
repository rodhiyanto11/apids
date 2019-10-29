<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    //
    protected $table = 'analytics';
    protected $fillable = [
        'id','analytics_name','analytics_url','analytics_status'
    ];
}
