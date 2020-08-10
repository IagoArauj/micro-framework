<?php

use Core\Routing\Router;

/**
 * In this file you can register all routes for the app.
 * 
 */

Router::get('/', function () {
    echo 'Hello World';
});
