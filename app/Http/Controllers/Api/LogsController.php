<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stevebauman\Location\Location;
use JWTAuth;
use App\Logs;
use Symfony\Component\HttpFoundation\Response;
class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


       // dd($response = json_decode(file_get_contents($this->server->get('REMOTE_ADDR')), true));
    }
    public function getLocation(){
        $ip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));

       $data = \Location::get($ip);
       return $data;
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
    public function logscontroll($data = array()){
        $auth           = JWTAuth::parseToken()->authenticate();

       //   dd($data['detail']);
       //'id','user_id','ip_address','latitude','longitude','country','region','menu_id','action','desc'
       $log = new Logs;
       $log->user_id = $auth->id;
       $log->user_name = $auth->username;
       $log->ip_address         = $data['location']->ip;
       $log->latitude           = $data['location']->latitude;
       $log->longitude          = $data['location']->longitude;
       $log->country            = '('.$data['location']->countryCode.')'.$data['location']->countryName;
       $log->region             = '('.$data['location']->regionCode.')'.$data['location']->regionName;
       $log->menu_id            = $data['detail']->id;
       $log->menu_name            = $data['detail']->analytics_name;
       $log->action             = 'view';
       $log->save();

      // dd($log);
    }
}
