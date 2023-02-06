<?php

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// authentication
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
});

// Absen
Route::group(['middleware' => ['auth']], function ($router){
    $router->get('/absen', 'AbsentsController@index');
    $router->post('/absen', 'AbsentsController@store');
    $router->get('/absen/{id}', 'AbsentsController@show');
    $router->put('/absen/{id}', 'AbsentsController@update');
    $router->delete('/absen/{id}', 'AbsentsController@destroy');
});
