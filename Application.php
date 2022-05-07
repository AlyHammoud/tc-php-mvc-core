<?php

namespace App\Core;

use App\Core\Db\Database;
use App\Core\Db\DbModel;
use App\Models\User;

/**
 *1
 * Class Application
 *
 * @author Ali H <ham***@gmail.com
 * @package App\Core
 *
 */
class Application
{
    public  static string $ROOT_DIR;

    public string $layout = 'main';
    public User $userClass;
    public Request $request;
    public Router $router;
    public Response $response;
    public Session $session;
    public Database $db;
    public static Application $app;
    public ?DbModel $user = null;
    public View $view;
    public ?Controller $controller = null;

    public function __construct($rootPath, array $config)
    {
        $this->userClass = new $config['userClass'];

        static::$ROOT_DIR = $rootPath;
        static::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if($primaryValue){
            $primayKey = $this->userClass->primaryKey();
            $this->user = $this->userClass->findOne([$primayKey => $primaryValue]);
        }else{
            $this->user = null;
        }
    }

    public static function isGuest()
    {
        return self::$app->user ?? null;
    }

    public function  run() : void
    {
        try {
            echo $this->router->resolve();
        }catch (\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('error', ['exception' => $e]);
        }

    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }

    public function login(DbModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }
}