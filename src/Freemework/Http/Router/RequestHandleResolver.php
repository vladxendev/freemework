<?php
declare(strict_types=1);

namespace Freemework\Http\Router;

use Closure;
use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Freemework\Http\Router\Exception\{RequestNotMatchedException, RouteNotFoundException};

use function is_string;
use function is_array;
use function is_callable;
use function is_object;
use function is_null;
use function method_exists;
use function array_key_first;
use function array_key_last;

class RequestHandleResolver
{
    public $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }
    
    public function resolve($handler): callable
    {
        if (is_string($handler) && method_exists($handler, '__invoke')) {
            return new $handler();
        } elseif ($handler instanceof Closure && method_exists($handler, '__invoke')) {
            return $handler;
        } elseif (!is_array($handler) && is_callable($handler)) {
            return $handler;
        } elseif (is_array($handler) && !is_null(array_key_first($handler)) && !is_null(array_key_last($handler))) {
            $firstKey = array_key_first($handler);
            $lastKey = array_key_last($handler);
            $handle = null;

            if (is_string($handler[$firstKey]) && method_exists($handler[$firstKey], $handler[$lastKey])) {
                $handle = function ($request) use ($handler, $firstKey, $lastKey) {
                    return (new $handler[$firstKey])->{$handler[$lastKey]}($request);
                };
            }

            if (is_object($handler[$firstKey]) && method_exists($handler[$firstKey], $handler[$lastKey])) {
                $handle = function ($request) use ($handler, $firstKey, $lastKey) {
                    return $handler[$firstKey]->{$handler[$lastKey]}($request);
                };
            }

            if (!empty($handle)) {
                return $handle;
            } else {
                throw new RequestNotMatchedException($this->request);
            }
        } else {
            throw new RequestNotMatchedException($this->request);
        }
    }
}
