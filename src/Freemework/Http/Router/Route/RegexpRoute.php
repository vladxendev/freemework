<?php
declare(strict_types=1);

namespace Freemework\Http\Router\Route;

use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Psr\Http\Server\RequestHandlerInterface;
use Freemework\Http\Router\Route\Route;
use Freemework\Http\Router\RequestHandleResolver;
use Freemework\Http\Router\Exception\RouteNotFoundException;
use InvalidArgumentException;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ResponseFactory;

use function in_array;
use function is_string;
use function preg_match;
use function preg_replace_callback;
use function array_filter;
use function key_exists;

class RegexpRoute implements Route
{
    protected $name;
    protected $pattern;
    protected $handler;
    protected $methods;
    protected array $tokens = [];
    protected array $defaults = [];
    protected array $attributes;

	/**
	 * @var array $middleware MiddlewareInterface[]
	 */
    private array $middleware = [];
    
    public function __construct($name, $pattern, $handler, array $methods)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->methods = $methods;
    }

    public function match(ServerRequestInterface $request): ?Route
    {
        if ($this->methods && !in_array($request->getMethod(), $this->methods, true)) {
            return null;
        }

        $pattern = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) {
            $argument = $matches[1];
            $replace = $this->tokens[$argument] ?? '[^}]+';
            return '(?P<' . $argument . '>' . $replace . ')';
        }, $this->pattern);

        $path = $request->getUri()->getPath();

        if (preg_match('~^' . $pattern . '$~i', $path, $matches)) {
            $this->attributes = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $this;
        }

        return null;
    }

    public function generate($name, array $params = []): ?string
    {
        $arguments = array_filter($params);
        
        if ($name !== $this->name) {
            return null;
        }

        $url = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use (&$arguments) {
            $argument = $matches[1];
            
            if (!key_exists($argument, $arguments)) {
                throw new InvalidArgumentException('Missing parameter "' . $argument . '"');
            }

            return $arguments[$argument];
        }, $this->pattern);

        return $url;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $resolver = new RequestHandleResolver($request);
        $resolve = $resolver->resolve($this->handler);
        return $resolve($request);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function tokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    public function defaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    public function middleware(array $middleware)
    {
        $this->middleware = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middleware;
    }
}
