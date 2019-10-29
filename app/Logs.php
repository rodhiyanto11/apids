<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    //
    protected $table = 'logs';
    protected $fillable = array(
        'id','user_id','ip_address','latitude','longitude','country','region','menu_id','action','desc'

    );
}
