<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\UserRoles;
use App\User;
use App\Roles;
use Illuminate\Support\Facades\Validator;
class UserRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

        //
    public function index(Request $request)
    {
        $columns = ['id','id','name', 'role_name','id'];

        $length = $request->input('length');
        $column = $request->input('column'); //Index

        $dir = $request->input('dir');
        $searchValue = $request->input('search');
        $id = $request->input('id');
        $page = $request->input('page');

        if ($searchValue) {
            $query =
            DB::table('user_roles')
            ->join('users','users.id','user_roles.user_id')
            ->join('roles','roles.id','user_roles.role_id')
            ->where('user_roles.user_id','=',$id)
            ->orWhere('roles.role_name', 'ilike', '%' . $searchValue . '%')
            ->orWhere('users.name', 'ilike', '%' . $searchValue . '%')
            ->select('user_roles.user_id','user_roles.role_id','users.name','roles.role_name','user_roles.id')
            ->orderBy($columns[$column], $dir);
        }else{
            $query = DB::table('user_roles')
            ->join('users','users.id','user_roles.user_id')
            ->join('roles','roles.id','user_roles.role_id')
            ->where('user_roles.user_id','=',$id)
            ->select('user_roles.user_id','user_roles.role_id','users.name','roles.role_name','user_roles.id')
            ->orderBy($columns[$column], $dir);
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
            'user_id'         => 'required|numeric|min:1|',
            'role_id'         => 'required|numeric|min:1|',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $update =User::where('id', $request->json('user_id'))
        ->first();
        if(strlen($update->role_default) == 0){
            $updatedata = 
            User::where('id', $request->json('user_id'))
            //dd($updatedata);
            ->update(['role_default' => $request->json('role_id')]);
        }
        //->update(['role_default' => $request->json('role_id')]);
        $menu = UserRoles::create([
            'user_id'         => $request->json('user_id'),
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
        $role = UserRoles::findOrFail($id)->delete();
         return response()->json([
         'data' => $role,
         ],Response::HTTP_OK);
    }
    public function edit($id){
        $notin = DB::table('user_roles')
        ->where('user_id',$id)
        ->select('role_id')->get()->toArray();
       // dd($notin);
        if(count($notin) > 0){
            foreach($notin as $row){
                $ar[] = $row->role_id;
            }
        }else{
            $ar = [];
        }

        $data = Roles::where('role_status','1')->whereNotIn('id',$ar)->get();
        return response()->json(
            [
                'status' => 'Success',
                'data'  => $data,
            ],Response::HTTP_OK
        );
    }
}
