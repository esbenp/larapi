<?php

$router->get('/users', 'UserController@getAll');
$router->get('/users/{id}', 'UserController@getById');
$router->post('/users', 'UserController@create');
$router->put('/users/{id}', 'UserController@update');
$router->delete('/users/{id}', 'UserController@delete');
