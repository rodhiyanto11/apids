<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu_Target extends Model
{
    //
    protected $table = 'menu_target';
    protected $fillable = [
        'id','menu_target_name','menu_target_status'
    ];
}
