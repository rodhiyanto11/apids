<?php
date_default_timezone_set('Asia/Jakarta');
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', 'Api\AuthController@login');
Route::get('getcompanies', 'Api\CompaniesController@index');
Route::post('refresh', 'Api\AuthController@refreshtoken');


//Route::group(['prefix' => 'admin',  'middleware' => 'jwt.verify'], function(){
    //===admin===\\
    Route::resource('users', 'Api\UserController')->middleware('jwt.verify');
    Route::resource('menus', 'Api\MenusController')->middleware('jwt.verify');
    Route::resource('menutarget', 'Api\Menu_TargetController')->middleware('jwt.verify');
    Route::resource('companies', 'Api\CompaniesController')->middleware('jwt.verify');
    Route::resource('divisions', 'Api\DivisionsController')->middleware('jwt.verify');
    Route::resource('departments', 'Api\DepartmentsController')->middleware('jwt.verify');
    Route::resource('analytics', 'Api\AnalyticsController')->middleware('jwt.verify');
//});


