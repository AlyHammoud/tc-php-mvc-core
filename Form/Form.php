<?php

namespace Alimvc\PhpMvc\Form;

use Alimvc\PhpMvc\Model;

class Form
{
    public static function begin($action, $method)
    {
        echo  sprintf('<form action="%s" method="%s">', $action, $method);
        return new static();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attribute, $type = 'text')
    {
        return new InputField($model, $attribute, $type);
    }
}