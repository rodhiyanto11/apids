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
        'menu_parent',
        'menu_desc',
        'tableau'
    ];
    public function parent() {

        return $this->hasOne('App\Menus', 'id', 'menu_parent');

    }

    public function children() {

        return $this->hasMany('App\Menus', 'menu_parent', 'id');

    }

    public static function tree() {
       // dd(1);
       dd(static::with(implode('.', array_fill(0, 4, 'children')))->where('menu_parent', '=', NULL)->get());
        return static::with(implode('.', array_fill(0, 4, 'children')))->where('menu_parent', '=', NULL)->get();

    }

}
