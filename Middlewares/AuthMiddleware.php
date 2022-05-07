<?php

namespace App\Core\Middlewares;

use App\Core\Application;
use App\Core\Exceptions\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{

    public function __construct(public array $actions = []) //$actions are the callbale or the array from the route [App::class, 'login'], login is the action
    {
    }

    /**
     * @throws ForbiddenException
     */

    public function execute()
    {
        if(!Application::isGuest()){
            if(empty($this->actions) || in_array(Application::$app->controller->actions, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}