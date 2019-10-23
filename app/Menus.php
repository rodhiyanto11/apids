<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    //
    protected $table = 'menus';
    protected $fillable = [
        'id',
        'menu_name',
        'menu_component',
        'menu_path',
        'menu_icon',
        'menu_target',
        'menu_status',
        'menu_parent'
    ];
}
