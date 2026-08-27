<?php

use App\Src\Core\HTTP\Response;
use App\Src\Core\HTTP\Request;

/**
 * TEST endpoint
 * 
 * Healthcheck for API
 */
$router->get('/api/test', function (Request $request) {
    Response::json([ 'message' => 'Too guci too nice' ]);
});