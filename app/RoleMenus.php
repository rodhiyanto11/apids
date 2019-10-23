<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleMenus extends Model
{
    //
    protected $table = 'role_menus';
    protected $fillable = [
        'id','role_id','menu_id','status','created_at','updated_at'
    ];

}
