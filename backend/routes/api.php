<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');


// JWT 登录
$api->version(['v1', 'v2'], ['namespace' => 'App\Http\Controllers'], function($api) {
    $api->post('/auth/login', 'AuthController@loginPost');
});

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function($api) {
    
    // 被 JWT 保护的 API
    $api->group(['middleware' => 'auth:api'], function ($api) {
        $api->get('hello', 'IndexController@hello');

        $api->get('posts', 'PostController@index');
    });

    

    
    
});




$api->version('v2', ['namespace' => 'App\Http\Controllers\Api\V2'], function($api) {
    //Show all available resources
    $api->get('hello', 'IndexController@hello');
    
});