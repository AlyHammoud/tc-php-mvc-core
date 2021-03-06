<?php

namespace Alimvc\PhpMvc\Form;

use Alimvc\PhpMvc\Model;

class InputField extends BaseField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';

    public function __construct(
        public Model $model,
        public string $attribute,
        public string $type = self::TYPE_TEXT
    )
    {
        parent::__construct($model, $attribute);
    }

    public function passwordField(): self
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="form-control%s" id="exampleInputPassword1">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? ' is-invalid' : '');
    }
}