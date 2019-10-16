<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    //
    protected $table = 'companies';
    protected $fillable = [
        'id','companies_name', 'companies_logo','companies_color','companies_status','created_at','updated_at'
    ];
}
