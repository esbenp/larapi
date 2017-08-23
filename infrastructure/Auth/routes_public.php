<?php

$router->post('/login', '\Infrastructure\Auth\Controllers\LoginController@login');
$router->post('/login/refresh', '\Infrastructure\Auth\Controllers\LoginController@refresh');