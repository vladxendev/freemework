<?php

use Laminas\Diactoros\{ServerRequestFactory, ResponseFactory, Response};
use Laminas\HttpHandlerRunner\Emitter\{EmitterStack, SapiEmitter};
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Laminas\Stratigility\MiddlewarePipe;

// Debug mode functions
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

// System directories
define ('ROOT_DIR', dirname(__DIR__));
define ('SRC_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'src');
define ('APP_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'App');
define ('TPL_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'templates');

if (file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    require ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} else {
    throw new DomainException('Composer: Autoload file vendor/autoload.php not exist.');
}

$request = ServerRequestFactory::fromGlobals();
$pipeline = new MiddlewarePipe();
$response = new Response();

$runner = new RequestHandlerRunner(
    $pipeline,
    new SapiEmitter(),
    $request,
    $response
);
$runner->run();
