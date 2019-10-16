<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    //
    protected $table = 'departments';
    protected $fillable = [
        'id','departments_name', 'departments_status','created_at','updated_at'
    ];
}
