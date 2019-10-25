<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Companies;
use Symfony\Component\HttpFoundation\Response;
use App\Menus;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if($request['logo']){
            $cekdata = Companies::where('companies_name',$request['companies_name'])
                        ->where('companies_status',1);
            //dd($cekdata->count());
            if($cekdata->count() > 0){
                $data = $cekdata->first();
            }else{
                $data = Companies::where('companies_name','admedika')
                                ->where('companies_status',1)
                                ->first();
            }
            //dd(1);
        }elseif($request['user']){
            //dd(2);
            $data = Companies::where('companies_status',1)->get();
        }elseif($request['menu']){
                $data = Menus::
                where('menu_target','<>','4')
                ->where('menu_component','<>','')
                ->get();
            //$data = JWTAuth::parseToken()->authenticate();
        }
       //dd($data);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ],Response::HTTP_OK);
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
