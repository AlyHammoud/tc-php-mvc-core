<?php


namespace Alimvc\PhpMvc;

use Alimvc\PhpMvc\Exceptions\NotFoundException;

/**
 *
 * Class Application
 *
 * @author Ali H <ham***@gmail.com
 * @package Alimvc\PhpMvc
 *
 */

class Router
{
    public Request $request;
    public Response $response;
    protected array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->response = $response;
        $this->request = $request;
    }

    public function get(string $path, callable|string|array $callback) : void
    {
        $this->routes['get']["/zura".$path] = $callback;
    }

    public function post(string $path, callable|string|array $callback) : void
    {
        $this->routes['post']["/zura".$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $callback = $this->routes[$method][$path] ?? false;

        if(!$callback){

            //Application::$app->response->setStatusCode(404);
            //return $this->renderContent("not found"); //renderContent not renderView, pass string directly not a page called not found
//            return "not found";
            //return $this->renderView("error");
            throw new NotFoundException();
        }

        if(is_string($callback)){
            return Application::$app->view->renderView($callback);
        }

        if(is_callable($callback)){
            return call_user_func($callback);
        }

        if (is_array($callback)){
//            //$callback[0] = new $callback[0]; //instance of the router
////            Application::$app->controller = new $callback[0]; //instance of the router, //controller which is defined in db as property, assign it to the new class
////            $callback[0] = Application::$app->controller;
////            Application::$app->controller->actions = $callback[1];
///
                //after milddleware:
            /**
             * @var \Alimvc\PhpMvc\Controller $controller
             * @var \Alimvc\PhpMvc\Middlewares\BaseMiddleware $middleware
            */
            $controller = new $callback[0]; //instance of the router, //controller which is defined in db as property, assign it to the new class
            Application::$app->controller = $controller;
            $controller->actions = $callback[1];
            $callback[0] = $controller;

            foreach ($controller->getMiddlewares() as $middleware){
                $middleware->execute();
            }
        }

        return call_user_func($callback, $this->request, $this->response);

//        if(is_array($callback)){
//            [$class, $method] = $callback;
//            if(class_exists($class)){
////                $class = new $class();              // we are commenting this just because of line 88, otherwise we would remove the second 2 lines here 69 & 70
//                Application::$app->controller = new $class(); //controller which is defined in db as property, assign it to the new class
//                $class = Application::$app->controller;
//                Application::$app->controller->actions = $method;
//
//                if(method_exists($class, $method)){
//                    return call_user_func_array([$class, $method], [$this->request, $this->response]);
//                }
//            }
//        }
   }
}