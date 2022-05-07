<?php

namespace Alimvc\PhpMvc;

class Response
{
    public function setStatusCode(int $code) : int
    {
        return http_response_code($code);
    }

    public function redirect(string $location)
    {
        header("Location: /zura". $location);
    }
}