<?php

namespace App\Core;

class View
{

    public string $title = '';

    public function renderView(string $view, array $params = []): string
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{ content }}', $viewContent, $layoutContent);
    }

    protected function layoutContent() : string // call the layoutcontent in the renderView Function and replace {{ content }} with the view
    {
        $layout = Application::$app->layout;
        if(Application::$app->controller){
            $layout = Application::$app->controller->layout;
        }
        ob_start(); //output caching
        include_once Application::$ROOT_DIR."/Views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function  renderOnlyView($view, $params) : string // render a view without layout
    {
        foreach ($params as $key => $param) {
            $$key = $param;
        }
        ob_start(); //output caching
        include_once Application::$ROOT_DIR."/Views/$view.php";
        return ob_get_clean();
    }

    private function renderContent(string $view) //directly render a string in the content view {{ the string passed not a view }}
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{ content }}', $view, $layoutContent);
    }
}