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
    // http://localhost:8000/api/auth/login
    $api->post('/auth/login', 'AuthController@loginPost');
    $api->post('/auth/login/create', 'AuthController@loginPost');
    $api->get('/auth/refresh', 'AuthController@refresh');
});

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1', 'middleware' => 'api.throttle', 'limit' => 5, 'expires' => 1], function($api) {


    // 查
    $api->get('posts', ['as' => 'posts', 'uses' => 'PostController@index']);

    $api->get('posts/{id}', ['as' => 'post', 'uses' => 'PostController@show']);

    $api->get('posts/filter/{id}', ['as' => 'post', 'uses' => 'PostController@byTag']);
    
    // 被 JWT 保护的 API
    $api->group(['middleware' => ['cors', 'auth:api']], function ($api) {

        // http://localhost:8000/api/hello
        $api->get('hello', 'IndexController@hello');

        

        // 增
        $api->post('posts/new', ['as' => 'postCreate', 'uses' => 'PostController@store']);

        // 改
        $api->post('posts/edit/{id}', ['as' => 'postUpdate', 'uses' => 'PostController@update']);



        $api->get('tags', ['as' => 'tags', 'uses' => 'TagController@index']);
        $api->get('tags/{id}', ['as' => 'tag', 'uses' => 'TagController@show']);
    });

    

    
    
});




$api->version('v2', ['namespace' => 'App\Http\Controllers\Api\V2'], function($api) {
    //Show all available resources
    $api->get('hello', 'IndexController@hello');
    
});