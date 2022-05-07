<?php

namespace Alimvc\PhpMvc;

use Alimvc\PhpMvc\Middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'main';
    public string $actions = '';
    /*
     * @var Alimvc\PhpMvc\Middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    public function render(string $view, array $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}