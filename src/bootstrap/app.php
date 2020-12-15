<?php
use Framework\Bootstrap\LoadEnvironment;
use App\Application;

require_once __DIR__.'/../../vendor/autoload.php';

$dotenv = new LoadEnvironment(
    dirname(__DIR__),
    ['.version']
);
$dotenv->bootstrap();

return new Application();