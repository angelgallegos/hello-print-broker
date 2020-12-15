<?php

use Symfony\Component\HttpFoundation\Request;

$app = require __DIR__.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the Application, and send the associated response back to
| the clients browser.
*/

$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();

