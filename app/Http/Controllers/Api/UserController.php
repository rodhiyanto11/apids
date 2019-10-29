<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use App\User;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Image;
class UserController extends Controller
{
    public function index(Request $request){
        if($request->profile == true){
            $auth           = JWTAuth::parseToken()->authenticate();
            $id             = $auth->id;
            $user = DB::table('users')
            ->leftjoin('divisions','divisions.id','users.divisions_id')
            ->join('companies','companies.id','users.companies_id')
            ->leftjoin('departments','departments.id','users.departments_id')
            ->where('users.id',$id)
            ->select('users.name','users.role_default','users.username','users.email','users.photo','companies.companies_name as company','divisions.divisions_name as division','departments.departments_name  as department','users.phone')
            ->get();
            // dd($user);
            return response()->json(
                [
                    'status' => 'Success',
                    'data'  => $user,
                ],Response::HTTP_OK);
        }else{
            if ( $request->input('client') ) {
                return User::select('id', 'name', 'email')->get();
            }

            $columns = ['id','id', 'name', 'email','id'];

            $length = $request->input('length');
            $column = $request->input('column'); //Index

            $dir = $request->input('dir');
            $searchValue = $request->input('search');
            $page = $request->input('page');

            $query = User::select('id', 'name', 'email');
           // dd($query->get());
            if ($searchValue) {
                $query->where(function($query) use ($searchValue) {
                    $query->where('name', 'ilike', '%' . $searchValue . '%')
                    ->orWhere('email', 'ilike', '%' . $searchValue . '%');
                });
            }

            $projects = $query->paginate($length);
            //dd($projects);
            return ['data' => $projects, 'draw' => $request->input('draw')];
        }

    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'name'  => 'required|string|max:255|min:2|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|min:6|numeric|unique:users',
            'expired_status' => 'required',
            'username' => 'required|string|max:255|min:2|unique:users',
            'companies_id' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->json('name'),
            'email' => $request->json('email'),
            'password' => Hash::make('admedika'),
            'username' => $request->json('username'),
            'companies_id' => $request->json('companies_id'),
            'divisions_id' => $request->json('divisions_id'),
            'departments_id' => $request->json('departments_id'),
            'status' => 1,
            'expired_status' => $request->json('expired_status'),
            'expired_date' => $request->json('expired_date'),
            'phone' => $request->json('phone'),
        ]);
        $exp = Carbon::now()->addDay(1);//add 1 minutes exp token
        $token = JWTAuth::fromUser($user,['exp' => $exp]);
        $token_exp = $exp->format('d-m-Y H:i:s');
        return response()->json([
            'data' => $user,
            'token' => $token,
            'token_exp' => $token_exp
        ],Response::HTTP_CREATED);
    }
    public function show($id)
    {

        $data = User::where('id',$id)->first();
        return response()->json([
            'status' => 'success',
            'data' => $data,

        ],Response::HTTP_OK
        );

    }
    public function CheckURL($foo){
        return preg_match(' %^((https?://)|(www\.))([a-z0-9-].?)+(:[0-9]+)?(/.*)?$%i',$foo);
    }
    public function update(Request $request, $id)
    {
       //dd($request->json('params')['changerole']);

        //dd(filter_var($request->photo,FILTER_VALIADTE_URL) ? 'true' : 'false');
       // $stt = filter_var($request->photo,FILTER_VALIADTE_DOMAIN) ? 'true' : 'false';



        if($request->password || $request->photo){
           if(!$this->CheckURL($request->photo)){
            $auth           = JWTAuth::parseToken()->authenticate();
            $id             = $auth->id;
            $user = User::findOrFail($id);
            $name = time().'.' .explode('/', explode(':', substr($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->resize(300, 300)->save(public_path('img/').$name);
            $request->merge(['photo' => $name]);
            //dd($name);
            $userPhoto = public_path('img/').$user->photo;

            // if (file_exists($userPhoto)) {
            //     @unlink($userPhoto);
            // }
            $user->photo = $request->photo;
            $user->save();
           }
           if($request->password && strlen($request->password) > 0){
                $userdata = User::findOrFail('id',$id)->get();
                $validatepwd = strlen($request->password) > 0  ||  $userdata->status == 2 ? 'required|string|min:8|confirmed' : '';
               $validator = Validator::make($request->all(), [
                    'password' => $validatepwd
                ]);
                if($validator->fails()){
                    return response()->json($validator->errors(), 400);
                }

               if(strlen($request->password) > 0  && $userdata->status == 2){
                    $update = User::findOrFail($request->id);
                    $update->status = 1;
                    $update->password = bcrypt::make($request->password);
                    $update->save();
                }elseif(strlen($request->password) > 0 && $userdata->status == 1){
                    $update->password = bcrypt::make($request->password);
                    $update->save();
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $update,
                ],Response::HTTP_OK);
           }
        }elseif($request->json('params')['changerole'] && $request->json('params')['role_id']){

            $auth           = JWTAuth::parseToken()->authenticate();
            $id             = $auth->id;
            $user = User::where('id', $id)->firstOrFail();
            $user->role_default = $request->json('params')['role_id'];
            $user->save();
            return response()->json([
                'status' => 'success',
                'data' => $user,
            ],Response::HTTP_OK);
        }else{
            $validator = Validator::make($request->json()->all(), [
                'name'  => 'required|string|max:255|min:2',
                'email' => 'required|string|email|max:255|',
                'phone' => 'required|min:6|numeric',
                'expired_status' => 'required',
                'username' => 'required|string|max:255|min:2',
                'companies_id' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors(), 400);
            }
            $data = User::where('id', $id)->firstOrFail();
                    $data->phone          =  $request->phone;
                    $data->expired_status =  $request->expired_status;
                    $data->companies_id   =  $request->companies_id;
                    if($request->expired_date){
                        $data->expired_date =  $request->expired_date;
                    }
                    if($request->divisions_id){
                        $data->divisions_id =  $request->divisions_id;
                    }
                    if($request->departments_id){
                        $data->departments_id =  $request->departments_id;
                    }
                    $data->save();

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ],Response::HTTP_OK);
        }

    }
    public function destroy($id)
    {
        //dd($id);
        $data = User::findOrFail($id);
        $data = $data->delete();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ],Response::HTTP_OK);

    }
}
