<?php

namespace Alimvc\PhpMvc\Exceptions;

class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access thi page';
    protected $code = '403';
}