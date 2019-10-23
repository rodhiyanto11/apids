<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Menus;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        }else{

            if ( $request->input('client') ) {
                return Menus::select('id', 'menu_name', 'menu_component')->get();
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
            'menu_path'         => 'required|string|max:255|unique:menus',
            'menu_component'    => 'required|min:6|string|unique:menus',
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
            'menu_name'         => 'sometimes|required|string|max:255|min:2',
            'menu_path'         => 'sometimes|required|string|max:255',
            'menu_component'    => 'sometimes|required|min:6|string',
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
