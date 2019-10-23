<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Roles;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $columns = ['id', 'role_name', 'role_status','role_desc','id'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index

        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $page = $request->input('page');

        $query = Roles::select('id', 'role_name', 'role_status','role_desc')->orderBy($columns[$column], $dir);
       // dd($query->get());
        if ($searchValue) {
            $query->where(function($query) use ($searchValue) {
                $query->where('role_name', 'like', '%' . $searchValue . '%')
                ->orWhere('role_status', 'like', '%' . $searchValue . '%')
                ->orWhere('role_desc', 'like', '%' . $searchValue . '%');
            });
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
            'role_name'         => 'required|string|max:255|min:2|unique:roles',
            'role_status'       => 'required|numeric',
            'role_desc'       => 'string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = Roles::create([
            'role_name' => $request->json('role_name'),
            'role_status' => $request->json('role_status'),
            'role_desc' => $request->json('role_desc'),
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
        $data = Roles::where('id',$id)->first();
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
            'role_name'         => 'required|string|max:255|min:2|unique:roles',
            'role_status'       => 'required|numeric',
            'role_desc'       => 'string|max:100',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $role = Roles::findOrFail($id);
        $role->role_name = $request->role_name;
        $role->role_status = $request->role_status;
        $role->role_desc = $request->role_desc;
        $role->save();

        return response()->json([
            'data' => $role,
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
        $role = Roles::findOrFail($id)->delete();
        return response()->json([
        'data' => $role,
        ],Response::HTTP_OK);
    }
}
