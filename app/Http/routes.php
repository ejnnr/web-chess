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
Route::get('/', 'MainController@index');

Route::group([
	'prefix' => 'api',
	'middleware' => ['jsonApi']
], function()
{
	Route::resource('tags', 'TagController', ['except' => ['create', 'edit']]);
	Route::resource('users', 'UserController', ['except' => ['create', 'edit']]);
	Route::resource('games', 'GameController', ['except' => ['create', 'edit']]);
});
