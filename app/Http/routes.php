<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', function () {
    return view('index');
});

Route::group(['prefix' => 'user'], function () {
    Route::get('login', 'Facebook\FacebookController@login');
    Route::get('callback', 'Facebook\FacebookController@callback');
});

Route::group(['prefix' => 'post'], function () {
    Route::get('all', 'Post\PostController@all');
    Route::get('getPosts/{page?}', 'Post\PostController@getPosts');
});