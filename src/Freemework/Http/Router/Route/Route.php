<?php
declare(strict_types=1);

namespace Freemework\Http\Router\Route;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Freemework\Http\Router\RequestHandleResolver;

interface Route extends RequestHandlerInterface
{
    public function match(ServerRequestInterface $request): ?Route;

    public function generate($name, array $params = []): ?string;

    public function getName(): string;

    public function getPattern(): string;

    public function getHandler();

    public function getAttributes(): array;

    public function tokens(array $tokens);

    public function defaults(array $defaults);

    public function middleware(array $middleware);

    public function getMiddlewares(): array;
}
