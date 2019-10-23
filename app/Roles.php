<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    //
    protected $table = 'roles';
    protected $fillable = [
        'id','role_name','role_status','role_desc','created_at','update_at'
    ];
}
