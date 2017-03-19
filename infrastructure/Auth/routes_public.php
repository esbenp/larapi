<?php

$router->post('/login', 'LoginController@login');
$router->post('/login/refresh', 'LoginController@refresh');