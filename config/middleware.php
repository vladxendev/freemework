<?php

use App\Http\Middleware;
use Freemework\Http\Middleware\RouteMiddleware;
use Freemework\Http\Router\Router;
use Freemework\Http\Router\RouteCollection;

/** @var \Framework\Http\Application $app */

$app->middleware->pipe($app->container->get(Middleware\ProfilerMiddleware::class));
$app->middleware->pipe(new RouteMiddleware($app->router, $app->middleware, $app->template));
