<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    //
    protected $table = 'user_roles';
    protected $fillable = [
        'role_id','user_id','id','created_at','updated_at','status'
    ];
}
