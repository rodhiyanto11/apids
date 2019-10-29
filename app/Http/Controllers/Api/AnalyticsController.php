<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Analytics;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\LogsController;

class AnalyticsController extends Controller
{
    public $drive;
    public $location = [];

    public function __construct()
    {
        $this->drive = new LogsController();
        $this->location = $this->drive->getLocation();
    }
    public function index(Request $request)
    {
      // dd(1);
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
        }else{
//dd(1);

            $columns = ['id','id', 'analytics_name', 'analytics_url','id'];

            $length = $request->input('length');
            $column = $request->input('column'); //Index

            $dir = $request->input('dir');
            $searchValue = $request->input('search');
            $page = $request->input('page');

            $query = Analytics::select('id', 'analytics_name', 'analytics_url')->orderBy($columns[$column], $dir);

            if ($searchValue) {
                $query->where(function($query) use ($searchValue) {
                    $query->where('analytics_name', 'ilike', '%' . $searchValue . '%')
                    ->orWhere('analytics_url', 'ilike', '%' . $searchValue . '%');
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
            'analytics_name'         => 'required|string|max:255|min:2|unique:analytics',
            'analytics_url'          => 'required|string|max:255|min:2|unique:analytics',
            'analytics_status'       => 'required|numeric',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = Analytics::create([
            'analytics_name'         => $request->json('analytics_name'),
            'analytics_url'          => $request->json('analytics_url'),
            'analytics_status'       => $request->json('analytics_status')
        ]);

        return response()->json([
            'data' => $menu,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Analytics::where('id',$id)->get();
        $detail = Analytics::where('id',$id)->first();
        $action = array(
            'location' =>$this->location,
            'detail' => $detail
         );
        //dd($this->drive->logscontroll($action));
        return $data;
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
            'analytics_name'         => 'required|string|max:255|min:2|unique:analytics',
            'analytics_url'          => 'required|string|max:255|min:2|unique:analytics',
            'analytics_status'       => 'required|numeric',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }

        $menu = Analytics::findOrFail($id);
        $menu->analytics_name   = $request->analytics_name;
        $menu->analytics_url    = $request->analytics_url;
        $menu->analytics_status = $request->analytics_status;
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
        $menu = Analytics::findOrFail($id)->delete();
        // dd($menus);
        return response()->json([
         'data' => $menu,
         ],Response::HTTP_OK);
    }
}
