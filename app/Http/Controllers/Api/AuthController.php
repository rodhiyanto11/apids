<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Http\Resources\User as UserCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{
   
    public function login(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
           
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);
        
           $credentials = $request->json()->all();
          // $exp = Carbon::now()->addDay(1);//add 1 minutes exp token
           $exp = Carbon::now()->addMinutes(3);//add 1 minutes exp token
           
    
        try {
            if (! $token = JWTAuth::attempt($credentials,['exp' => $exp])) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        User::where('email', $request->json('email'))->first()
            ->update(['api_token' => $token]);

        $data = new UserCollection(User::where('email', $request->json('email'))->first());
        //$token_exp = $exp->format('d-m-Y H:i:s');
        //$new_token = JWTAuth::refresh($token);
        return response()->json([
            'status' => 'Login is Successfully',
            'data' => $data,
            'token' => $token,
          //  'refresh_token' => $new_token,
           // 'token_exp' => $token_exp
        ],Response::HTTP_CREATED);
    }

    

    public function getAuthenticatedUser()
    {
            
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }
    public function refreshtoken(Request $request){
        $token = $request->bearerToken();
       // dd($token);
        
        $newToken = JWTAuth::parseToken()->refresh($token);
        //dd($newToken);
        return response()->json([
            'status' => 'Login is Successfully',
            'refresh_token' => $newToken,
        ],Response::HTTP_CREATED);
    }
}
