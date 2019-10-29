<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menus;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Illuminate\Support\Facades\DB;
class MenusController extends Controller
{

    public function __construct()
    {
    }
    public function getstructure($role = null){
        $auth           = JWTAuth::parseToken()->authenticate();
        $role_default   = $auth->role_default;
        $id             = $auth->id;
       //dd($id);
       //$data = Menus::where('menu_status','1')->orderBy('menu_name','asc')->get();
        $data = DB::table('user_roles')
        ->join('role_menus','role_menus.role_id','user_roles.role_id')
        ->join('roles','roles.id','role_menus.role_id')
        ->join('menus','menus.id','role_menus.menu_id')
        ->join('users','users.id','user_roles.user_id')
        ->where('users.id',$id)
        ->where('menus.menu_status',1)
        ->where('user_roles.role_id',$role_default)
        ->orderBy('menu_name','asc')
        ->distinct('menus.id')
        ->select('menus.menu_component','menus.menu_parent','menus.menu_target','menus.id','menus.menu_name','menu_icon','menu_path')
        ->get();

        $parents = [];
        $i = 0;
        $j = 0;

        foreach($data as $parent){
           // dd($parent);
            if(!$parent->menu_component || $parent->menu_parent == $parent->id && $parent->menu_target !=2){
                $parents[$j]['no'] =$j;
                $parents[$j]['id'] =$parent->id;
                $parents[$j]['header'] = $parent->menu_name;
                $parents[$j]['iconname'] = $parent->menu_icon;
                $parents[$j]['index'] = str_replace(' ','',$parent->menu_name);
                $parents[$j]['link'] = $parent->menu_path;
                $j++;
            }
            $i++;
        }
     //  dd($parents);

        for($k = 0 ; $k < count($parents) ; $k++ ){
            $children =
            DB::table('user_roles')
            ->join('users','users.id','user_roles.user_id')
            ->join('roles','roles.id','users.role_default')
            ->join('role_menus','role_menus.role_id','roles.id')
            ->join('menus','menus.id','role_menus.menu_id')
            ->where('users.id',$id)
            ->where('menus.menu_status',1)
            ->where('menus.menu_parent',$parents[$k]['id'])

            ->orderBy('menus.menu_name','asc')
            ->distinct('menus.id')
            ->select('menus.menu_component','menus.menu_parent','menus.menu_target','menus.id','menus.menu_name','menu_icon','menu_path','menus.tableau','users.role_default','role_menus.role_id')
            ->get();
          //  dd($children);
            $m = 0;
            foreach($children as $childrens){

                if($childrens->menu_component && $childrens->menu_parent != $childrens->id && $childrens->role_default == $childrens->role_id){
                    //dd($parent->menu_component);
                    $parents[$k]['child'][$m]['header'] = $childrens->menu_name;
                    $parents[$k]['child'][$m]['parent'] = $childrens->menu_parent;
                    $parents[$k]['child'][$m]['id'] = $childrens->id;
                    //$parents[$k]['child'][$m]['name'] = $childrens->menu_name;
                    $parents[$k]['child'][$m]['link'] = $childrens->menu_target == 4 ?  '/app/'.$childrens->menu_path."-".$childrens->tableau : '/app/'.$childrens->menu_path ;
                    //$parents[$k]['child'][$m]['component'] = $childrens->component;
                    $m++;
                }
                $i++;
            }
            //$parents[$k]['']
        }
       $data =  $parents;
     // dd($parents);
       return $data;

    }
    public function index(Request $request){


        if($request['menu'] == true){
            $data = Menus::where('menu_status',1)

                    //->where('menu_parent','<>','id')
                    ->orderBy('id')
                    ->get();
            //$data = JWTAuth::parseToken()->authenticate();
           // dd($data);
            return response()->json(
                [
                    'status' => 'Success',
                    'data'  => $data,
                ],Response::HTTP_OK
            );
        }else if($request['sidebar'] == true){
          $data =  $this->getstructure();
           return response()->json(
            [
                'status' => 'Success',
                'data'  => $data,
            ],Response::HTTP_OK
        );
    }else if($request['navigation'] == true){

        $items = new Menus();

        dd($items->tree());
        return response()->json(
            [
                'status' => 'Success',
                'data'  => $items,
            ],Response::HTTP_OK);
        }else{

            if ( $request->input('client') ) {
                return Menus::select('id', 'menu_name', 'menu_component','tableau','menu_desc')->get();
            }

            $columns = ['id','id', 'menu_name', 'menu_component','tableau','menu_desc','id'];

            $length = $request->input('length');
            $column = $request->input('column'); //Index

            $dir = $request->input('dir');
            $searchValue = $request->input('search');
            $page = $request->input('page');

            $query = Menus::select('id', 'menu_name', 'menu_component','tableau','menu_desc')->orderBy($columns[$column], $dir);

            if ($searchValue) {
                $query->where(function($query) use ($searchValue) {
                    $query->where('menu_name', 'ilike', '%' . $searchValue . '%')
                    ->orWhere('menu_component', 'ilike', '%' . $searchValue . '%');
                });
            }

            $projects = $query->paginate($length);
            return ['data' => $projects, 'draw' => $request->input('draw')];
        }
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
            'menu_name'         => 'required|string|max:255|min:2|unique:menus',
            'menu_path'         => 'max:255',
            'menu_component'    => 'max:255',
            'menu_parent'       => 'required|numeric',
            'menu_target'       => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = Menus::create([
            'menu_name' => $request->json('menu_name'),
            'menu_path' => $request->json('menu_path'),
            'menu_status' => 1,
            'menu_component' => $request->json('menu_component'),
            'menu_parent' => $request->json('menu_parent'),
            'menu_target' => $request->json('menu_target'),
            'tableau' => $request->json('tableau'),
            'menu_desc' => $request->json('menu_desc'),
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
        $data = Menus::where('id',$id)->first();
        return response()->json([
            'status' => 'success',
            'data' => $data,

        ],Response::HTTP_OK
        );
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
        $validator = Validator::make($request->json()->all(), [
            'menu_name'         => 'required|string|max:255|min:2',
            'menu_path'         => 'max:255',
            'menu_component'    => 'max:255',
            'menu_parent'       => 'required|numeric',
            'menu_target'       => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = Menus::findOrFail($id);
        $menu->menu_name = $request->menu_name;
        $menu->menu_path = $request->menu_path;
        $menu->menu_component = $request->menu_component;
        $menu->menu_parent = $request->menu_parent;
        $menu->tableau = $request->tableau;
        $menu->menu_desc = $request->desc;
        $menu->save();

        return response()->json([
            'data' => $menu,
        ],Response::HTTP_CREATED);
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
        $menu = Menus::findOrFail($id)->delete();
       // dd($menus);
       return response()->json([
        'data' => $menu,
        ],Response::HTTP_OK);
    }
}
