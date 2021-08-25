<?php
declare(strict_types=1);

namespace App\Http\Action;

use Psr\Http\Message\{ServerRequestInterface, ResponseInterface};
use Laminas\Diactoros\Response\HtmlResponse;
use Freemework\Template\TemplateRenderer;

class HomeAction
{
    public TemplateRenderer $view;
    
    public function __construct(TemplateRenderer $view)
    {
        $this->view = $view;
    }
    
    public function home(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->view->render('index.html', ['title' => 'Down']), 200);
    }
}
