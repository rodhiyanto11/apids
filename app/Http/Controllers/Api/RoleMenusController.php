<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\RoleMenus;
use App\Menus;
use App\Roles;
use App\Http\Resources\RoleMenusCollection as RoleMenusResource;
use Illuminate\Support\Facades\Validator;
class RoleMenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = ['id','id','menu_name', 'role_name','id'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index

        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $id = $request->input('id');
        $page = $request->input('page');

        if ($searchValue) {
            $query =
            DB::table('role_menus')
            ->join('menus','menus.id','role_menus.menu_id')
            ->join('roles','roles.id','role_menus.role_id')
            ->where('role_menus.role_id','=',$id)
            ->orWhere('roles.role_name', 'like', '%' . $searchValue . '%')
            ->orWhere('menus.menu_name', 'like', '%' . $searchValue . '%')
            ->select('role_menus.menu_id','role_menus.role_id','menus.menu_name','roles.role_name','role_menus.id');
           // ->orderBy($columns[$column], $dir);
        }else{
           $query =  DB::table('role_menus')
            ->join('menus','menus.id','role_menus.menu_id')
            ->join('roles','roles.id','role_menus.role_id')
            ->where('role_menus.role_id','=',$id)
            ->select('role_menus.menu_id','role_menus.role_id','menus.menu_name','roles.role_name','role_menus.id');
           //->orderBy($columns[$column], $dir);
        }

        $projects = $query->paginate($length);
        //dd($projects);
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
        $validator = Validator::make($request->json()->all(), [
            'menu_id'         => 'required|numeric|min:1|',
            'role_id'         => 'required|numeric|min:1|',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = RoleMenus::create([
            'menu_id'         => $request->json('menu_id'),
            'role_id'         => $request->json('role_id'),
            'status'         => 1,

        ]);

        return response()->json([
            'data' => $menu,
        ],Response::HTTP_CREATED);
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
         //
       //  dd(1);
         $role = RoleMenus::findOrFail($id)->delete();
         return response()->json([
         'data' => $role,
         ],Response::HTTP_OK);
    }
    public function edit($id)
    {
        //

        $notin = DB::table('role_menus')
        ->where('role_id',$id)
        ->select('menu_id')->get()->toArray();

        if(count($notin) > 0){
            foreach($notin as $row){
                $ar[] = $row->menu_id;
            }
        }else{
            $ar = [];
        }

        $data = Menus::where('menu_status','1')->whereNotIn('id',$ar)->get();
        return response()->json(
            [
                'status' => 'Success',
                'data'  => $data,
            ],Response::HTTP_OK
        );

    }
}
