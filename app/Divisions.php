<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Divisions extends Model
{
    //
    protected $table = 'divisions';
    protected $fillable = [
        'id','divisions_name', 'created_at','update_at','divisions_status'
    ];
}
