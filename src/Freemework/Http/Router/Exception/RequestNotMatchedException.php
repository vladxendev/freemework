<?php
declare(strict_types=1);

namespace Freemework\Http\Router\Exception;

use LogicException;
use Psr\Http\Message\ServerRequestInterface;

class RequestNotMatchedException extends LogicException
{
    private $request;

    public function __construct(ServerRequestInterface $request)
    {
        parent::__construct('Matches not found');
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }
}
