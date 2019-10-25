<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Analytics;
use Symfony\Component\HttpFoundation\Response;

class AnalyticsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      //  dd(1);
     //   api/analytics/{analytic}
        //
        if($request['analytics'] == true){
            $data = Analytics::where('analytics_status',1)
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
        //$log = JWTAuth::parseToken()->authenticate()->name;

        return Analytics::where('id',$id)->get();
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
