<?php

namespace Alimvc\PhpMvc;

class Request
{
    public function getPath() :string
    {
//        $path = $_SERVER['REQUEST_URI'] ?? '/';
//        $position = strpos($path, '?');
//
//        if(!$position){
//            return $position;
//        }
//
//        return substr($path, 0, $position);

        $path = $_SERVER['REQUEST_URI'] ?? '/';
        return explode("?","$path")[0];
    }

    public  function  method() : string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public  function  isGet() : string
    {
        return $this->method() === 'get';
    }

    public  function  isPost() : string
    {
        return $this->method() === 'post';
    }

    public function getBody() : array
    {
        $body = [];

        if($this->method() === 'get'){
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }

        if($this->method() === 'post'){
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }
        }
        return $body;
    }
}