<?php

$router->get('/users', '\Api\Users\Controllers\UserController@getAll');
$router->get('/users/{id}', '\Api\Users\Controllers\UserController@getById');
$router->post('/users', '\Api\Users\Controllers\UserController@create');
$router->put('/users/{id}', '\Api\Users\Controllers\UserController@update');
$router->delete('/users/{id}', '\Api\Users\Controllers\UserController@delete');
