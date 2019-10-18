<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menus;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        if ( $request->input('client') ) {
            return User::select('id', 'menu_name', 'menu_component')->get();
        }

        $columns = ['id','id', 'menu_name', 'menu_component','id'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index

        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $page = $request->input('page');

        $query = Menus::select('id', 'menu_name', 'menu_component')->orderBy($columns[$column], $dir);

        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('menu_name', 'like', '%' . $searchValue . '%')
                ->orWhere('menu_component', 'like', '%' . $searchValue . '%');
            });
        }

        $projects = $query->paginate($length);
        return ['data' => $projects, 'draw' => $request->input('draw')];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
